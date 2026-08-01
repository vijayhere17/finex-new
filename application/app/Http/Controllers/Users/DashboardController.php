<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserStaked;
use App\Models\LevelReferral;
use App\Models\StakeRequest;
use App\Models\WithdrawalLog;
use App\Models\BinaryPoints;
use App\Models\SalaryMaster;
use App\Models\SalaryAchiever;
use App\Models\EarningWallet;
use App\Models\DailyRoiLog;
use App\Models\TurnoverRewardMaster;
use App\Models\TurnoverRewardAchiever;
use App\Services\DirectRoiService;
use App\Services\AutoUpgradeService;

use DB;

class DashboardController extends Controller
{
    //
    public function index()
    {
        $balanceCon = app('App\Http\Controllers\Users\EarningWalletController');
        $user = Auth::user();
        $userId = $user->id;
        $page_titel = 'Dashboard';

        $directRoi = app(DirectRoiService::class)->getDisplayStats($user);
        $progress = app(AutoUpgradeService::class)->resolveSlotProgress($user);

        if ((int) ($user->current_slot ?? -1) !== (int) $progress['current_slot']
            || (int) ($user->next_slot ?? -1) !== (int) $progress['next_slot']) {
            $user->current_slot = $progress['current_slot'];
            $user->next_slot = $progress['next_slot'];
            $user->save();
        }

        $amounts = config('income.slot_amounts', []);
        $nextAmount = ($progress['next_slot'] > 0) ? ($amounts[$progress['next_slot'] - 1] ?? 0) : 0;

        $fx = (object) [
            'current_slot' => $progress['current_slot'],
            'next_slot' => $progress['next_slot'],
            'next_slot_amount' => $nextAmount,
            'activation_status' => $user->activation_status ?? 'registered',
            'qualified_active_directs' => $directRoi['qualified_active_directs'],
            'direct_roi_percent' => $directRoi['direct_roi_percent'],
            'today_roi' => (float) DailyRoiLog::where('member_id', $userId)->whereDate('roi_date', today())->sum('amount'),
            'total_roi' => (float) $balanceCon->getearningsum($userId, 2),
            'level_roi' => (float) $balanceCon->getearningsum($userId, 4),
            'auto_upgrade_balance' => (float) ($user->auto_upgrade_balance ?? 0),
            'available_wallet' => (float) $balanceCon->getearningbalance($userId),
            'total_withdrawn' => (float) WithdrawalLog::where('member_id', $userId)->where('status', 2)->sum('payable'),
        ];

        $object = new Dashboarddata;
        $object->qualified_active_directs = $directRoi['qualified_active_directs'];
        $object->direct_roi_percent = $directRoi['direct_roi_percent'];
        $object->total_a_referral = $directRoi['qualified_active_directs'];

        return view('users.dashboard', compact('page_titel', 'fx', 'object'));
    }
    
    public function getDownlineTeam($user_id, $is_active)
    {
		DB::statement('SET SESSION group_concat_max_len = 10000000');

		$downlines = LevelReferral::where('member_id', '=', $user_id)
                                  ->select(DB::raw('group_concat(downlines) as downlines'))
                                  ->first();

        $downlines = $downlines->downlines;
        
        if ($is_active > 0) 
        {
            $total = User::whereRaw('FIND_IN_SET(id,"'.$downlines.'")')->where('kit_id', '>', 0)->count();
        } 
        else 
        {
            $total = User::whereRaw('FIND_IN_SET(id,"'.$downlines.'")')->where('kit_id', '<=', 0)->count();
        }
		
		return $total == null ? 0 : $total;
	}

    public function getTeamBusiness($user_id, $level, $is_date)
    {
        DB::statement('SET SESSION group_concat_max_len = 10000000');

        if ($level == 0) 
        {
            $downlines = LevelReferral::where('member_id', '=', $user_id)->select(DB::raw('group_concat(downlines) as downlines'))->first();
            $downlines = $downlines->downlines;
        } 
        else 
        {
            $downlines = LevelReferral::where('member_id', '=', $user_id)->where('level', '=', $level)->first();
            $downlines = ($downlines == null ? '' : $downlines->downlines);
        }
        
        $query = UserStaked::whereRaw('FIND_IN_SET(member_id,"'.$downlines.'")');

        // Add date filter based on is_date
        if ($is_date == 1) { // Today
            $query->whereDate('created_at', today());
        } elseif ($is_date == 2) { // Weekly (last 7 days)
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } // is_date == 0 means no date filter (all records)
    
        $total = $query->sum('paid_amount');
        
        return $total == null ? 0 : $total;
	}

    public function getTeamStaking($user_id, $leg){
        /* DB::statement('SET SESSION group_concat_max_len = 10000000');
        $downlines = LevelReferral::where('member_id', '=', $user_id)->select(DB::raw('group_concat(downlines) as downlines'))->first();
        $downlines = $downlines->downlines;

        $total = UserStaked::whereRaw('FIND_IN_SET(member_id,"'.$downlines.'")')->sum('paid_amount'); */
        
        $binary_point = BinaryPoints::where('member_id',$user_id)->first();
        
        if ($leg  == 'A') {
            $total = ($binary_point->left_points+$binary_point->right_points);
        } else if ($leg  == 'L') {
            $total = ($binary_point->left_points);
        } else if ($leg  == 'R') {
            $total = ($binary_point->right_points);
        }
        
        return $total == null ? 0 : $total;
	}
	
	// ============================================================================================================================================================================
	
