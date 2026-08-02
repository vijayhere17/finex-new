<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserStaked;
use App\Services\Blockchain\BlockchainService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ROI Withdrawal Unlock Rule
 *
 * requiredReferrals = floor(ROI Generated / Package Amount)
 * withdrawable ROI  = min(ROI Generated, qualifyingDirects * Package Amount)
 *
 * Qualifying direct = activated referral whose package >= this investment package.
 * If enough directs already exist, future ROI unlocks automatically.
 */
class RoiUnlockService
{
    public function requiredReferrals(float $roiGenerated, float $packageAmount): int
    {
        if ($packageAmount <= 0 || $roiGenerated <= 0) {
            return 0;
        }

        return (int) floor($roiGenerated / $packageAmount);
    }

    public function computeUnlockedRoi(float $roiGenerated, float $packageAmount, int $qualifyingDirects): float
    {
        if ($roiGenerated <= 0 || $packageAmount <= 0) {
            return 0.0;
        }

        $cap = $qualifyingDirects * $packageAmount;

        return (float) min($roiGenerated, $cap);
    }

    /**
     * Count activated directs of $sponsor whose self package (paid_amount max / self_investment slot)
     * is >= $packageAmount. Uses each direct's highest active/closed stake amount.
     */
    public function countQualifyingDirects(int $sponsorId, float $packageAmount, ?int $beforeStakeId = null): int
    {
        $directIds = User::where('referral_id', $sponsorId)
            ->where('activation_status', DirectRoiService::STATUS_ACTIVE)
            ->pluck('id')
            ->all();

        if (empty($directIds)) {
            return 0;
        }

        $count = 0;
        foreach ($directIds as $directId) {
            $q = UserStaked::where('member_id', $directId);
            // Prefer max paid_amount across their stakes
            $maxPackage = (float) $q->max('paid_amount');
            if ($maxPackage + 0.00001 >= $packageAmount) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Refresh unlock state for one stake. Credits newly unlocked ROI delta to earning wallet.
     * Returns ['unlocked' => float, 'credited' => float, 'qualifying' => int].
     */
    public function refreshStake(UserStaked $stake, bool $creditWallet = true): array
    {
        $package = (float) $stake->paid_amount;
        $generated = (float) $stake->total_roi_paid;
        $qualifying = $this->countQualifyingDirects((int) $stake->member_id, $package);
        $unlocked = $this->computeUnlockedRoi($generated, $package, $qualifying);
        $previous = (float) ($stake->unlocked_roi ?? 0);
        $delta = round($unlocked - $previous, 4);

        if ($delta < 0) {
            $delta = 0;
        }

        DB::transaction(function () use ($stake, $unlocked, $qualifying, $delta, $creditWallet) {
            $locked = UserStaked::where('id', $stake->id)->lockForUpdate()->first();
            if ($locked == null) {
                return;
            }

            $prev = (float) ($locked->unlocked_roi ?? 0);
            $newUnlocked = $unlocked;
            // Recompute inside lock with stored qualifying
            $locked->qualifying_directs = $qualifying;
            $locked->unlocked_roi = $newUnlocked;

            $creditDelta = round($newUnlocked - $prev, 4);
            if ($creditDelta < 0) {
                $creditDelta = 0;
            }

            $locked->save();

            if ($creditWallet && $creditDelta > 0) {
                $walletCon = app('App\Http\Controllers\Users\EarningWalletController');
                $earningType = (int) config('income.daily_roi.earning_type', 2);
                $tag = '[UNLOCK|S'.$locked->id.'|'.$newUnlocked.']';
                $exists = \App\Models\EarningWallet::where('member_id', $locked->member_id)
                    ->where('earning_type', $earningType)
                    ->where('description', 'like', '%'.$tag.'%')
                    ->exists();

                if (!$exists) {
                    $desc = 'Unlocked Daily ROI $'.number_format($creditDelta, 4).' (qualifying directs '.$qualifying.') '.$tag;
                    $walletCon->addearningwalletlog(
                        $locked->member_id,
                        1,
                        $earningType,
                        $desc,
                        $creditDelta,
                        0,
                        0,
                        date('Y-m-d H:i:s')
                    );
                }
            }
        });

        $stake->refresh();

        // Sync unlock to vault when investment is on-chain
        try {
            if ($stake->onchain_investment_id) {
                app(BlockchainService::class)->syncRoi($stake);
            }
        } catch (\Throwable $e) {
            Log::warning('RoiUnlock on-chain sync: '.$e->getMessage());
        }

        return [
            'unlocked' => (float) $stake->unlocked_roi,
            'credited' => $delta,
            'qualifying' => $qualifying,
            'required' => $this->requiredReferrals($generated, $package),
            'generated' => $generated,
        ];
    }

    /**
     * After a new direct activates, refresh all of sponsor's open stakes for unlock.
     */
    public function onDirectActivated(User $sponsor, User $direct, float $directPackageAmount): void
    {
        $stakes = UserStaked::where('member_id', $sponsor->id)
            ->where('is_deleted', 0)
            ->orderBy('id')
            ->get();

        foreach ($stakes as $stake) {
            if ($directPackageAmount + 0.00001 < (float) $stake->paid_amount) {
                continue;
            }
            $this->refreshStake($stake, true);
        }

        // Also refresh completed stakes that still have locked ROI uncredited
        if (\Illuminate\Support\Facades\Schema::hasColumn('staked_users', 'unlocked_roi')) {
            $closed = UserStaked::where('member_id', $sponsor->id)
                ->where('is_deleted', 1)
                ->whereColumn('unlocked_roi', '<', 'total_roi_paid')
                ->orderBy('id')
                ->get();

            foreach ($closed as $stake) {
                if ($directPackageAmount + 0.00001 < (float) $stake->paid_amount) {
                    continue;
                }
                $this->refreshStake($stake, true);
            }
        }
    }

    /**
     * Total withdrawable ROI across member stakes (unlocked - withdrawn_roi tracking).
     * Non-ROI incomes remain fully withdrawable via earning wallet; this gates Daily ROI.
     */
    public function withdrawableRoi(int $memberId): float
    {
        $stakes = UserStaked::where('member_id', $memberId)->get();
        $total = 0.0;
        foreach ($stakes as $stake) {
            $open = (float) ($stake->unlocked_roi ?? 0) - (float) ($stake->withdrawn_roi ?? 0);
            if ($open > 0) {
                $total += $open;
            }
        }

        return round($total, 4);
    }

    /**
     * Max amount user may withdraw now =
     *   (earning wallet balance - locked ROI still in wallet)
     * Locked ROI in wallet ≈ total ROI credited historically that is not yet unlocked.
     *
     * Practical approach used here:
     * available = earning_balance - max(0, total_roi_paid_sum - unlocked_roi_sum)
     * i.e. subtract still-locked ROI from wallet.
     */
    public function availableWithdrawalAmount(int $memberId, float $earningBalance): float
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasColumn('staked_users', 'unlocked_roi')) {
                return max(0, round($earningBalance, 4));
            }

            $generated = (float) UserStaked::where('member_id', $memberId)->sum('total_roi_paid');
            $unlocked = (float) UserStaked::where('member_id', $memberId)->sum('unlocked_roi');
            $lockedRoi = max(0, round($generated - $unlocked, 4));

            // If Daily ROI is only credited on unlock, lockedRoi in wallet should be ~0.
            $available = round($earningBalance - $lockedRoi, 4);

            return max(0, $available);
        } catch (\Throwable $e) {
            return max(0, round($earningBalance, 4));
        }
    }
}
