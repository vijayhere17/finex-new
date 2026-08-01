<?php

namespace App\Services;

use App\Models\AutoUpgradeLog;
use App\Models\StakeMaster;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Auto Slot Upgrade — income from 2nd & 3rd activated directs accumulates
 * toward the next unpurchased slot; activates when balance covers the slot price.
 */
class AutoUpgradeService
{
    public function __construct(protected DirectRoiService $directRoi)
    {
    }

    /**
     * Rank of $fromId among sponsor's activated directs (1-based by activation_date, id).
     */
    public function directActivationPosition(int $sponsorId, int $fromId): int
    {
        $directs = User::where('referral_id', $sponsorId)
            ->where('activation_status', DirectRoiService::STATUS_ACTIVE)
            ->orderBy('activation_date')
            ->orderBy('id')
            ->pluck('id')
            ->values()
            ->all();

        $index = array_search($fromId, $directs, true);
        return $index === false ? 0 : ($index + 1);
    }

    /**
     * Credit amount into auto-upgrade wallet if from 2nd/3rd direct and next slot unpaid.
     * Returns true if diverted (caller should NOT also credit main wallet).
     */
    public function maybeDivertToAutoUpgrade(User $sponsor, int $fromId, float $amount, string $description): bool
    {
        if ($amount <= 0) {
            return false;
        }

        $positions = config('income.auto_upgrade.source_direct_positions', [2, 3]);
        $position = $this->directActivationPosition((int) $sponsor->id, $fromId);

        if (!in_array($position, $positions, true)) {
            return false;
        }

        $amounts = config('income.slot_amounts', []);
        $nextSlot = (int) ($sponsor->next_slot ?? 0);
        if ($nextSlot <= 0 || $nextSlot > count($amounts)) {
            // Sync from progress if stale
            $progress = $this->resolveSlotProgress($sponsor);
            $nextSlot = $progress['next_slot'];
            if ($nextSlot <= 0) {
                return false;
            }
        }

        DB::transaction(function () use ($sponsor, $fromId, $amount, $description, $nextSlot, $position) {
            $user = User::where('id', $sponsor->id)->lockForUpdate()->first();
            $user->auto_upgrade_balance = ((float) $user->auto_upgrade_balance) + $amount;
            $user->save();

            AutoUpgradeLog::create([
                'member_id' => $user->id,
                'event_type' => 'credit',
                'slot_number' => $nextSlot,
                'amount' => $amount,
                'balance_after' => $user->auto_upgrade_balance,
                'from_id' => $fromId,
                'description' => 'Auto-upgrade credit from Direct #'.$position.': '.$description,
            ]);

            $this->tryActivateNextSlot($user);
        });

        return true;
    }

    /**
     * Finex path (no Direct Sponsor wallet income): credit auto-upgrade pot from
     * 2nd/3rd activated direct using % of the activated slot amount.
     */
    public function creditFromDirectActivation(User $sponsor, int $fromId, float $slotAmount): bool
    {
        if ($slotAmount <= 0) {
            return false;
        }

        $position = $this->directActivationPosition((int) $sponsor->id, $fromId);
        $percents = config('income.auto_upgrade.divert_percent_of_slot', [2 => 2, 3 => 1]);
        $percent = (float) ($percents[$position] ?? 0);

        if ($percent <= 0) {
            return false;
        }

        $amount = ($slotAmount * $percent) / 100.0;
        $description = 'Slot $'.$slotAmount.' activation (Direct #'.$position.')';

        return $this->maybeDivertToAutoUpgrade($sponsor, $fromId, $amount, $description);
    }

    protected static $activating = false;

