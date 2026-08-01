<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Session;
use App\Jobs\PostActivationWork;

use App\Models\Member;
use App\Models\MemberInfo;
use App\Models\EWalletLog;

use Carbon\Carbon;
use Log;
use Mail;
use Yajra\DataTables\DataTables;
use DB;

class WalletController extends Controller
{
    public function walletstatusrep(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'mobile' => 'required',
                'wallet' => 'required',
            ]);

            if($v->fails())
            {
				return response()->json(array('success'=>false,'error_code'=> 'INVALID_REQUEST_DATA'), 200);
            }

            $mobile = $request->get('mobile');
            $wallet = $request->get('wallet');
            
            $member = Member::where('username',$mobile)->first();
            if($member == null){
                return response()->json(array('success'=>false,'error_code'=> 'Invalid user mobile no'), 200);
            }
            
            $member_info = MemberInfo::where('member_id',$member->id)->first();
            $name = ($member_info == null ? '' : $member_info->name);
            
            $cradit = EWalletLog::where('member_id',$member->id)->where('type_id',1)->where('income_type',$wallet)->where('assign_withdrawal','!=',3)->sum('amount');
            
            $debit  = EWalletLog::where('member_id',$member->id)->where('type_id',2)->where('income_type',$wallet)->where('assign_withdrawal','!=',3)->sum('amount');
            
            $balance = number_format((float)$cradit-$debit, 2, '.', '');
            
            $data = EWalletLog::where('member_id',$member->id)->where('income_type',$wallet)->get();

			return response()->json(array('success'=> true, 'error_code'=> '', 'name'=>$name, 'cradit'=>($cradit == null ? 0 : $cradit), 'debit'=>($debit == null ? 0 : $debit), 'balance'=>$balance, 'data'=>$data), 200);
        }catch(\Exception $exception){
            Log::error($exception);
            return response()->json(array('success'=>false,'error_code'=> 'UNEXPECTED_ERROR_OCCURED'), 200);
        }
    }
    
    public function walletcreditdebit(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'mobile' => 'required',
                'amount' => 'required',
                'type' => 'required',
                'remark' => 'required',
                'wallet' => 'required',
            ]);

            if($v->fails())
            {
				return response()->json(array('success'=>false,'error_code'=> 'INVALID_REQUEST_DATA'), 200);
            }

            $mobile = $request->get('mobile');
            $amount = $request->get('amount');
            $type = $request->get('action');
            $remark = $request->get('remark');
            $wallet = $request->get('wallet');
            
            $member = Member::where('username',$mobile)->first();
            if($member == null){
                return response()->json(array('success'=>false,'error_code'=> 'Invalid user mobile no'), 200);
            }
            
            $object = new EWalletLog;
            $object->member_id = $member->id;
            $object->description = $remark;
            $object->amount = $amount;
            $object->type_id = $type;
            $object->income_type = 0;
            $object->save();
           
			return response()->json(array('success'=> true, 'error_code'=> ''), 200);
        }catch(\Exception $exception){
            Log::error($exception);
            return response()->json(array('success'=>false,'error_code'=> 'UNEXPECTED_ERROR_OCCURED'), 200);
        }
    }
    
    public function walletdelete(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'log_id' => 'required',
            ]);

            if($v->fails())
            {
				return response()->json(array('success'=>false,'error_code'=> 'INVALID_REQUEST_DATA'), 200);
            }

            $log_id = $request->get('log_id');
            
            $log = EWalletLog::where('id',$log_id)->first();
            if($log == null){
                return response()->json(array('success'=>false,'error_code'=> 'Invalid record found'), 200);
            }
            $log->delete();
            
			return response()->json(array('success'=> true, 'error_code'=> ''), 200);
        }catch(\Exception $exception){
            Log::error($exception);
            return response()->json(array('success'=>false,'error_code'=> 'UNEXPECTED_ERROR_OCCURED'), 200);
        }
    }
}
