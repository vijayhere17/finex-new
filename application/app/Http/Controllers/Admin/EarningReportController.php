<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\EarningWallet;
use App\Models\PotentialWallet;
use App\Models\WalletLog;
use App\Models\DepositWallet;
use App\Models\CraditDebitLog;
use App\Models\BalanceView;
use Log;
use DB;

class EarningReportController extends Controller
{
    //
    public function craditdebitMaster()
    {
        $page_titel = 'Cradit & Debit Wallet';    
        return view('admin.credit-debit')->with(['page_titel'=>$page_titel])->toJS();
    }
    
    public function earningReport($type, $title)
    {
        $page_titel = $title;  
        
        return view('admin.earning-report')->with(['page_titel'=>$page_titel, 'type'=>$type])->toJS(); 
    }
    
    public function balanceReport()
    {
        $page_titel = 'Outstanding Balance Report';  
        return view('admin.balance-report')->with(['page_titel'=>$page_titel])->toJS(); 
    }
    
    public function craditdebitReport()
    {
        $page_titel = 'History Of Cradit & Debit';    
        return view('admin.cradit-debit-report')->with(['page_titel'=>$page_titel])->toJS();
    }
    
    public function actionCraditDebit(Request $request)
    {
        try {
            $v = Validator::make($request->all(), [
                'assigned_to' => 'required',
				'amount' => 'required',
				'wallet' => 'required',
				'action' => 'required',
				'description' => 'required'
            ]);

            if($v->fails())
            {
				return response()->json(array('success'=>false,'error_code'=> 'INVALID_REQUEST_DATA'), 200);
            }

			$user = Auth::user();
			if($user == null)
			{
				return response()->json(array('success'=>false,'error_code'=> 'SESSION_INVALID'), 200);
			}
            
            $member = User::where('username',$request->assigned_to)->first();  
            if($member == null)
            {
				return response()->json(array('success'=>false,'error_code'=> 'INVALID_MEMBER'), 200);
			}
			
			$coin_rate = getcoinrate();
			
			$created_at = date("Y-m-d H:i:s");
            
            if($request->wallet == 'Earning Wallet')
            {
                $this->addearningwalletlog($member->id, $request->action, 0, $request->description, $request->amount, $coin_rate, formatdecimal($request->amount/$coin_rate, 8), $created_at);   
            }
            else if($request->wallet == 'Topup Wallet')
            {
                $this->adddepositwalletlog($member->id, $request->action, 0, $request->description, $request->amount, $created_at); 
            }
            
            $this->addcraditdebitlog($user->id, $member->id, $request->amount, $request->action, $request->wallet, $request->description, $created_at);
            
			return response()->json(array('success'=> true, 'error_code'=> ''), 200);
        } catch(\Exception $exception) {
            Log::error($exception);
            return response()->json(array('success'=>false,'error_code'=> 'UNEXPECTED_ERROR_OCCURED'), 200);
        }
    }
    
    public function addearningwalletlog($member_id, $txn_type, $earning_type, $description, $amount, $coin_rate, $coin_amount, $created_at)
    {
        $object = new EarningWallet;
        $object->member_id = $member_id;
        $object->txn_type = $txn_type;
        $object->earning_type = $earning_type;
        $object->description = $description;
        $object->amount = $amount;
        $object->coin_rate = $coin_rate;
        $object->coin_amount = $coin_amount;
        $object->created_at = $created_at;
        $object->save();
        return $object;
    }
    
