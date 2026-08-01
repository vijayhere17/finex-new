<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\TurnoverRewardMaster;
use App\Models\TurnoverRewardAchiever;
use App\Models\LevelReferral;
use App\Models\EarningWallet;

use Log;
use DB;

class TurnoverRewardController extends Controller
{
    public function indexachievers()
    {
        $page_titel = 'Reward Achievement';

        $allrewards = TurnoverRewardMaster::where('milestone_order', '<=', 7)->orderBy('milestone_order', 'asc')->get();

        $user_id = Auth::user()->id;

        $leg_data = $this->getLegBusiness($user_id);
        $progress = $this->getRewardProgress($user_id);

        $achieved = TurnoverRewardAchiever::where('member_id', '=', $user_id)->get();
        $achieved_ids = $achieved->pluck('reward_id')->toArray();
        $active_achiever = $achieved->where('status', 0)->sortByDesc('id')->first();

        return view('users.turnover-reward')->with([
            'page_titel' => $page_titel,
            'allrewards' => $allrewards,
            'user_id' => $user_id,
            'leg_data' => $leg_data,
            'achieved_ids' => $achieved_ids,
            'achieved' => $achieved,
            'active_achiever' => $active_achiever,
            'progress' => $progress,
        ])->toJS();
    }

    /**
     * Top 3 sponsoring legs only (not binary).
     * Leg volume = direct self_investment + direct team_investment.
     */
    public function getLegBusiness($member_id)
    {
        $directs = User::where('referral_id', '=', $member_id)
            ->select('id', 'username', 'self_investment', 'team_investment')
            ->get()
            ->map(function ($d) {
                $d->leg_business = ((float) $d->self_investment) + ((float) $d->team_investment);
                return $d;
            })
            ->sortByDesc('leg_business')
            ->values();

        $leg1 = $directs->get(0);
        $leg2 = $directs->get(1);
        $leg3 = $directs->get(2);

        return [
            'leg1_business' => $leg1 ? (float) $leg1->leg_business : 0,
            'leg2_business' => $leg2 ? (float) $leg2->leg_business : 0,
            'leg3_business' => $leg3 ? (float) $leg3->leg_business : 0,
            'leg1_username' => $leg1 ? obscureAddress($leg1->username) : '',
            'leg2_username' => $leg2 ? obscureAddress($leg2->username) : '',
            'leg3_username' => $leg3 ? obscureAddress($leg3->username) : '',
        ];
    }

    public function getRewardProgress($member_id)
    {
        $member = User::find($member_id);

        $directs = User::where('referral_id', '=', $member_id)->where('kit_id', '>', 0)->count();

        $dashboardCon = app('App\Http\Controllers\Users\DashboardController');
        $team = $dashboardCon->getDownlineTeam($member_id, 1);

        $self_business = (float) ($member->self_investment ?? 0);
        $team_business = (float) ($member->team_investment ?? 0);

        $legs = $this->getLegBusiness($member_id);

        return [
            'directs' => $directs,
            'team' => $team,
            'self_business' => $self_business,
            'team_business' => $team_business,
            'legs' => $legs,
        ];
    }

    private function qualifiesForReward($progress, $reward)
    {
        $team_business = (float) ($reward->required_team_business > 0 ? $reward->required_team_business : $reward->turnover_amount);

        $leg1_percent = config('income.reward_leg1_percent', 40);
        $leg2_percent = config('income.reward_leg2_percent', 30);
        $leg3_percent = config('income.reward_leg3_percent', 30);

        $need_leg1 = $team_business * $leg1_percent / 100;
        $need_leg2 = $team_business * $leg2_percent / 100;
        $need_leg3 = $team_business * $leg3_percent / 100;

        $required_directs = (int) ($reward->required_directs ?? 0);
        $required_team = (int) ($reward->required_team ?? 0);
        $required_self = (float) ($reward->required_self_business ?? 0);

        if($required_directs > 0 && $progress['directs'] < $required_directs)
        {
            return false;
        }

        if($required_team > 0 && $progress['team'] < $required_team)
        {
            return false;
        }

        if($required_self > 0 && $progress['self_business'] < $required_self)
        {
            return false;
        }

        if($team_business > 0)
        {
            $legs = $progress['legs'];

            if($legs['leg1_business'] < $need_leg1 || $legs['leg2_business'] < $need_leg2 || $legs['leg3_business'] < $need_leg3)
            {
                return false;
            }
        }

        return true;
    }

    // ==========================================================================================================================================================================================

