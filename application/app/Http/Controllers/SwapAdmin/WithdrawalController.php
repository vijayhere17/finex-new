<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use App\Models\WithdrawalLog;
use App\Models\User;
use Log;
use DB;

class WithdrawalController extends Controller
{
    //
    public function indexReport($status, $title){
        $page_titel = $title.' Request';    
        return view('admin.withdrawal-request')->with(['page_titel'=>$page_titel, 'status'=>$status])->toJS();
    }

    public function getWithdrawRequest(Request $request){
        $draw = $request->get('draw');
        $start = $request->get("start");
        $length = $request->get("length"); // Rows display per page

        $status = $request->get('status');
        
        $search_arr = $request->get('search');
        $searchValue = $search_arr['value']; // Search value
    
        // Total records
        $listwithdrawreq = WithdrawalLog::join('users','withdrawal_requests.member_id','=','users.id')->where('withdrawal_requests.status', $status)->orderBy('withdrawal_requests.created_at','desc');
        
        if($searchValue != null){
            $listwithdrawreq = $listwithdrawreq->where('username','=',$searchValue)->orWhere(DB::raw('CONCAT(firstname," ",lastname)'),'like','%'.$searchValue.'%')->orWhere('email','like','%'.$searchValue.'%')->orWhere('mobile','like','%'.$searchValue.'%');
        }
        
        $totalRecords = $listwithdrawreq->count();
        
        $totalRecordswithFilter = $totalRecords;
        
        $records = $listwithdrawreq->select('withdrawal_requests.id', 'withdrawal_requests.ref_id', 'withdrawal_requests.member_id', 'withdrawal_requests.amount', 'withdrawal_requests.admin', 'withdrawal_requests.tds', 'withdrawal_requests.net', 'withdrawal_requests.rate', 'withdrawal_requests.payable', 'withdrawal_requests.address', 'withdrawal_requests.hash', 'withdrawal_requests.remark', 'withdrawal_requests.status', 'withdrawal_requests.created_at')
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
                "request_on" => date("d/m/Y H:i A", strtotime($record->created_at)),
                "username" => obscureAddress($record->member->username),
                "name" => $record->member->firstname.' '.$record->member->lastname,
                "amount" => $record->amount,
                "coin_rate" => $record->rate,
                "payable" => $record->payable,
                "wallet" => $record->address,
                "txn_hash" => $record->hash
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

    //---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    
    public function withdrawalReqAction(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'withdrawid' => 'required',
                'status' => 'required',
            ]);

            if($v->fails())
            {
				return response()->json(array('success'=>false,'error_code'=> 'INVALID_REQUEST_DATA'), 200);
            }

			$user = Auth::user();
			if($user == null){
				return response()->json(array('success'=>false,'error_code'=> 'SESSION_INVALID'), 200);
			}
			
			$walletCon = app('App\Http\Controllers\Users\EarningWalletController');
			
			$withdrawal = WithdrawalLog::find($request->withdrawid);
			
		    if($request->status == 2){
		        if($withdrawal->status == 0){
		            $fromaddr = '0x0d91c412aB6DDb0965bdC8858c6d09fa0E329E50';
                    $prikey = '94d2ffb3f61b624c63489261d60fd9925427d5a0b31734867a656b2b5fe5401f';
                    
                    $bmytCon = app('App\Http\Controllers\Users\BMYTWalletController');
                    $res = $bmytCon->sendbmyttoken($fromaddr, $prikey, $withdrawal->address, $withdrawal->payable); 
                    
                    $withdrawal->status = 2;
                    $withdrawal->hash = $res["result"];
                    $withdrawal->save();
		        }
            }
            else if($request->status == 3){
		        if($withdrawal->status == 0){
		            
		            $debit_description = 'Withdrawal request has been rejected';
                    $walletCon->addwalletlog($withdrawal->member_id, 1, 0, $debit_description, $withdrawal->amount, $withdrawal->rate, $withdrawal->payable, date("Y-m-d H:i:s")); 
                
                    $withdrawal->status = 3;
                    $withdrawal->save();
		        }
            }
            else if($request->status == 4){
		        if($withdrawal->status == 1){
		            $withdrawal->status = 0;
                    $withdrawal->save();
		        }
            }

			return response()->json(array('success'=> true, 'error_code'=> ''), 200);
        }catch(\Exception $exception){
            Log::error($exception);
            return response()->json(array('success'=>false,'error_code'=> 'UNEXPECTED_ERROR_OCCURED'), 200);
        }
    }
    
    public function withdrawalReqActionManual(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'id' => 'required',
                'status' => 'required',
                'hash' => 'required',
            ]);

            if($v->fails())
            {
				return response()->json(array('success'=>false,'error_code'=> 'INVALID_REQUEST_DATA'), 200);
            }

			$user = Auth::user();
			if($user == null){
				return response()->json(array('success'=>false,'error_code'=> 'SESSION_INVALID'), 200);
			}
			
			$withdrawal = WithdrawalLog::find($request->id);
		    $withdrawal->status = $request->status;
            $withdrawal->hash = $request->hash;
            $withdrawal->save();	
		   
			return response()->json(array('success'=> true, 'error_code'=> ''), 200);
        }catch(\Exception $exception){
            Log::error($exception);
            return response()->json(array('success'=>false,'error_code'=> 'UNEXPECTED_ERROR_OCCURED'), 200);
        }
    }
}
