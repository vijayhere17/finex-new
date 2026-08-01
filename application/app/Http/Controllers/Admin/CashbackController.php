<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Session;
use App\Jobs\PostActivationWork;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

use Carbon\Carbon;
use Log;
use Mail;
use Yajra\DataTables\DataTables;
use DB;

class CashbackController extends Controller
{
	public function index(){
		$page_titel = "Set Cashback & Report";
        return view('admin.set-cashback-report', compact('page_titel'));
    }
    
    public function setCashback(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'assigned_to' => 'required',
            ]);

            if($v->fails())
            {
				return response()->json(array('success'=>false,'error_code'=> 'INVALID_REQUEST_DATA'), 200);
            }
            
            $walletCon = app('App\Http\Controllers\Users\EarningWalletController');

            $username = $request->assigned_to;

            $check = Auth::user();
			if($check == null){
				return response()->json(array('success'=>false,'error_code'=> 'SESSION_INVALID'), 200);
			}         

            $user = User::where('username',$username)->first();
            if($user != null){
                if($user->kit_id == 0){
                    return response()->json(array('success'=>false,'error_code'=> 'Member is not active.'), 200);
                }
                if($user->is_cashback == 1){
                    return response()->json(array('success'=>false,'error_code'=> 'Already redeem cashback.'), 200);
                }
                
                $coin_rate = getcoinrate();
                
                $user->is_cashback = 1;
                $user->dmc_status = 1;
                $user->admin_cashback = 1;
                $user->save();
                
                $description = 'Earned Cashback'; $created_at = date("Y-m-d H:i:s");
                $walletCon->addearningwalletlog($user->id, 1, 3, $description, $user->self_investment, $coin_rate, formatdecimal($user->self_investment/$coin_rate, 8), $created_at);  
                
                return response()->json(array('success'=> true, 'error_code'=> ''), 200);
            }else{
                return response()->json(array('success'=>false,'error_code'=> 'INVALID_MEMBER'), 200); 
            }
        }catch(\Exception $exception){
            Log::error($exception);
            return response()->json(array('success'=>false,'error_code'=> 'UNEXPECTED_ERROR_OCCURED'), 200);
        }
    }
    
	public function getCashbackUserReport(Request $request){
        $draw = $request->get('draw');
        $start = $request->get("start");
        $length = $request->get("length"); // Rows display per page
        
        $search_arr = $request->get('search');
        $searchValue = $search_arr['value']; // Search value

        $status = $request->get('status');
    
        // Total records
        $listreport = User::where('is_cashback','>',0)->orderBy('created_at','desc');
        
        if($searchValue != null){
            $listreport = $listreport->where('username','=',$searchValue)->orWhere(DB::raw('CONCAT(firstname," ",lastname)'),'like','%'.$searchValue.'%')->orWhere('email','like','%'.$searchValue.'%')->orWhere('mobile','like','%'.$searchValue.'%');
        }
                      
        $totalRecords = $listreport->count();
        
        $totalRecordswithFilter = $totalRecords;
        
        $records = $listreport->select('id', 'referral_id', 'username', 'firstname', 'lastname', 'email', 'mobile', 'leg', 'kit_id', 'self_investment', 'activation_date', 'admin_cashback', 'created_at')
                              ->skip($start)
    						  ->take($length)
                              ->get(); 
        // Fetch records
        $data_arr = array();
         
        foreach($records as $record){ 
            $data_arr[] = array(
                "id" => $record->id,
                "username" => $record->username,
                "name" => $record->firstname.' '.$record->lastname,
                "email" => $record->email,
                "mobile" => $record->mobile,
                "total_stake" => $record->self_investment,
                "activation" => ($record->kit_id > 0 ? '<b style="color: green;">Active</b>' : '<b style="color: red;">Inactive</b>'),
                "activation_on" => ($record->activation_date == null ? '' : date("d/m/Y H:i A", strtotime($record->activation_date))),
                "signup_on" => date("d/m/Y H:i A", strtotime($record->created_at)),
                "process_on" => ($record->admin_cashback == 0 ? '<b style="color: green;">System</b>' : '<b style="color: orange;">Admin</b>'),
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