	// Working ID (has >=1 active direct referral) caps at 3x lifetime staked; Non-Working ID caps at 2x.
	public function isWorkingId($member_id)
	{
	    return User::where('referral_id','=',$member_id)->where('kit_id','>',0)->exists();
	}

	public function check3xEarningLimit($member_id, $amount)
	{
	    $balanceCon = app('App\Http\Controllers\Users\EarningWalletController');

	    $stake_obj = UserStaked::where('member_id', $member_id)->sum('paid_amount');
	    $total_staked = ($stake_obj == null ? 0 : $stake_obj);

	    $member = User::find($member_id);
	    $total_earning = $member->total_earning;

	    $cap_multiplier = $this->isWorkingId($member_id) ? config('income.working_cap_multiplier', 3) : config('income.non_working_cap_multiplier', 2);

	    if($total_staked > 0)
	    {
	        $max_earning = $total_staked * $cap_multiplier;

	        // $total_earning = formatdecimal(($balanceCon->getTotalEarningSum($member_id)), 4);

	        $total = formatdecimal($total_earning+$amount,4);

    	    if($total > $max_earning)
    	    {
    	        return $max_earning-$total_earning;
    	    }
    	    else
    	    {
    	        return $amount;
    	    }
	    }
	    else
	    {
	        return 0;
	    }
	}
	
	public function check2xEarningLimit($member_id, $amount)
	{
	    $balanceCon = app('App\Http\Controllers\Users\EarningWalletController');

	    $stake_obj = UserStaked::where('member_id', $member_id)->sum('paid_amount');
	    $total_staked = ($stake_obj == null ? 0 : $stake_obj);
	    
	    $member = User::find($member_id);
	    $total_earning = $member->total_return;
	    
	    if($total_staked > 0)
	    {
	        $max_earning = $total_staked * 2;
	        
	        // $total_roi = formatdecimal(($balanceCon->getearningsum($member_id, 1)), 4);
	        // $total_booster = formatdecimal(($balanceCon->getearningsum($member_id, 2)), 4);
	        
	        // $total_earning = $total_roi+$total_booster;
	        
	        $total = formatdecimal($total_earning+$amount,4);
	        
    	    if($total > $max_earning)
    	    {
    	        return $max_earning-$total_earning; 
    	    }
    	    else
    	    {
    	        return $amount;  
    	    }
	    }
	    else
	    {
	        return 0;  
	    }
	}

    public function remin3xEarning($member_id)
	{
        $stake_obj = UserStaked::where('member_id', $member_id)->sum('paid_amount');
	    $total_staked = ($stake_obj == null ? 0 : $stake_obj);

        $cap_multiplier = $this->isWorkingId($member_id) ? config('income.working_cap_multiplier', 3) : config('income.non_working_cap_multiplier', 2);

        $max_earning = $total_staked * $cap_multiplier;

        // $balanceCon = app('App\Http\Controllers\Users\EarningWalletController');
	    // $total_earning = formatdecimal(($balanceCon->getTotalEarningSum($member_id)), 4);
	    
	    $member = User::find($member_id);
	    $total_earning = $member->total_earning;

        if($max_earning > 0)
        {
            $remain = ($max_earning - $total_earning);

            if($remain > 0)
            {
               return $remain;
            }
            else
            {
                return 0;
            }
        }
        else
        {
            return 0;
        }
	}
	
	public function remin2xEarning($member_id)
	{
        $stake_obj = UserStaked::where('member_id', $member_id)->sum('paid_amount');
	    $total_staked = ($stake_obj == null ? 0 : $stake_obj);

        $max_earning = $total_staked * 2;

        // $balanceCon = app('App\Http\Controllers\Users\EarningWalletController');
	    
	    // $total_roi = formatdecimal(($balanceCon->getearningsum($member_id, 1)), 4);
        // $total_booster = formatdecimal(($balanceCon->getearningsum($member_id, 2)), 4);
        
        // $total_earning = $total_roi+$total_booster;
        
        $member = User::find($member_id);
	    $total_earning = $member->total_return;

        if($max_earning > 0)
        {
            $remain = ($max_earning - $total_earning);

            if($remain > 0)
            {
               return $remain;
            }
            else
            {
                return 0;
            }
        }
        else
        {
            return 0;
        }
	}
	
	public function get80XLimitWarning($member_id)
	{
        $stake_obj = UserStaked::where('member_id', $member_id)->sum('paid_amount');
	    $total_staked = ($stake_obj == null ? 0 : $stake_obj);

        $cap_multiplier = $this->isWorkingId($member_id) ? config('income.working_cap_multiplier', 3) : config('income.non_working_cap_multiplier', 2);

        $max_earning = $total_staked * $cap_multiplier;
        $max_earning = $max_earning * 0.80;

        // $balanceCon = app('App\Http\Controllers\Users\EarningWalletController');
	    // $total_earning = formatdecimal(($balanceCon->getTotalEarningSum($member_id)), 4);
	    
	    $member = User::find($member_id);
	    $total_earning = $member->total_earning;

        if($max_earning > 0)
        {
            $remain = ($max_earning - $total_earning);

            if($remain > 0)
            {
               return $remain;
            }
            else
            {
                return 0;
            }
        }
        else
        {
            return -1;
        }
	}

    public function maxEarning($member_id, $max_x)
	{
        $stake_obj = UserStaked::where('member_id', $member_id)->sum('paid_amount');
	    $total_staked = ($stake_obj == null ? 0 : $stake_obj);

	    return $total_staked * $max_x;
	}
}


class Dashboarddata
{
    //
}