    /**
     * Daily reward qualification. Stores achievement + weekly salary schedule.
     * Highest reward only receives weekly salary (status=0). No duplicate achievements.
     */
    public function runTurnoverAchiever()
    {
        $ladder = TurnoverRewardMaster::where('milestone_order', '<=', 7)->orderBy('milestone_order', 'asc')->get();

        $members = User::where('kit_id', '>', 0)->get();

        foreach($members as $member)
        {
            $progress = $this->getRewardProgress($member->id);

            foreach($ladder as $reward)
            {
                $already = TurnoverRewardAchiever::where('member_id', '=', $member->id)
                    ->where('reward_id', '=', $reward->id)
                    ->exists();

                if($already)
                {
                    continue;
                }

                if(!$this->qualifiesForReward($progress, $reward))
                {
                    continue;
                }

                $weekly = (float) (($reward->weekly_salary > 0) ? $reward->weekly_salary : $reward->cash_reward);

                try {
                    DB::beginTransaction();

                    // Stop salary on any previous active reward — highest only
                    TurnoverRewardAchiever::where('member_id', '=', $member->id)
                        ->where('status', '=', 0)
                        ->update(['status' => 1]);

                    $achiever = new TurnoverRewardAchiever;
                    $achiever->member_id = $member->id;
                    $achiever->reward_id = $reward->id;
                    $achiever->leg1_business = $progress['legs']['leg1_business'];
                    $achiever->leg2_business = $progress['legs']['leg2_business'];
                    $achiever->leg3_business = $progress['legs']['leg3_business'];
                    $achiever->cash_reward = $weekly;
                    $achiever->weekly_salary = $weekly;
                    $achiever->directs_count = $progress['directs'];
                    $achiever->team_count = $progress['team'];
                    $achiever->self_business = $progress['self_business'];
                    $achiever->team_business = $progress['team_business'];
                    $achiever->return_date = date('Y-m-d', strtotime('+7 days'));
                    $achiever->weeks_paid = 0;
                    $achiever->status = 0;
                    $achiever->save();

                    $member->reward_id = $reward->id;
                    $member->save();

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error($e);
                    continue;
                }
            }
        }
    }

    /**
     * Pay weekly reward salary every 7 days for the highest active reward only.
     * Respects 3X earning cap. Prevents duplicate salary for the same week.
     */
    public function runRewardSalary()
    {
        $dashboardCon = app('App\Http\Controllers\Users\DashboardController');
        $walletCon = app('App\Http\Controllers\Users\EarningWalletController');

        $today = date('Y-m-d');

        $objects = TurnoverRewardAchiever::where('status', '=', 0)
            ->where('weekly_salary', '>', 0)
            ->where('return_date', '<=', $today)
            ->get();

        foreach($objects as $log)
        {
            $reward = TurnoverRewardMaster::find($log->reward_id);
            $title = $reward ? ($reward->title ?: ('Reward #'.$reward->milestone_order)) : ('Reward #'.$log->reward_id);

            // Duplicate salary guard: already paid today
            $already_paid = EarningWallet::where('member_id', '=', $log->member_id)
                ->where('earning_type', '=', 7)
                ->where('txn_type', '=', 1)
                ->whereDate('created_at', $today)
                ->where('description', 'like', 'Reward Salary - '.$title.'%')
                ->exists();

            if($already_paid)
            {
                continue;
            }

            // Also skip if last_paid_at is within last 6 days
            if($log->last_paid_at != null && strtotime($log->last_paid_at) > strtotime('-6 days'))
            {
                continue;
            }

            $commission = (float) $log->weekly_salary;
            $description = 'Reward Salary - '.$title.' (Weekly)';

            $remain_commission = $dashboardCon->check3xEarningLimit($log->member_id, $commission);

            try {
                DB::beginTransaction();

                // Lock row to prevent overlapping cron double-pay
                $fresh = TurnoverRewardAchiever::where('id', $log->id)->where('status', 0)->lockForUpdate()->first();

                if($fresh == null || ($fresh->return_date != null && $fresh->return_date > $today))
                {
                    DB::rollBack();
                    continue;
                }

                if($remain_commission > 0)
                {
                    $walletCon->addearningwalletlog($log->member_id, 1, 7, $description, $remain_commission, 0, 0, date('Y-m-d H:i:s'));
                }
                else
                {
                    $walletCon->addearningwalletlog($log->member_id, 3, 7, $description, $commission, 0, 0, date('Y-m-d H:i:s'));
                }

                $fresh->weeks_paid = ((int) $fresh->weeks_paid) + 1;
                $fresh->last_paid_at = date('Y-m-d H:i:s');
                $fresh->return_date = date('Y-m-d', strtotime('+7 days'));
                $fresh->save();

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e);
                continue;
            }
        }
    }

    /**
     * Backward-compatible alias used by older cron comments.
     */
    public function runRewardEarning()
    {
        $this->runRewardSalary();
    }
}
