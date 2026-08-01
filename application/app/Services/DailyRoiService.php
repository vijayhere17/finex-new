<?php

namespace App\Services;

use App\Models\DailyRoiLog;
use App\Models\User;
use App\Models\UserStaked;
use App\Services\Blockchain\BlockchainService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Daily ROI distribution for active slots.
 * Rate = user's live Direct ROI % (direct_roi_percent).
 * Cap = package maximum_income OR max_roi_days (default 300).
 * Duplicate-safe via daily_roi_logs unique(stake_id, roi_date) + wallet tag.
 *
 * Wallet credit for Daily ROI is gated by RoiUnlockService
 * (qualifying same-or-greater package directs). Level ROI still pays uplines
 * when Daily ROI is generated.
 */
class DailyRoiService
{
    public function __construct(
        protected DirectRoiService $directRoi,
        protected LevelRoiService $levelRoi,
        protected SpilloverService $spillover,
        protected RoiUnlockService $roiUnlock
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

        $levelPaid = 0;
        $finalStatus = 'paid';
        $dailyLog = null;
        $lockedStakeId = null;

        try {
            DB::transaction(function () use (
                $stake, $member, $percent, $commission, $roiDate,
                $maxDays, &$levelPaid, &$finalStatus, &$dailyLog, &$lockedStakeId
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

                $log = DailyRoiLog::create([
                    'member_id' => $member->id,
                    'stake_id' => $locked->id,
                    'stake_amount' => $locked->paid_amount,
                    'roi_percent' => $percent,
                    'amount' => $commission,
                    'roi_date' => $roiDate,
                    'day_number' => $dayNumber,
                ]);

                // Daily ROI is generated here but wallet credit is applied by
                // RoiUnlockService only when qualifying directs unlock it.

                $locked->receive_return = ((float) $locked->receive_return) + $commission;
                $locked->total_roi_paid = ((float) $locked->total_roi_paid) + $commission;
                $locked->roi_days_paid = $dayNumber;

                $hitCap = $locked->total_roi_paid >= (float) $locked->maximum_income;
                $hitDays = $dayNumber >= $maxDays;

                if ($hitCap || $hitDays) {
                    $locked->is_deleted = 1;
                }

                $locked->save();
                $lockedStakeId = $locked->id;
                $dailyLog = $log;

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

        // Unlock + credit Daily ROI if qualifying directs already satisfy the rule.
        if ($lockedStakeId && $finalStatus !== 'skipped') {
            $fresh = UserStaked::find($lockedStakeId);
            if ($fresh) {
                $this->roiUnlock->refreshStake($fresh, true);
            }

            // Sync Level ROI incomes to vault withdrawable balance (immediate)
            if ($levelPaid > 0 && $dailyLog) {
                try {
                    $chain = app(BlockchainService::class);
                    if ($chain->enabled()) {
                        $levelLogs = \App\Models\LevelRoiLog::where('daily_roi_log_id', $dailyLog->id)->get();
                        foreach ($levelLogs as $ll) {
                            $upline = User::find($ll->member_id);
                            if ($upline) {
                                $chain->syncIncomeOnChain($upline, (float) $ll->amount, (int) config('income.level_roi.earning_type', 4));
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning('Level ROI chain sync: '.$e->getMessage());
                }
            }
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
