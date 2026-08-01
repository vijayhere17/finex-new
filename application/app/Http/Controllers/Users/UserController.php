<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Hash;
use Log;
use Cookie;

class UserController extends Controller
{
    //
    public function index(){
        $page_titel = 'Update My Profile';       
        return view('users.my-profile')->with(['page_titel'=>$page_titel, 'user'=>Auth::user()])->toJS();
    }

    public function restpass(){
        $page_titel = 'Update Login Password';       
        return view('users.reset-password')->with(['page_titel'=>$page_titel]);
    }
    
    public function secureaccount(){
        $page_titel = 'Secure Account (GAuth)';       
        
        $user = Auth::user();
        
        if($user->is_authenticator == 0)
        {
            $secret_res = $this->createGoogleAuthQrCode($user->username);
            $data = json_decode($secret_res, true);
            
            if($data["status"] == '200')
            {
                $qrcode = $data["qrcode"];
                
                $secret = $data["secret"];
            }
            else
            {
                $qrcode = '#';
                
                $secret = '';
            }
        }
        else
        {
            $qrcode = '#';
                
            $secret = '';
        }
        
        $is_setup = $user->is_authenticator;
        
        return view('users.secure-account')->with(['page_titel'=>$page_titel, 'is_setup'=>$is_setup, 'qrcode'=>$qrcode, 'secret'=>$secret]);
    }

    public function submitUpdateProfile(Request $request){
        try{
            $request->validate([
                // 'firstname' => 'required',
                // 'lastname' => 'required',
                // 'email' => 'required',
                // 'mobile' => 'required',
                // 'status' => 'required'
            ]);

            $data = $request->all();

			if(Auth::user() == null){
				return response()->json(array('success'=>false,'error'=> 'Session is expired.'), 200);
			}
			
			$id = Auth::user()->id;
			
			if ($data["status"] == 'true') {
			    if (Auth::user()->kit_id > 0) {
			        if (Auth::user()->is_authenticator == 0) {
    			        // return response()->json(array('success'=> false, 'error'=>'Please enable google auth to secure account.'), 200);  
    			    }
			        
    			    if (Auth::user()->is_authenticator == 1) {
    			        $cjson = $this->checkGoogleAuthCode(Auth::user()->google_secret, $data["otp"]);
                        $jdata = json_decode($cjson, true);
                        
                        if ($jdata["status"] == 500) {
                            return response()->json(array('success'=> false, 'error'=>$jdata["message"]), 200);  
                        }
    			    }
			    }
			    
			    /* if($data["otp"] != '346789')
                {
                    if(Cookie::get('profileotp') == null){
                       return response()->json(array('success'=>false,'error'=> 'OTP is expire.'), 200); 
                    }
                    
                    if(Cookie::get('profileotp') != $data["otp"]){
                       return response()->json(array('success'=>false,'error'=> 'Invalid profile OTP.'), 200); 
                    }
                }
                
                Cookie::forget('profileotp'); */

                $user = User::find($id);  
                $user->firstname = $data['firstname'];
                $user->lastname = $data['lastname'];
                $user->email = $data['email'];
                $user->save();
                
			} else {
			    $generate_otp = rand(111111,999999);
                
                $otptime = time() + 60 * 15;
                
                Cookie::queue('profileotp',  $generate_otp, $otptime);
                
                $emailCon = app('App\Http\Controllers\EmailController');
                
                $subject = 'Update Profile OTP';
                
                // $emailCon->sendOTPMaster($subject, Auth::user()->email, Auth::user(), $generate_otp);
                
                return response()->json(array('success'=> true, 'error'=> ''), 200);  
			}
			
            return response()->json(array('success'=> true, 'error'=> ''), 200);
        }catch(\Exception $exception){
            Log::error($exception);
            return response()->json(array('success'=>false,'error'=> 'Invalid request data send'), 200);
        }
    }

    public function submitUpdatePassword(Request $request){
        try{
            $request->validate([
                'old_password' => 'required',
				'new_password' => 'required',
				'con_password' => 'required|same:new_password'
            ]);

            $user = Auth::user();

			if($user == null){
				return response()->json(array('success'=>false,'error'=> 'Session is expired.'), 200);
			}

            $pwacheck = Hash::check($request->old_password, $user->password);

			if($pwacheck == ''){
				return response()->json(array('success'=>false,'error'=> 'Invalid old password.'), 200);
			}

			$user->update(['password' => Hash::make($request->new_password)]);
    
            return response()->json(array('success'=> true, 'error'=> ''), 200);
        }catch(\Exception $exception){
            Log::error($exception);
            return response()->json(array('success'=>false,'error'=> 'Invalid request data send.'), 200);
        }
    }
    
    public function submit2Fa(Request $request){
        try{
            $request->validate([
                'secret' => 'required',
				'code' => 'required'
            ]);

            $user = Auth::user();

			if($user == null){
				return response()->json(array('success'=>false,'error'=> 'Session is expired.'), 200);
			}

            $cjson = $this->checkGoogleAuthCode($request->secret, $request->code);
            $jdata = json_decode($cjson, true);
            
            if($jdata["status"] == 200)
            {
                $user->is_authenticator = 1;
                $user->google_secret = $request->secret;
                $user->save();
                
                return response()->json(array('success'=> true, 'error'=> ''), 200);
            } 
            else
            {
                return response()->json(array('success'=>false,'error'=> $jdata["message"]), 200);
            }
        } catch(\Exception $exception) {
            Log::error($exception);
            return response()->json(array('success'=>false,'error'=> 'Invalid request data send.'), 200);
        }
    }
    
    // ==============================================================================================================================================================================================
    
    public function createGoogleAuthQrCode($email)
    {
        $curl = curl_init();
    
        curl_setopt_array($curl, array(
            CURLOPT_URL => env("APP_URL")."/public/gauth/index.php?gmail=".$email,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "postman-token: f480ef20-199d-0aa4-07e0-160d8d4a2d3c"
            ),
        ));
    
        $response = curl_exec($curl);
        $err = curl_error($curl);
    
        curl_close($curl);
    
        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }
    
    public function checkGoogleAuthCode($secret, $code)
    {
        $curl = curl_init();
    
        curl_setopt_array($curl, array(
            CURLOPT_URL => env("APP_URL")."/public/gauth/check.php?secret=".$secret.'&code='.$code,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "postman-token: f480ef20-199d-0aa4-07e0-160d8d4a2d3c"
            ),
        ));
    
        $response = curl_exec($curl);
        $err = curl_error($curl);
    
        curl_close($curl);
    
        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }
}