    public function adddepositwalletlog($member_id, $txn_type, $log_type, $description, $amount, $created_at)
    {
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
    
    public function addwalletlog($member_id, $txn_type, $earning_type, $description, $amount, $coin_rate, $coin_amount, $created_at)
    {
        $object = new WalletLog;
        $object->member_id = $member_id;
        $object->txn_type = $txn_type;
        $object->earning_type = $earning_type;
        $object->description = $description;
        $object->amount = $amount;
        $object->coin_rate = $coin_rate;
        $object->coin_amount = $coin_amount;
        $object->created_at = $created_at;
        $object->save();
        return $object;
    }
    
    public function addcraditdebitlog($admin_id, $member_id, $amount, $type_id, $wallet, $description, $created_at)
    {
        $object = new CraditDebitLog;
        $object->admin_id = $admin_id;
        $object->member_id = $member_id;
        $object->amount = $amount;
        $object->type_id = $type_id;
        $object->wallet = $wallet;
        $object->description = $description;
        $object->created_at = $created_at;
        $object->save();
        return $object;
    }

    public function getEarningReport(Request $request){
        $draw = $request->get('draw');
        $start = $request->get("start");
        $length = $request->get("length"); // Rows display per page

        $type = $request->get('type');
        
        $search_arr = $request->get('search');
        $searchValue = $search_arr['value']; // Search value

        // Total records  
        if($type == 10)
        {
            $listearningrep = PotentialWallet::join('users','pwallet_logs.member_id','=','users.id')->where('pwallet_logs.earning_type','=',1)->where('pwallet_logs.amount','>',0)->orderBy('pwallet_logs.created_at','desc'); 
        }
        else 
        {
           $listearningrep = EarningWallet::join('users','ewallet_logs.member_id','=','users.id')->where('ewallet_logs.earning_type',$type)->where('ewallet_logs.amount','>',0)->orderBy('ewallet_logs.created_at','desc'); 
        }
        
        if($searchValue != null){
            $listearningrep = $listearningrep->where('users.username','=',$searchValue)->orWhere(DB::raw('CONCAT(users.firstname," ",users.lastname)'),'like','%'.$searchValue.'%');
        }
                      
        $totalRecords = $listearningrep->count();
        
        $totalRecordswithFilter = $totalRecords;
        
        if($type == 10)
        {
            $records = $listearningrep->select('pwallet_logs.id', 'pwallet_logs.member_id', 'pwallet_logs.txn_type', 'pwallet_logs.earning_type', 'pwallet_logs.description', 'pwallet_logs.amount', 'pwallet_logs.coin_rate', 'pwallet_logs.coin_amount', 'pwallet_logs.created_at')
                                      ->with(array('member'=>function($query){
                                            $query->select('id', 'username', 'firstname', 'lastname');
                                        }))                        
                                      ->skip($start)
        							  ->take($length)
                                      ->get();
        }
        else
        {
            $records = $listearningrep->select('ewallet_logs.id', 'ewallet_logs.member_id', 'ewallet_logs.txn_type', 'ewallet_logs.earning_type', 'ewallet_logs.description', 'ewallet_logs.amount', 'ewallet_logs.coin_rate', 'ewallet_logs.coin_amount', 'ewallet_logs.created_at')
                                      ->with(array('member'=>function($query){
                                            $query->select('id', 'username', 'firstname', 'lastname');
                                        }))                        
                                      ->skip($start)
        							  ->take($length)
                                      ->get(); 
        }
        
        // Fetch records
        $data_arr = array();
         
        foreach($records as $record){ 
            $data_arr[] = array(
                "id" => $record->id,
                "txn_on" => date("d/m/Y H:i A", strtotime($record->created_at)),
                "username" => obscureAddress($record->member->username),
                "name" => $record->member->firstname.' '.$record->member->lastname,
                "description" => $record->description,
                "amount" => $record->amount,
                "coin_rate" => $record->coin_rate,
                "coin_amount" => $record->coin_amount,
                "txn_type" => ($record->txn_type == 1 ? '<b style="color: green;">Cradit</b>' : '<b style="color: red;">Debit</b>')
            );
        }
        
        $response = array(
            "draw" => intval($draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecordswithFilter,
            "data" => $data_arr
        );
    
        return json_encode($response);
    }
    
    public function getDMCEarningReport(Request $request){
        $draw = $request->get('draw');
        $start = $request->get("start");
        $length = $request->get("length"); // Rows display per page

        $type = $request->get('type');
        
        $search_arr = $request->get('search');
        $searchValue = $search_arr['value']; // Search value

        // Total records 
        $listearningrep = WalletLog::join('users','wallet_logs.member_id','=','users.id')->where('wallet_logs.earning_type',$type)->where('wallet_logs.coin_amount','>',0)->orderBy('wallet_logs.created_at','desc');
        
        if($searchValue != null){
            $listearningrep = $listearningrep->where('users.username','=',$searchValue)->orWhere(DB::raw('CONCAT(users.firstname," ",users.lastname)'),'like','%'.$searchValue.'%');
        }
        
        $totalRecords = $listearningrep->count();
        
        $totalRecordswithFilter = $totalRecords;
        
        $records = $listearningrep->select('wallet_logs.id', 'wallet_logs.member_id', 'wallet_logs.txn_type', 'wallet_logs.earning_type', 'wallet_logs.description', 'wallet_logs.amount', 'wallet_logs.coin_rate', 'wallet_logs.coin_amount', 'wallet_logs.created_at')
                                  ->with(array('member'=>function($query){
                                        $query->select('id', 'username', 'firstname', 'lastname');
                                    }))                        
                                  ->skip($start)
    							  ->take($length)
                                  ->get(); 
        // Fetch records
        $data_arr = array();
         
        foreach($records as $record){ 
            $data_arr[] = array(
                "id" => $record->id,
                "txn_on" => date("d/m/Y H:i A", strtotime($record->created_at)),
                "username" => $record->member->username,
                "name" => $record->member->firstname.' '.$record->member->lastname,
                "description" => $record->description,
                "amount" => $record->amount,
                "coin_rate" => $record->coin_rate,
                "coin_amount" => $record->coin_amount,
                "txn_type" => ($record->txn_type == 1 ? '<b style="color: green;">Cradit</b>' : '<b style="color: red;">Debit</b>')
            );
        }
        
        $response = array(
            "draw" => intval($draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecordswithFilter,
            "data" => $data_arr
        );
    
        return json_encode($response);
    }
    
    public function getCraditDebitReport(Request $request){
        $draw = $request->get('draw');
        $start = $request->get("start");
        $length = $request->get("length"); // Rows display per page

        $type = $request->get('type');
        
        $search_arr = $request->get('search');
        $searchValue = $search_arr['value']; // Search value

        // Total records
        $listrep = CraditDebitLog::join('users','credit_debit_by_admin.member_id','=','users.id')->orderBy('credit_debit_by_admin.created_at','desc');
        
        if($searchValue != null){
            $listrep = $listrep->where('users.username','=',$searchValue)->orWhere(DB::raw('CONCAT(users.firstname," ",users.lastname)'),'like','%'.$searchValue.'%');
        }
                      
        $totalRecords = $listrep->count();
        
        $totalRecordswithFilter = $totalRecords;
        
        $records = $listrep->select('credit_debit_by_admin.id', 'credit_debit_by_admin.admin_id', 'credit_debit_by_admin.member_id', 'credit_debit_by_admin.amount', 'credit_debit_by_admin.type_id', 'credit_debit_by_admin.wallet', 'credit_debit_by_admin.description', 'credit_debit_by_admin.created_at')
                           ->with(array('member'=>function($query){
                                $query->select('id', 'username', 'firstname', 'lastname');
                            }))                        
                           ->skip($start)
    					   ->take($length)
                           ->get(); 
        // Fetch records
        $data_arr = array();
         
        foreach($records as $record){ 
            $data_arr[] = array(
                "id" => $record->id,
                "txn_on" => date("d/m/Y H:i A", strtotime($record->created_at)),
                "username" => obscureAddress($record->member->username),
                "name" => $record->member->firstname.' '.$record->member->lastname,
                "description" => $record->description,
                "amount" => $record->amount,
                "wallet" => '<b>'.$record->wallet.'</b>',
                "txn_type" => ($record->type_id == 1 ? '<b style="color: green;">Cradit</b>' : '<b style="color: red;">Debit</b>')
            );
        }
        
        $response = array(
            "draw" => intval($draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecordswithFilter,
            "data" => $data_arr
        );
    
        return json_encode($response);
    }
    
    public function getBalanceReport(Request $request){
        $draw = $request->get('draw');
        $start = $request->get("start");
        $length = $request->get("length"); // Rows display per page

        $type = $request->get('type');
        
        $search_arr = $request->get('search');
        $searchValue = $search_arr['value']; // Search value

        // Total records
        $listrep = BalanceView::join('users','balance_summary.member_id','=','users.id')->orderBy('balance_summary.balance','desc');
        
        if($searchValue != null)
        {
            $listrep = $listrep->where('users.username','=',$searchValue)->orWhere(DB::raw('CONCAT(users.firstname," ",users.lastname)'),'like','%'.$searchValue.'%');
        }
                      
        $totalRecords = $listrep->count();
        
        $totalRecordswithFilter = $totalRecords;
        
        $records = $listrep->select('balance_summary.member_id', 'balance_summary.balance', 'balance_summary.tcredit', 'balance_summary.tdebit')
                           ->with(array('member'=>function($query){
                                $query->select('id', 'username', 'firstname', 'lastname');
                            }))   
                           ->skip($start)
    					   ->take($length)
                           ->get(); 
        // Fetch records
        $data_arr = array();
         
        foreach($records as $record){ 
            $data_arr[] = array(
                "id" => $record->member_id,
                "username" => obscureAddress($record->member->username),
                "name" => $record->member->firstname.' '.$record->member->lastname,
                "balance" => $record->balance,
                "tcredit" => $record->tcredit,
                "tdebit" => $record->tdebit
            );
        }
        
        $response = array(
            "draw" => intval($draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecordswithFilter,
            "data" => $data_arr
        );
    
        return json_encode($response);
    }
}