    /**
     * If balance covers next slot price, auto-activate and pay upline upgrade income.
     */
    public function tryActivateNextSlot(User $user): bool
    {
        if (self::$activating) {
            return false;
        }

        $amounts = config('income.slot_amounts', []);
        $progress = $this->resolveSlotProgress($user);
        $nextSlot = $progress['next_slot'];

        if ($nextSlot <= 0 || $nextSlot > count($amounts)) {
            return false;
        }

        $price = (float) $amounts[$nextSlot - 1];
        $balance = (float) $user->auto_upgrade_balance;

        if ($balance < $price) {
            return false;
        }

        $pkg = StakeMaster::where('ptype', 2)
            ->where('is_admin', 0)
            ->where('is_travel', 0)
            ->where('amount', $price)
            ->first();

        if ($pkg == null) {
            $packages = StakeMaster::where('ptype', 2)->where('is_admin', 0)->where('is_travel', 0)->orderBy('amount')->get();
            $pkg = $packages->get($nextSlot - 1);
        }

        if ($pkg == null) {
            Log::warning('AutoUpgrade: no package for slot '.$nextSlot);
            return false;
        }

        self::$activating = true;

        try {
            DB::transaction(function () use ($user, $price, $pkg, $nextSlot) {
                $locked = User::where('id', $user->id)->lockForUpdate()->first();
                if ((float) $locked->auto_upgrade_balance < $price) {
                    return;
                }

                $locked->auto_upgrade_balance = ((float) $locked->auto_upgrade_balance) - $price;
                $locked->current_slot = $nextSlot;
                $locked->next_slot = ($nextSlot < 12) ? ($nextSlot + 1) : 0;
                $locked->save();

                AutoUpgradeLog::create([
                    'member_id' => $locked->id,
                    'event_type' => 'activate',
                    'slot_number' => $nextSlot,
                    'amount' => $price,
                    'balance_after' => $locked->auto_upgrade_balance,
                    'description' => 'Auto-activated Slot '.$nextSlot.' ($'.$price.')',
                ]);

                $stakeCon = app('App\Http\Controllers\Users\StakeController');
                $stakeCon->setStakeActivation($locked->id, $pkg->id, $price, 0);

                $this->payUplineUpgradeIncome($locked, $nextSlot, $price);
            });
        } finally {
            self::$activating = false;
        }

        return true;
    }

    protected function payUplineUpgradeIncome(User $member, int $slotNumber, float $slotAmount): void
    {
        $ladder = config('income.auto_upgrade.upline_ladder', []);
        $earningType = (int) config('income.auto_upgrade.income_earning_type', 11);
        $walletCon = app('App\Http\Controllers\Users\EarningWalletController');

        $uplineId = (int) $member->referral_id;
        $level = 1;

        while ($uplineId > 0 && $level <= count($ladder)) {
            $upline = User::find($uplineId);
            if ($upline == null) {
                break;
            }

            $percent = (float) ($ladder[$level] ?? 0);
            if ($percent > 0 && ($upline->activation_status ?? '') === DirectRoiService::STATUS_ACTIVE) {
                $commission = ($slotAmount * $percent) / 100.0;
                if ($commission > 0) {
                    $desc = 'Auto Upgrade Income L'.$level.' from Slot '.$slotNumber.' of '.obscureAddress($member->username);
                    $walletCon->addearningwalletlog($upline->id, 1, $earningType, $desc, $commission, 0, 0, date('Y-m-d H:i:s'));

                    AutoUpgradeLog::create([
                        'member_id' => $upline->id,
                        'event_type' => 'upline_income',
                        'slot_number' => $slotNumber,
                        'amount' => $commission,
                        'balance_after' => (float) $upline->auto_upgrade_balance,
                        'from_id' => $member->id,
                        'description' => $desc,
                    ]);
                }
            }

            $uplineId = (int) $upline->referral_id;
            $level++;
        }
    }

    public function resolveSlotProgress(User $user): array
    {
        $amounts = config('income.slot_amounts', []);
        $purchased = \App\Models\UserStaked::where('member_id', $user->id)
            ->pluck('paid_amount')
            ->map(fn ($a) => (float) $a)
            ->unique()
            ->all();

        $current = 0;
        foreach ($amounts as $i => $amt) {
            if (in_array((float) $amt, $purchased, true)) {
                $current = $i + 1;
            } else {
                break;
            }
        }

        $next = ($current < count($amounts)) ? ($current + 1) : 0;

        return ['current_slot' => $current, 'next_slot' => $next];
    }
}
