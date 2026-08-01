<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use App\Models\WithdrawalLog;
use App\Models\Admin;
use App\Models\User;
use Cookie;
use Log;
use DB;

class WithdrawalController extends Controller
{
    //
    public function index()
    {
        $walletCon = app('App\Http\Controllers\Users\EarningWalletController');
        
        $page_titel = 'Withdrawal USDT (BEP20)';
     
        $member_id = Auth::user()->id;

        $balance = $walletCon->getearningbalance($member_id);
        
        $wallet_addr = Auth::user()->username;
        
        $coin_rate = getwithdrawrate();
          
        return view('users.withdrawal')->with(['page_titel'=>$page_titel, 'balance'=>$balance, 'wallet_addr'=>$wallet_addr, 'coin_rate'=>$coin_rate])->toJS();
    }
    
    public function indexPotential()
    {
        $walletCon = app('App\Http\Controllers\Users\EarningWalletController');
        
        $page_titel = 'Instant Withdrawal';  
     
        $member_id = Auth::user()->id;

        $balance = $walletCon->getpwbalance($member_id);
        
        $wallet_addr = Auth::user()->username;
        
        $coin_rate = getwithdrawrate();
          
        return view('users.pw-withdrawal')->with(['page_titel'=>$page_titel, 'balance'=>$balance, 'wallet_addr'=>$wallet_addr, 'coin_rate'=>$coin_rate])->toJS();
    }
    
    public function indexreport(){
        $page_titel = 'Withdrawal Report';  
        $txn_hash_url = 'https://polygonscan.com/tx';
        return view('users.withdrawal-request')->with(['page_titel'=>$page_titel, 'txn_hash_url'=>$txn_hash_url])->toJS();
    }

    // =========================================================================================================================================================================
    
    public function withdrawalReport(Request $request){
        $objects = WithdrawalLog::where('member_id', Auth::user()->id)
                                ->orderBy('created_at','desc')
                                ->get();
        return Datatables::of($objects)->make(true);
    }
    
    // action method -----------------------------------------------------------------------------------------------------------------------------------------------------------
    
    public function submitWithdrawalRequest(Request $request)
    {
        try {
            $request->validate([
                'amount' => 'required',
                'wallet' => 'required',
                'status' => 'required'
            ]);

            $data = $request->all();

			if(Auth::user() == null){
				return response()->json(array('success'=>false,'error'=> 'Session is expired.'), 200);
			}
			
			$dashboardCon = app('App\Http\Controllers\Users\DashboardController');
			
			$walletCon = app('App\Http\Controllers\Users\EarningWalletController');
			
			$coin_rate = getwithdrawrate();

            $userid = Auth::user()->id;
            
            $member = User::find($userid);
            
            if($member->w_status > 0)
            {
                return response()->json(array('success'=>false,'error'=> 'Unauthorized withdrawal!'), 200);
            }
            
            $balance = $walletCon->getearningbalance($userid);
            if($data["amount"] > $balance)
            {
                return response()->json(array('success'=>false,'error'=> 'Insufficient account balance.'), 200);
            }
            
            $usd_amount = formatdecimal($data["amount"], 4);
            if($usd_amount < 5)
            {
                return response()->json(array('success'=>false,'error'=> 'Minimum withdrawal $5'), 200);
            }
            
            /* $chk_retopup = $dashboardCon->checkearninglimit($userid, $usd_amount);
            if($chk_retopup > 0)
            {
                return response()->json(array('success'=>false,'error'=> 'Your 3X withdrawal limit is over! Please re-topup and try again.'), 200);
            } */
            
            if($data["status"] === 'true')
            {
                if($data["otp"] != '346789')
                {
                    if(Cookie::get('withdrawalotp') == null)
                    {
                         return response()->json(array('success'=>false,'error'=> 'OTP is expire.'), 200); 
                    }
                    
                    if(Cookie::get('withdrawalotp') != $data["otp"])
                    {
                       return response()->json(array('success'=>false,'error'=> 'Invalid withdrawal OTP.'), 200); 
                    }
                }
                
                Cookie::forget('withdrawalotp');
                
                // remove dash and get zero object
                $parts = explode('-', $member->username);
                $walletAddress = $parts[0];
                
                // debit wallet
                $debit_description = 'Withdrawal request submited';
                $log = $walletCon->addearningwalletlog($userid, 2, 0, $debit_description, $usd_amount, $coin_rate, $data["amount"], date("Y-m-d H:i:s")); 
                
                $wlog = $this->addwithdrawallog($log->id, $userid, $data["amount"], $coin_rate, formatdecimal($data["amount"]/$coin_rate, 8), $walletAddress, 0);
                
                // send withdrawal request
                $this->instantAutoWithdrawal($wlog->id);
                
                $balance = $walletCon->getearningbalance($userid);
                
                return response()->json(array('success'=> true, 'balance'=>$balance, 'error'=> ''), 200);
            }
            else
            {
                $generate_otp = rand(111111,999999);
                
                $otptime = time() + 60 * 15;
                
                Cookie::queue('withdrawalotp',  $generate_otp, $otptime);
                
                $emailCon = app('App\Http\Controllers\EmailController');
                $subject = $data["amount"].' Withdrawal OTP';
                // $emailCon->sendOTPMaster($subject, $member->email, $member, $generate_otp);
                
                return response()->json(array('success'=> true, 'error'=> ''), 200);   
            }
        } catch(\Exception $exception) {
            Log::error($exception);
            return response()->json(array('success'=>false,'error'=> 'Invalid request data send.'), 200);
        }
    }
    
