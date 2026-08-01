<?php

namespace App\Http\Controllers\Users;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Hash;
use Log;

class SigninController extends Controller
{
    //
    public function signin(){
        $page_titel = 'Sign In';
        return view('users.sign-in', compact('page_titel'));
    }
    
    public function forgotpass(){
        $page_titel = 'Forgot Password';
        return view('users.forgot-pass', compact('page_titel'));
    }

    public function submitSigninOld(Request $request){
        try{
            $request->validate([
                'username' => 'required',
                'password' => 'required|min:6'
            ]);

            $data = $request->all();

            $credentials = $request->only('username', 'password');

            if(Auth::attempt($credentials))
            {
                $userStatus = Auth::User()->status;
                
                if($userStatus == '1') 
                {
                    Auth::logout();
                    return response()->json(array('success'=> false,'error'=> 'Invalid login credential.'), 200);
                }
                
                return response()->json(array('success'=> true, 'error'=> ''), 200);
            }
            else
            {
                return response()->json(array('success'=> false,'error'=> 'Invalid login credential.'), 200);
            }
        }catch(\Exception $exception){
            Log::error($exception);
            return response()->json(array('success'=> false,'error'=> 'Invalid request data send.'), 200);
        }
    }

    public function submitSignin(Request $request) {
        try {
            $request->validate([
                'wallet' => 'required',
            ]);

            $data = $request->all();
            
            $key = 'login-attempts:' . $request->ip();

            if (RateLimiter::tooManyAttempts($key, 5)) {
                return response()->json(array('success' => false, 'error' => 'Too many login attempts. Try again later.'), 200);
            }
        
            RateLimiter::hit($key, 300);

            $user = User::where('username', 'like', $data["wallet"])->where('status', '=', 0)->first();

            if($user != null)
            {
                Auth::guard('web')->login($user);
                
                return response()->json(array('success'=> true, 'error'=> ''), 200);
            }
            else
            {
                return response()->json(array('success'=> false,'error'=> 'Invalid login credential.'), 200);
            }
        } catch(\Exception $exception) {
            Log::error($exception);
            return response()->json(array('success'=> false,'error'=> 'Invalid request data send.'), 200);
        }
    }
        
    public function submitForgotPasswordOld(Request $request){
        try{
            $request->validate([
                'username' => 'required',
            ]);

            $data = $request->all();
            
            $password = rand(111111,999999); 
            
            $user = User::where('username',$data["username"])->first();
            if($user == null){
                return response()->json(array('success'=> false,'error'=> 'Invalid username.'), 200);
            }
            $user->password = Hash::make($password);
            $user->save();
            
            $emailrCon = app('App\Http\Controllers\EmailController');
            // $send = $emailrCon->sendForgotpassword($user->email, $user->username, $password);
            
            return response()->json(array('success'=> true, 'error'=> ''), 200);
        }catch(\Exception $exception){
            Log::error($exception);
            return response()->json(array('success'=> false,'error'=> 'Invalid request data send.'), 200);
        }
    }
}
