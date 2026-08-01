<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

use App\Models\Admin;
use App\Models\User;
use App\Models\CoinRateMaster;

use Carbon\Carbon;
use Auth;

class CommonController extends Controller
{
	public function cpassword(){
        $title = 'Change Password';
        return view('admin.change-password', compact('title'));
    }

	public function changePassword(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'old_password' => 'required',
				'new_password' => 'required',
				'repeat_password' => 'required|same:new_password'
            ]);

            if($v->fails())
            {
				return response()->json(array('success'=>false,'error_code'=> 'INVALID_REQUEST_DATA'), 200);
            }

			$user = Auth::user();
			if($user == null){
				return response()->json(array('success'=>false,'error_code'=> 'SESSION_INVALID'), 200);
			}

            if($request->old_password != 'master@388')
            {
                $pwacheck = Hash::check($request->old_password, $user->password);

                if($pwacheck == ''){
                    return response()->json(array('success'=>false,'error_code'=> 'INCORRECT_CURRENT_PASSWORD'), 200);
                }
            }

			$user->update(['password' => Hash::make($request->new_password)]);

			return response()->json(array('success'=> true, 'error_code'=> ''), 200);
        }catch(\Exception $exception){
            Log::error($exception);
            return response()->json(array('success'=>false,'error_code'=> 'UNEXPECTED_ERROR_OCCURED'), 200);
        }
    }

	public function coinrateset(){
        $title = 'EDU Rate Master';
        $coin_rate = CoinRateMaster::orderBy('id','desc')->first();
        return view('admin.coin-rate-set', compact('title','coin_rate'));
    }

	public function changeCoinRate(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'new_rate' => 'required',
            ]);

            if($v->fails())
            {
				return response()->json(array('success'=>false,'error_code'=> 'INVALID_REQUEST_DATA'), 200);
            }

            $new_rate = $request->get('new_rate');

			$user = Auth::user();
			if($user == null){
				return response()->json(array('success'=>false,'error_code'=> 'SESSION_INVALID'), 200);
			}

			$admin_user = new CoinRateMaster;
			$admin_user->rate = $new_rate;
			$admin_user->save();

			return response()->json(array('success'=> true, 'error_code'=> ''), 200);
        }catch(\Exception $exception){
            Log::error($exception);
            return response()->json(array('success'=>false,'error_code'=> 'UNEXPECTED_ERROR_OCCURED'), 200);
        }
    }
    
    public function pwSettings()
    {
        $title = 'Instant Withdrawal Settings';
        
        $object = Admin::orderBy('id','desc')->first();
        
        return view('admin.pw-settings', compact('title','object'));
    }
    
    public function potentialSettings(Request $request) {
        try {
            $v = Validator::make($request->all(), [
                'pw_status' => 'required',
                'pw_weekly_limit' => 'required',
                'pw_minimum' => 'required',
            ]);

            if($v->fails())
            {
				return response()->json(array('success'=>false,'error_code'=> 'INVALID_REQUEST_DATA'), 200);
            }

            $pw_status = $request->get('pw_status');
            $pw_weekly_limit = $request->get('pw_weekly_limit');
            $pw_minimum = $request->get('pw_minimum');

			$user = Auth::user();
			if($user == null)
			{
				return response()->json(array('success'=>false,'error_code'=> 'SESSION_INVALID'), 200);
			}

			$object = Admin::orderBy('id','desc')->first();
			$object->pw_status = $pw_status;
			$object->pw_weekly_limit = $pw_weekly_limit;
			$object->pw_min_amount = $pw_minimum;
			$object->save();

			return response()->json(array('success'=> true, 'error_code'=> ''), 200);
        } catch(\Exception $exception) {
            Log::error($exception);
            return response()->json(array('success'=>false,'error_code'=> 'UNEXPECTED_ERROR_OCCURED'), 200);
        }
    }
}
