<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use App\Models\TransferWalletLog;
use App\Models\EarningWallet;
use App\Models\PotentialWallet;
use App\Models\WalletLog;
use App\Models\User;
use App\Models\UserStaked;
use Cookie;
use Log;
use DB;

class EarningWalletController extends Controller
{
    //
    public function earningReport()
    {
        $page_titel = 'Earning Wallet';  
        $member_id = Auth::user()->id;

        $cradit = formatdecimal(self::getcraditdebitsum($member_id, 1), 4);
        $debit = formatdecimal(self::getcraditdebitsum($member_id, 2), 4);
        $balance = self::getearningbalance($member_id);
          
        return view('users.stake-earning')->with(['page_titel'=>$page_titel, 'cradit'=>$cradit, 'debit'=>$debit, 'balance'=>$balance])->toJS();
    }

    public function earningWiseReport($logtype, $page_titel)
    {
        $member_id = Auth::user()->id;
        $logtype = (int) $logtype;

        // Normalize page titles for final business plan naming (UI only)
        $title_map = [
            1 => 'Direct Income',
            2 => 'Daily ROI Income',
            4 => 'Team Level ROI Income',
            7 => 'Reward Salary',
            9 => 'Life Time Reward',
            10 => 'Locked Reward Unlock',
        ];
        if(isset($title_map[$logtype]))
        {
            $page_titel = $title_map[$logtype];
        }

        $today_sum = formatdecimal(EarningWallet::where('member_id', $member_id)
            ->where('earning_type', $logtype)
            ->where('txn_type', 1)
            ->whereDate('created_at', today())
            ->sum('amount'), 4);

        $total_sum = formatdecimal($this->getearningsum($member_id, $logtype), 4);

        $latest_package = UserStaked::where('member_id', $member_id)
            ->with('kit')
            ->orderBy('id', 'desc')
            ->first();

        $package_name = ($latest_package && $latest_package->kit) ? $latest_package->kit->name : '-';
        $package_amount = $latest_package ? (float) $latest_package->paid_amount : 0;

        $history_title = 'Income History';
        if($logtype == 2) { $history_title = 'ROI History'; }
        if($logtype == 4) { $history_title = 'Team Level ROI History'; }
        if($logtype == 7) { $history_title = 'Reward Salary History'; }
        if($logtype == 10) { $history_title = 'Locked Reward Unlock History'; }

        return view('users.earning-wise-log')->with([
            'page_titel' => $page_titel,
            'logtype' => $logtype,
            'today_sum' => $today_sum,
            'total_sum' => $total_sum,
            'package_name' => $package_name,
            'package_amount' => $package_amount,
            'history_title' => $history_title,
        ])->toJS();
    }
    
    public function potentialReport()
    {
        $page_titel = 'Instant Wallet';  
        $member_id = Auth::user()->id;

        $cradit = formatdecimal(self::getcraditdebitsumpw($member_id, 1), 4);
        $debit = formatdecimal(self::getcraditdebitsumpw($member_id, 2), 4);
        $balance = self::getpwbalance($member_id);
          
        return view('users.potential-earning')->with(['page_titel'=>$page_titel, 'cradit'=>$cradit, 'debit'=>$debit, 'balance'=>$balance])->toJS();
    }

    // =========================================================================================================================================================================
    
    public function getearningsum($member_id, $log_type)
    {
        $total = EarningWallet::where('member_id', $member_id)->where('txn_type', '=', 1)->where('earning_type', $log_type)->sum('amount');
        
        return ($total == null ? 0 : $total);
    }
    
    public function getearningflush($member_id, $log_type)
    {
        $total = EarningWallet::where('member_id', $member_id)->where('txn_type', '=', 3)->sum('amount');
        
        return ($total == null ? 0 : $total);
    }
    
    public function getEarningWalletReport(Request $request)
    {
        $objects = EarningWallet::where('member_id', '=', Auth::user()->id)
                                ->orderBy('created_at', 'desc')
                                ->get();

        return Datatables::of($objects)->make(true);
    }
    
    public function getPotentialWalletReport(Request $request)
    {
        $objects = PotentialWallet::where('member_id', '=', Auth::user()->id)
                                  ->orderBy('created_at', 'desc')
                                  ->get();

        return Datatables::of($objects)->make(true);
    }

    public function getEarningWiseReport(Request $request)
    {
        $logtype = $request->get('logtype');

        $objects = EarningWallet::where('member_id', '=', Auth::user()->id)
                                ->where('earning_type', '=', $logtype)
                                ->orderBy('created_at', 'desc')
                                ->get();

        return Datatables::of($objects)->make(true);
    }
    
