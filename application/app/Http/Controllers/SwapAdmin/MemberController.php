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

class MemberController extends Controller
{
	public function index(){
		$page_titel = "All User Report";
        return view('admin.member-report', compact('page_titel'));
    }
    
    public function editmember($id){
		$page_titel = "Edit Member";
		$member = User::find($id);
        return view('admin.edit-member', compact('page_titel','member'));
    }

	public function getMemberReport(Request $request){
        $draw = $request->get('draw');
        $start = $request->get("start");
        $length = $request->get("length"); // Rows display per page
        
        $search_arr = $request->get('search');
        $searchValue = $search_arr['value']; // Search value

        $status = $request->get('status');
    
        // Total records
        $listreport = User::orderBy('created_at','desc');
        
        if($searchValue != null){
            $listreport = $listreport->where('username','=',$searchValue)->orWhere(DB::raw('CONCAT(firstname," ",lastname)'),'like','%'.$searchValue.'%')->orWhere('email','like','%'.$searchValue.'%')->orWhere('mobile','like','%'.$searchValue.'%');
        }
                      
        $totalRecords = $listreport->count();
        
        $totalRecordswithFilter = $totalRecords;
        
        $records = $listreport->select('id', 'referral_id', 'username', 'firstname', 'lastname', 'email', 'mobile', 'leg', 'kit_id', 'activation_date', 'created_at')
                              ->with(array('referral'=>function($query){
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
                "username" => obscureAddress($record->username),
                "name" => $record->firstname.' '.$record->lastname,
                "email" => $record->email,
                "mobile" => $record->mobile,
                "position" => ($record->leg == 'L' ? 'Left' : 'Right'),
                "activation" => ($record->kit_id > 0 ? '<b style="color: green;">Active</b>' : '<b style="color: red;">Inactive</b>'),
                "activation_on" => ($record->activation_date == null ? '' : date("d/m/Y H:i A", strtotime($record->activation_date))),
                "signup_on" => date("d/m/Y H:i A", strtotime($record->created_at)),
                "referral" => ($record->referral == null ? '' : $record->referral->firstname.' '.$record->referral->lastname.' #'.obscureAddress($record->referral->username))
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

    public function backLogin(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'id' => 'required',
            ]);

            if($v->fails())
            {
				return response()->json(array('success'=>false,'error_code'=> 'INVALID_REQUEST_DATA'), 200);
            }

            $id = $request->id;

            $check = Auth::user();
			if($check == null){
				return response()->json(array('success'=>false,'error_code'=> 'SESSION_INVALID'), 200);
			}         

            $user = User::find($id);

            if($user != null){
                Auth::guard('web')->login($user);

                return response()->json(array('success'=> true, 'error_code'=> ''), 200);
            }else{
                return response()->json(array('success'=>false,'error_code'=> 'INVALID_MEMBER'), 200); 
            }
        }catch(\Exception $exception){
            Log::error($exception);
            return response()->json(array('success'=>false,'error_code'=> 'UNEXPECTED_ERROR_OCCURED'), 200);
        }
    }
    
    public function updateMemberDetails(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'member_id' => 'required',
            ]);

            if($v->fails())
            {
				return response()->json(array('success'=>false,'error_code'=> 'INVALID_REQUEST_DATA'), 200);
            }

            $member_id = $request->member_id;
            
            $password = $request->password;
            
            $firstname = $request->firstname;
            $lastname = $request->lastname;
            $status = $request->status;
            $w_status = $request->w_status;

            $check = Auth::user();
			if($check == null){
				return response()->json(array('success'=>false,'error_code'=> 'SESSION_INVALID'), 200);
			}         

            $user = User::find($member_id);

            if($user != null){
                
                if($password != null){
                    $user->password = Hash::make($password);
                }
                
                $user->firstname = $firstname;
                $user->lastname = $lastname;
                $user->status = $status;
                $user->w_status = $w_status;
                $user->save();
 
                return response()->json(array('success'=> true, 'error_code'=> ''), 200);
            }else{
                return response()->json(array('success'=>false,'error_code'=> 'INVALID_MEMBER'), 200); 
            }
        }catch(\Exception $exception){
            Log::error($exception);
            return response()->json(array('success'=>false,'error_code'=> 'UNEXPECTED_ERROR_OCCURED'), 200);
        }
    }
}
