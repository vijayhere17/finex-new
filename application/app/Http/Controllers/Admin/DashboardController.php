<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\AdminUser;
use App\Models\User;
use App\Models\StakeMaster;
use App\Models\UserStaked;
use App\Models\EarningWallet;
use App\Models\WalletLog;

use App\Models\WithdrawalLog;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Session;

class DashboardController extends Controller
{
    public function index()
    {
        $title = 'Dashboard';
        
        $today_member = User::whereDate('created_at',date("Y-m-d"))->count();
        $today_a_member = User::where('kit_id','>',0)->whereDate('activation_date',date("Y-m-d"))->count();
        $total_member = User::count();
        $total_a_member = User::where('kit_id','>',0)->count();
        
        $today_business = UserStaked::whereDate('created_at', now()->toDateString())->sum('paid_amount');
        $weekly_business = UserStaked::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('paid_amount');
        $monthly_business = UserStaked::whereMonth('created_at', now()->month)->whereYear('updated_at', now()->year)->sum('paid_amount');
        $total_business = UserStaked::sum('paid_amount');
        
        $refer_bonus = EarningWallet::where('earning_type', 1)->sum('amount');
        $refer_upline_bonus = EarningWallet::where('earning_type', 2)->sum('amount');
        $cashback_bonus = EarningWallet::where('earning_type', 3)->sum('amount');
        $binary_bonus = EarningWallet::where('earning_type', 4)->sum('amount');
        $binary_upline_bonus = EarningWallet::where('earning_type', 5)->sum('amount');
        $leadership_bonus = EarningWallet::where('earning_type', 6)->sum('amount');
        $reward_salary_bonus = EarningWallet::where('earning_type', 7)->sum('amount');
        $booster_bonus = EarningWallet::where('earning_type', 8)->sum('amount');
        $lifetime_bonus = EarningWallet::where('earning_type', 9)->sum('amount');
        $locked_unlock_bonus = EarningWallet::where('earning_type', 10)->sum('amount');
        
        $generate_bonus = EarningWallet::where('earning_type', '>', 0)->sum('amount');
        
        $t_pending_w = WithdrawalLog::where('status', '=', 0)->whereDate('updated_at','=',date("Y-m-d"))->sum('amount');
        $t_processing_w = WithdrawalLog::where('status', '=', 1)->whereDate('updated_at','=',date("Y-m-d"))->sum('amount');
        $t_success_w = WithdrawalLog::where('status', '=', 2)->whereDate('updated_at','=',date("Y-m-d"))->sum('amount');
        $t_rejected_w = WithdrawalLog::where('status', '=', 3)->whereDate('updated_at','=',date("Y-m-d"))->sum('amount');
        
        $pending_w = WithdrawalLog::where('status', '=', 0)->sum('amount');
        $processing_w = WithdrawalLog::where('status', '=', 1)->sum('amount');
        $success_w = WithdrawalLog::where('status', '=', 2)->sum('amount');
        $rejected_w = WithdrawalLog::where('status', '=', 3)->sum('amount');

        $locked_reward_total = User::sum('locked_reward_bonus');
        $unlocked_reward_total = User::sum('unlocked_reward_bonus');
        $expired_reward_total = User::sum('expired_reward_bonus');
    
        return view('admin.dashboard', compact('title', 'today_member', 'today_a_member', 'total_member', 'total_a_member', 'refer_bonus', 'refer_upline_bonus', 'cashback_bonus', 'binary_bonus', 'binary_upline_bonus', 'leadership_bonus', 'reward_salary_bonus', 'booster_bonus', 'lifetime_bonus', 'locked_unlock_bonus', 'locked_reward_total', 'unlocked_reward_total', 'expired_reward_total', 'generate_bonus', 't_pending_w', 't_processing_w', 't_success_w', 't_rejected_w', 'pending_w', 'processing_w', 'success_w', 'rejected_w', 'today_business', 'weekly_business', 'monthly_business', 'total_business'));
    }
}
