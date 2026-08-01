<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\DailyRoiService;

class ProcessDaily extends Command
{
    protected $signature = 'act:processdaily
                            {--date= : Optional ROI date Y-m-d}
                            {--test : TEMP: use next fake date (for 10-min testing)}';

    protected $description = 'Finex daily process: Daily ROI + Level ROI distribution';

    public function handle()
    {
        $date = $this->option('date') ?: null;

        // TEMP testing: each run uses a new calendar date so unique(stake_id, roi_date) allows another payout.
        if ($date == null && ($this->option('test') || config('income.test_roi.enabled', false))) {
            $date = $this->nextTestRoiDate();
            $this->warn('TEST ROI mode — paying for fake date: '.$date);
        }

        Log::info('Finex Daily ROI start...', ['date' => $date]);
        $this->info('Running Daily ROI distribution...');

        $summary = app(DailyRoiService::class)->distribute($date);

        Log::info('Finex Daily ROI end', $summary);
        $this->info(
            'Paid: '.$summary['paid']
            .' | LevelROI: '.($summary['levelPaid'] ?? 0)
            .' | Skipped: '.$summary['skipped']
            .' | Closed: '.$summary['closed']
            .' | Date: '.$summary['roiDate']
        );

        // Legacy reward / locked-reward / salary crons disabled for new compensation plan.
        if (config('income.legacy_reward_cron_enabled', false)) {
            $rewardCon = app('App\Http\Controllers\Users\TurnoverRewardController');
            $rewardCon->runTurnoverAchiever();
            $rewardCon->runRewardSalary();
        }

        if (config('income.legacy_locked_reward_enabled', false)) {
            app('App\Http\Controllers\Users\StakeController')->runLockedRewardExpiry();
        }

        return Command::SUCCESS;
    }

    /**
     * Advance a counter and map it to seed_date + N days.
     * Example: run 1 → 2026-01-01, run 2 → 2026-01-02, ...
     */
    protected function nextTestRoiDate(): string
    {
        $seed = config('income.test_roi.date_seed', '2026-01-01');
        $n = (int) Cache::increment('finex_test_roi_day_counter');
        if ($n < 1) {
            $n = 1;
            Cache::forever('finex_test_roi_day_counter', 1);
        }

        return date('Y-m-d', strtotime($seed.' +'.($n - 1).' days'));
    }
}
