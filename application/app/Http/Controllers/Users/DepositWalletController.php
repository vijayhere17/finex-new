<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use App\Models\DepositWallet;
use Log;
use DB;

class DepositWalletController extends Controller
{
    //
    public function depositWallet()
    {
        if(Auth::user()->is_topup <= 0)
        {
            $page_titel = '404';
            return view('users.404')->with(['page_titel'=>$page_titel])->toJS();
        }
        
        $page_titel = 'Topup Wallet';  

        $member_id = Auth::user()->id;

        $cradit = formatdecimal(self::getcraditdebitsum($member_id, 1), 2);
        $debit = formatdecimal(self::getcraditdebitsum($member_id, 2), 2);
        $balance = self::gewalletbalance($member_id);
          
        return view('users.deposit-wallet')->with(['page_titel'=>$page_titel, 'cradit'=>$cradit, 'debit'=>$debit, 'balance'=>$balance])->toJS();
    }

    public function getWalletReport(Request $request){
        $objects = DepositWallet::where('member_id', Auth::user()->id)
                                ->orderBy('created_at','desc')
                                ->get();
        return Datatables::of($objects)->make(true);
    }

    // ===============================================================================================================================================================

    public function getcraditdebitsum($member_id, $txn_type){
        $total = DepositWallet::where('member_id', $member_id)->where('txn_type', $txn_type)->sum('amount');
        return ($total == null ? 0 : $total);
    }

    public function gewalletbalance($member_id){
        $cradit = self::getcraditdebitsum($member_id, 1);
        $debit = self::getcraditdebitsum($member_id, 2);
        return formatdecimal($cradit-$debit, 2);
    }  
 
    public function addwalletlog($member_id, $txn_type, $log_type, $description, $amount, $created_at){
        $object = new DepositWallet;
        $object->member_id = $member_id;
        $object->txn_type = $txn_type;
        $object->log_type = $log_type;
        $object->description = $description;
        $object->amount = $amount;
        $object->created_at = $created_at;
        $object->save();
        return $object;
    }
}
