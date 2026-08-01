<?php

namespace App\Services;

use App\Models\DailyRoiLog;
use App\Models\User;
use App\Models\UserStaked;
use App\Models\EarningWallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Daily ROI distribution for active slots.
 * Rate = user's live Direct ROI % (direct_roi_percent).
 * Cap = package maximum_income OR max_roi_days (default 300).
 * Duplicate-safe via daily_roi_logs unique(stake_id, roi_date) + wallet tag.
 */
class DailyRoiService
{
    public function __construct(
        protected DirectRoiService $directRoi,
        protected LevelRoiService $levelRoi,
        protected SpilloverService $spillover
    ) {}

    /**
     * Run once per calendar day (cron). Returns summary counts.
     */
    public function distribute(?string $roiDate = null): array
    {
        $roiDate = $roiDate ?: date('Y-m-d');
        $paid = 0;
        $skipped = 0;
        $closed = 0;
        $levelPaid = 0;

        $stakes = UserStaked::where('is_deleted', 0)
            ->where('topup_type', '!=', 1)
            ->orderBy('id')
            ->get();

        foreach ($stakes as $stake) {
            try {
                $result = $this->payStake($stake, $roiDate);
                if ($result['status'] === 'paid') {
                    $paid++;
                    $levelPaid += (int) ($result['level_paid'] ?? 0);
                } elseif ($result['status'] === 'closed') {
                    $closed++;
                } else {
                    $skipped++;
                }
            } catch (\Throwable $e) {
                Log::error('DailyRoiService stake '.$stake->id.': '.$e->getMessage());
                $skipped++;
            }
        }

        return compact('paid', 'skipped', 'closed', 'levelPaid', 'roiDate');
    }

    /**
     * Pay one stake for a given date.
     * @return array{status:string,level_paid?:int}
     */
    public function payStake(UserStaked $stake, string $roiDate): array
    {
        // Duplicate prevention (same stake + same calendar/fake date)
        if (DailyRoiLog::where('stake_id', $stake->id)->where('roi_date', $roiDate)->exists()) {
            return ['status' => 'skipped'];
        }

        $member = User::find($stake->member_id);
        if ($member == null || ($member->activation_status ?? '') !== DirectRoiService::STATUS_ACTIVE) {
            return ['status' => 'skipped'];
        }

        // Refresh live Direct ROI % before paying
        $stats = $this->directRoi->refresh((int) $member->id);
        $percent = (float) $stats['direct_roi_percent'];

        if ($percent <= 0) {
            return ['status' => 'skipped'];
        }

        $maxDays = (int) ($stake->max_roi_days ?: config('income.daily_roi.max_days', 300));
        $daysPaid = (int) ($stake->roi_days_paid ?? 0);

        if ($daysPaid >= $maxDays) {
            $this->closeStake($stake, 'max days reached');
            return ['status' => 'closed'];
        }

        $remainingCap = (float) $stake->maximum_income - (float) $stake->total_roi_paid;
        if ($remainingCap <= 0) {
            $this->closeStake($stake, 'earning limit reached');
            return ['status' => 'closed'];
        }

        $commission = ((float) $stake->paid_amount * $percent) / 100.0;
        if ($commission > $remainingCap) {
            $commission = $remainingCap;
        }

        if ($commission <= 0) {
            return ['status' => 'skipped'];
        }

        $walletCon = app('App\Http\Controllers\Users\EarningWalletController');
        $earningType = (int) config('income.daily_roi.earning_type', 2);
        $levelPaid = 0;
        $finalStatus = 'paid';

        try {
            DB::transaction(function () use (
                $stake, $member, $percent, $commission, $roiDate, $daysPaid,
                $walletCon, $earningType, $maxDays, &$levelPaid, &$finalStatus
            ) {
                // Lock stake row to block parallel double-pay
                $locked = UserStaked::where('id', $stake->id)->lockForUpdate()->first();
                if ($locked == null) {
                    $finalStatus = 'skipped';
                    return;
                }

                if (DailyRoiLog::where('stake_id', $locked->id)->where('roi_date', $roiDate)->exists()) {
                    $finalStatus = 'skipped';
                    return;
                }

                $dayNumber = ((int) ($locked->roi_days_paid ?? 0)) + 1;
                $tag = '[S'.$locked->id.'|'.$roiDate.']';

                // Extra wallet-level duplicate guard (covers missing unique index)
                $walletDup = EarningWallet::where('member_id', $member->id)
                    ->where('earning_type', $earningType)
                    ->where('description', 'like', '%'.$tag.'%')
                    ->exists();

                if ($walletDup) {
                    $finalStatus = 'skipped';
                    return;
                }

                $log = DailyRoiLog::create([
                    'member_id' => $member->id,
                    'stake_id' => $locked->id,
                    'stake_amount' => $locked->paid_amount,
                    'roi_percent' => $percent,
                    'amount' => $commission,
                    'roi_date' => $roiDate,
                    'day_number' => $dayNumber,
                ]);

                $description = 'Daily ROI '.$percent.'% on Slot $'.$locked->paid_amount.' (Day '.$dayNumber.') '.$tag;
                $walletCon->addearningwalletlog(
                    $member->id,
                    1,
                    $earningType,
                    $description,
                    $commission,
                    0,
                    0,
                    $roiDate.' '.date('H:i:s')
                );

                $locked->receive_return = ((float) $locked->receive_return) + $commission;
                $locked->total_roi_paid = ((float) $locked->total_roi_paid) + $commission;
                $locked->roi_days_paid = $dayNumber;

                $hitCap = $locked->total_roi_paid >= (float) $locked->maximum_income;
                $hitDays = $dayNumber >= $maxDays;

                if ($hitCap || $hitDays) {
                    $locked->is_deleted = 1;
                }

                $locked->save();

                // Level ROI Income to uplines (sponsor sees it on Level ROI page)
                $levelPaid = $this->levelRoi->distributeFromDailyRoi($log);

                if ((int) $locked->is_deleted === 1) {
                    $this->spillover->onEarningCycleComplete($member, $locked);
                    $finalStatus = 'closed';
                }
            });
        } catch (\Throwable $e) {
            // Unique constraint race → treat as skip (no duplicate credit)
            Log::warning('DailyRoiService duplicate/error stake '.$stake->id.': '.$e->getMessage());
            return ['status' => 'skipped'];
        }

        return ['status' => $finalStatus, 'level_paid' => $levelPaid];
    }

    protected function closeStake(UserStaked $stake, string $reason): void
    {
        $stake->is_deleted = 1;
        $stake->save();

        $member = User::find($stake->member_id);
        if ($member) {
            $this->spillover->onEarningCycleComplete($member, $stake, $reason);
        }
    }
}
