<?php

namespace App\Services;

use App\Models\DailyRoiLog;
use App\Models\LevelRoiLog;
use App\Models\User;
use App\Models\EarningWallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Level Income on ROI — paid from downline Daily ROI only.
 * Unlock: N qualified active directs unlocks Level N (max 12).
 * Ladder: L1=12% … L12=1% of the Daily ROI amount.
 *
 * Shown on the UPLINE's Level ROI page (member_id = receiver), not on the
 * downline who earned the Daily ROI.
 */
class LevelRoiService
{
    public function __construct(
        protected DirectRoiService $directRoi
    ) {}

    public function distributeFromDailyRoi(DailyRoiLog $dailyLog): int
    {
        $paidLevels = 0;

        $from = User::find($dailyLog->member_id);
        if ($from == null || (int) $from->referral_id <= 0) {
            return 0;
        }

        $baseAmount = (float) $dailyLog->amount;
        if ($baseAmount <= 0) {
            return 0;
        }

        $ladder = config('income.level_roi.ladder', []);
        $maxDepth = (int) config('income.level_roi.max_depth', 12);
        $earningType = (int) config('income.level_roi.earning_type', 4);
        $roiDate = $dailyLog->roi_date;
        $walletCon = app('App\Http\Controllers\Users\EarningWalletController');

        $uplineId = (int) $from->referral_id;
        $level = 1;

        while ($uplineId > 0 && $level <= $maxDepth) {
            $upline = User::find($uplineId);
            if ($upline == null) {
                break;
            }

            $percent = (float) ($ladder[$level] ?? 0);

            // Always refresh live qualified directs before unlock check
            $stats = $this->directRoi->refresh((int) $upline->id);
            $qualified = (int) $stats['qualified_active_directs'];

            // Unlock rule: N directs unlock Level N
            if ($percent > 0
                && ($upline->activation_status ?? '') === DirectRoiService::STATUS_ACTIVE
                && $qualified >= $level
            ) {
                $commission = ($baseAmount * $percent) / 100.0;

                if ($commission > 0) {
                    // Duplicate prevention (log + wallet)
                    $exists = LevelRoiLog::where('member_id', $upline->id)
                        ->where('from_id', $from->id)
                        ->where('level', $level)
                        ->where('roi_date', $roiDate)
                        ->where('daily_roi_log_id', $dailyLog->id)
                        ->exists();

                    $walletDup = EarningWallet::where('member_id', $upline->id)
                        ->where('earning_type', $earningType)
                        ->where('description', 'like', '%[D'.$dailyLog->id.'|L'.$level.']%')
                        ->exists();

                    if (!$exists && !$walletDup) {
                        try {
                            LevelRoiLog::create([
                                'member_id' => $upline->id,
                                'from_id' => $from->id,
                                'daily_roi_log_id' => $dailyLog->id,
                                'level' => $level,
                                'percent' => $percent,
                                'base_amount' => $baseAmount,
                                'amount' => $commission,
                                'roi_date' => $roiDate,
                            ]);

                            $desc = 'Level '.$level.' ROI Income ('.$percent.'%) from '
                                .obscureAddress($from->username)
                                .' [D'.$dailyLog->id.'|L'.$level.']';

                            $walletCon->addearningwalletlog(
                                $upline->id,
                                1,
                                $earningType,
                                $desc,
                                $commission,
                                0,
                                0,
                                $roiDate.' '.date('H:i:s')
                            );

                            $paidLevels++;
                        } catch (\Throwable $e) {
                            // Unique race — treat as already paid
                            Log::warning('LevelRoiService duplicate skipped: '.$e->getMessage());
                        }
                    }
                }
            }

            $uplineId = (int) $upline->referral_id;
            $level++;
        }

        return $paidLevels;
    }
}