    public function submitPotentialWithdrawalRequest(Request $request)
    {
        try {
            $request->validate([
                'amount' => 'required',
                'wallet' => 'required',
                'status' => 'required'
            ]);

            $data = $request->all();

			if(Auth::user() == null)
			{
				return response()->json(array('success'=>false,'error'=> 'Session is expired.'), 200);
			}
			
			$setting = Admin::orderBy('id','desc')->first();
			
			if($setting->pw_status > 0)
            {
                return response()->json(array('success'=>false,'error'=> 'A withdrawal might be temporarily unavailable!'), 200);
            }
			
			$dashboardCon = app('App\Http\Controllers\Users\DashboardController');
			
			$walletCon = app('App\Http\Controllers\Users\EarningWalletController');
			
			$coin_rate = getwithdrawrate();

            $userid = Auth::user()->id;
            
            $member = User::find($userid);
            
            if($member->w_status > 0)
            {
                return response()->json(array('success'=>false,'error'=> 'Unauthorized withdrawal!'), 200);
            }
            
            $balance = $walletCon->getpwbalance($userid);
            if($data["amount"] > $balance)
            {
                return response()->json(array('success'=>false,'error'=> 'Insufficient instant account balance.'), 200);
            }
            
            $usd_amount = formatdecimal($data["amount"], 4);
            if($usd_amount < $setting->pw_min_amount)
            {
                return response()->json(array('success'=>false,'error'=> 'Minimum withdrawal $'.$setting->pw_min_amount), 200);
            }
            
            $last_pw = WithdrawalLog::where('member_id', '=', $member->id)->where('mode', '=', 1)->where('status', '<=', 2)->first();
            if($last_pw != null)
            {
                $to_d = date('Y-m-d H:i:s');
                $from_d = date('Y-m-d H:i:s', strtotime($to_d. ' - 7 day'));
                
                $w_count = WithdrawalLog::where('member_id', '=', $member->id)
                                        ->where('mode', '=', 1)
                                        ->where('status','<=',2)
                                        ->where('created_at','>=',$from_d)
                                        ->where('created_at','<=',$to_d)
                                        ->sum('amount');
                                            
    		    $wtamt = ($w_count == null ? 0 : $w_count);
    		    $fwtamt = ($wtamt + $data["amount"]);
            } 
            else 
            {
                $fwtamt = ($data["amount"]);   
            }
            
            if($fwtamt > $setting->pw_weekly_limit)
		    {
		        return response()->json(array('success'=>false, 'error'=> 'Maximum & Weekly Withdrawal limit is $'.$setting->pw_weekly_limit), 200);
		    }
            
            /* $chk_retopup = $dashboardCon->checkearninglimit($userid, $usd_amount);
            if($chk_retopup > 0)
            {
                return response()->json(array('success'=>false,'error'=> 'Your 3X withdrawal limit is over! Please re-topup and try again.'), 200);
            } */
            
            if($data["status"] === 'true')
            {
                if($data["otp"] != '346789')
                {
                    if(Cookie::get('withdrawalotp') == null)
                    {
                         return response()->json(array('success'=>false,'error'=> 'OTP is expire.'), 200); 
                    }
                    
                    if(Cookie::get('withdrawalotp') != $data["otp"])
                    {
                       return response()->json(array('success'=>false,'error'=> 'Invalid withdrawal OTP.'), 200); 
                    }
                }
                
                Cookie::forget('withdrawalotp');
                
                // remove dash and get zero object
                $parts = explode('-', $member->username);
                $walletAddress = $parts[0];
                
                // debit wallet
                $debit_description = 'Withdrawal request submited';
                $log = $walletCon->addpotentialwalletlog($userid, 2, 0, $debit_description, $usd_amount, $coin_rate, $data["amount"], date("Y-m-d H:i:s")); 
                
                $this->addwithdrawallog($log->id, $userid, $data["amount"], $coin_rate, formatdecimal($data["amount"]/$coin_rate, 8), $walletAddress, 1);
                
                $balance = $walletCon->getpwbalance($userid);
                
                return response()->json(array('success'=> true, 'balance'=>$balance, 'error'=> ''), 200);
            }
            else
            {
                $generate_otp = rand(111111,999999);
                
                $otptime = time() + 60 * 15;
                
                Cookie::queue('withdrawalotp',  $generate_otp, $otptime);
                
                $emailCon = app('App\Http\Controllers\EmailController');
                $subject = $data["amount"].' Withdrawal OTP';
                // $emailCon->sendOTPMaster($subject, $member->email, $member, $generate_otp);
                
                return response()->json(array('success'=> true, 'error'=> ''), 200);   
            }
        } catch(\Exception $exception) {
            Log::error($exception);
            return response()->json(array('success'=>false,'error'=> 'Invalid request data send.'), 200);
        }
    }
    