    public function getcraditdebitsum($member_id, $txn_type)
    {
        $total = EarningWallet::where('member_id', $member_id)->where('txn_type', $txn_type)->sum('amount');
        return ($total == null ? 0 : $total);
    }
    
    public function getcraditdebitsumpw($member_id, $txn_type)
    {
        $total = PotentialWallet::where('member_id', $member_id)->where('txn_type', $txn_type)->sum('amount');
        return ($total == null ? 0 : $total);
    }
    
    public function getTotalEarningSum($member_id)
    {
        $total = EarningWallet::where('member_id', '=', $member_id)->where('txn_type', '=', 1)->where('earning_type', '>', 0)->sum('amount');
        return ($total == null ? 0 : $total);
    }

    public function getearningbalance($member_id)
    {
        $cradit = self::getcraditdebitsum($member_id, 1);
        $debit = self::getcraditdebitsum($member_id, 2);
        return formatdecimal($cradit-$debit, 4);
    }
    
    public function getpwbalance($member_id)
    {
        $cradit = self::getcraditdebitsumpw($member_id, 1);
        $debit = self::getcraditdebitsumpw($member_id, 2);
        return formatdecimal($cradit-$debit, 4);
    }
    
    public function addearningwalletlog($member_id, $txn_type, $earning_type, $description, $amount, $coin_rate, $coin_amount, $created_at)
    {
        if($txn_type == 1)
        {
           $gross_amount = $amount; $admin_charge = 0; $system_charge = 0;  $net_amount = $amount; 
        }
        else if($txn_type == 3)
        {
           $gross_amount = $amount; $admin_charge = 0; $system_charge = 0;  $net_amount = $amount; 
        }
        else
        {
           $gross_amount = $amount; $admin_charge = 0; $system_charge = $amount;  $net_amount = $amount;  
        }
        
        if($gross_amount > 0)
        {
            $object = new EarningWallet;
            $object->member_id = $member_id;
            $object->txn_type = $txn_type;
            $object->earning_type = $earning_type;
            $object->description = $description;
            $object->gross_amount = formatdecimal($gross_amount,4);
            $object->admin_charge = formatdecimal($admin_charge,4);
            $object->system_charge = formatdecimal($system_charge,4);
            $object->amount = formatdecimal($net_amount,4);
            $object->coin_rate = formatdecimal($coin_rate,8);
            $object->coin_amount = formatdecimal($coin_amount,8);
            $object->created_at = $created_at;
            $object->save();
            
            if($txn_type == 1)
            {
                $member = User::find($member_id);
                $member->total_earning += $net_amount;
                if($earning_type <= 2)
                {
                    $member->total_return += $net_amount;
                }
                $member->save();
            }
            
            return $object;
        }
    }
    
    public function addpotentialwalletlog($member_id, $txn_type, $earning_type, $description, $amount, $coin_rate, $coin_amount, $created_at)
    {
        $gross_amount = $amount; $admin_charge = 0; $system_charge = $amount;  $net_amount = $amount;  
        
        $object = new PotentialWallet;
        $object->member_id = $member_id;
        $object->txn_type = $txn_type;
        $object->earning_type = $earning_type;
        $object->description = $description;
        $object->gross_amount = formatdecimal($gross_amount,4);
        $object->admin_charge = formatdecimal($admin_charge,4);
        $object->system_charge = formatdecimal($system_charge,4);
        $object->amount = formatdecimal($net_amount,4);
        $object->coin_rate = formatdecimal($coin_rate,8);
        $object->coin_amount = formatdecimal($coin_amount,8);
        $object->created_at = $created_at;
        $object->save();
        
        /* if($txn_type == 1)
        {
            $member = User::find($member_id);
            $member->total_earning += $net_amount;
            $member->save();
        } */
        
        return $object;
    }
    
    //
    
    public function runUpdateEarning()
    {
        $objects = User::where('kit_id', '>', 0)->get();
 
        foreach($objects as $log)
        {
            $total = EarningWallet::where('member_id', '=', $log->id)->where('txn_type', '=', 1)->where('earning_type', '>', 0)->sum('amount');
            
            $total_roi = formatdecimal(($this->getearningsum($log->id, 1)), 4);
	        $total_booster = formatdecimal(($this->getearningsum($log->id, 2)), 4);
	        
	        $total_return = $total_roi+$total_booster;
            
            $log->total_earning = $total;
            $log->total_return = $total_return;
            $log->save();
        }
    }
}
