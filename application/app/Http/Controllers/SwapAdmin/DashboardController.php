<?php

namespace App\Http\Controllers\SwapAdmin;

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
        
        $refer_bonus = EarningWallet::where('earning_type', 1)->sum('amount');
        $refer_upline_bonus = EarningWallet::where('earning_type', 2)->sum('amount');
        $cashback_bonus = EarningWallet::where('earning_type', 3)->sum('amount');
        $binary_bonus = EarningWallet::where('earning_type', 4)->sum('amount');
        $binary_upline_bonus = EarningWallet::where('earning_type', 5)->sum('amount');
        $leadership_bonus = EarningWallet::where('earning_type', 6)->sum('amount');
        
        $generate_bonus = EarningWallet::where('earning_type', '>', 0)->sum('amount');
        
        $t_pending_w = WithdrawalLog::where('status', '=', 0)->whereDate('updated_at','=',date("Y-m-d"))->sum('amount');
        $t_processing_w = WithdrawalLog::where('status', '=', 1)->whereDate('updated_at','=',date("Y-m-d"))->sum('amount');
        $t_success_w = WithdrawalLog::where('status', '=', 2)->whereDate('updated_at','=',date("Y-m-d"))->sum('amount');
        $t_rejected_w = WithdrawalLog::where('status', '=', 3)->whereDate('updated_at','=',date("Y-m-d"))->sum('amount');
        
        $pending_w = WithdrawalLog::where('status', '=', 0)->sum('amount');
        $processing_w = WithdrawalLog::where('status', '=', 1)->sum('amount');
        $success_w = WithdrawalLog::where('status', '=', 2)->sum('amount');
        $rejected_w = WithdrawalLog::where('status', '=', 3)->sum('amount');
    
        return view('swapadmin.dashboard', compact('title', 'today_member', 'today_a_member', 'total_member', 'total_a_member', 'refer_bonus', 'refer_upline_bonus', 'cashback_bonus', 'binary_bonus', 'binary_upline_bonus', 'leadership_bonus', 'generate_bonus', 't_pending_w', 't_processing_w', 't_success_w', 't_rejected_w', 'pending_w', 'processing_w', 'success_w', 'rejected_w'));
    }
}