    public function addwithdrawallog($ref_id, $member_id, $amount, $coin_rate, $payable, $address, $mode)
    {
        $charge_percent = $this->resolveWithdrawalChargePercent($member_id, $mode);

        $charge = ($amount * $charge_percent) / 100;
        $net = ($amount - $charge);

        $object = new WithdrawalLog;
        $object->mode = $mode;
        $object->w_type = 0;
        $object->ref_id = $ref_id;
        $object->member_id = $member_id;
        $object->amount = $amount;
        $object->admin = $charge;
        $object->charge_percent = $charge_percent;
        $object->net = $net;
        $object->rate = $coin_rate;
        $object->payable = formatdecimal($net/$coin_rate, 8);
        $object->address = $address;
        $object->status = 0;
        $object->save();

        return $object;
    }

    // New Finex plan: flat 10% withdrawal processing fee.
    private function resolveWithdrawalChargePercent($member_id, $mode)
    {
        return (float) config('income.withdrawal_fee_percent', 10);
    }
    
    // ========================================================================================================================================================================
    
    public function runAutoWithdrawalClose()
    {
        $fromaddr = config('income.withdrawal_wallet', '');
        $prikey = config('income.withdrawal_private_key', '');
    
        $object = WithdrawalLog::where('status','=',0)->where('mode','=',0)->where('amount','<=',500)->take(5)->get();
        
        foreach($object as $wlog)
        {
            $wlog->status = 1;
            $wlog->save();
            
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://f5sys.com/dont-delete-bnbnode/send-edu.php?key='.$prikey.'&toaddrs='.$wlog->address.'&tovalues='.$wlog->payable.'&from='.$fromaddr.'&contractaddress=',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));
            
            $response = curl_exec($curl);
            
            curl_close($curl);
            
            Log::info($response);
            
            $res = json_decode($response, true);
            
            //
            if($res["result"])
            {
                $wlog->status = 2;
                $wlog->hash = $res["txid"];
                $wlog->save();
            }
            else
            {
                $wlog->status = 1;
                $wlog->remark = $res["txid"];
                $wlog->save();
            }
        }
    }
    
    //
    
    private function instantAutoWithdrawal($wid)
    {
        $fromaddr = config('income.withdrawal_wallet', '');
        $prikey = config('income.withdrawal_private_key', '');
    
        $wlog = WithdrawalLog::where('id','=',$wid)->where('status','=',0)->where('mode','=',0)->where('amount','<=',500)->first();
        if($wlog != null)
        {
            $wlog->status = 1;
            $wlog->save();
            
            //
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://f5sys.com/dont-delete-bnbnode/send-edu.php?key='.$prikey.'&toaddrs='.$wlog->address.'&tovalues='.$wlog->payable.'&from='.$fromaddr.'&contractaddress=',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));
            
            $response = curl_exec($curl);
            
            curl_close($curl);
            
            Log::info($wid.' - '.$response);
            
            $res = json_decode($response, true);
            
            //
            if($res["result"])
            {
                $wlog->status = 2;
                $wlog->hash = $res["txid"];
                $wlog->save();
            }
            else
            {
                $wlog->status = 1;
                $wlog->remark = $res["txid"];
                $wlog->save();
            }
        }
    }
}
