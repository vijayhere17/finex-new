<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Session;
use App\Jobs\PostActivationWork;

use App\Models\User;
use App\Models\UserStaked;
use App\Models\BinaryPoints;
use App\Models\BinaryPayout;

use Log;
use DB;
use Carbon\Carbon;

class BinaryController extends Controller
{
   // Process binary payout ---------------------------------------------------------------------------------------------------------------------------------------------------
    public function processDailyBinary(){
        try{
            $walletCon = app('App\Http\Controllers\Users\EarningWalletController');
            
            $coin_rate = getcoinrate();
            
            $created_at = date("Y-m-d H:i:s");   

			$level_refs = BinaryPoints::where('left_points', '>', 0)->where('right_points', '>', 0)->get();

			if(count($level_refs) > 0)
            {
			    foreach($level_refs as $level_ref)
                {
				    $member = User::find($level_ref->member_id);
		            $allowed_left = ($level_ref->left_points - $level_ref->left_cal_points);
		            $allowed_right = ($level_ref->right_points - $level_ref->right_cal_points);
                    $min_points = min($allowed_left, $allowed_right);

                    if($member != null)
                    {
                        if($min_points > 0)
                        {
    						if($member->kit_id > 0)
                            {
                                $highest_topup = UserStaked::where('member_id',$member->id)->orderBy('paid_amount','desc')->first();
                                $capping = ($highest_topup == null ? 0 : $highest_topup->paid_amount*10);
                                
                                $cal_commission = $min_points * 5 / 100;
                                $commission = min($cal_commission, $capping);

    							$this->addBinaryPayout($member->id, $level_ref->left_points, $level_ref->right_points, $min_points, $commission, $capping);

    							$this->updateBinaryHirrarchy($level_ref->member_id, $min_points, $min_points);

                                $description = "Team Matching Bonus (Match Business $".$min_points.")"; 
                                
                        		$walletCon->addearningwalletlog($member->id, 1, 4, $description, $commission, $coin_rate, formatdecimal($commission/$coin_rate, 8), $created_at);   

    							//Process help ----------------------------------------------------------------------------------------------------------------------------

                                $this->processteamlevelcommission($member->referral_id, 1, $commission, $created_at, $member->id);
    						}
    					}
                    }
				}
			}
		}
		catch(Exception $exception){
            Log::error('Binary Payout ----> '.$exception);
		}
	}
    
    // Private method 
    private function addBinaryPayout($member_id, $left_points, $right_points, $min_points, $commission, $capping){
		$object = new BinaryPayout;
		$object->member_id = $member_id;
		$object->left_points = $left_points;
		$object->right_points = $right_points;
		$object->meching_point = $min_points;
		$object->commission = $commission;
		$object->capping = $capping;
		$object->save();
	}

    private function updateBinaryHirrarchy($id, $left_cal_points, $right_cal_points){
		$object = BinaryPoints::find($id);
		$object->left_cal_points += $left_cal_points;
		$object->right_cal_points += $right_cal_points;
		$object->save();
	}
	
	public function processteamlevelcommission($member_id, $level, $amount, $created_at, $from_id){
        $walletCon = app('App\Http\Controllers\Users\EarningWalletController');
        
        $coin_rate = getcoinrate();
        
        $earning_type = 5;
        
        $member = User::where('id','=',$member_id)->first();
        
        $from_member = User::where('id','=',$from_id)->first();
        
        if($member != null){
            
            if($level == 1){
                $description = '1st Upline Team Bonus From '.$from_member->username;
                $commission = $amount * 1.25 / 100;
            }else if($level == 2){
                $description = '2st Upline Team Bonus From '.$from_member->username;
                $commission = $amount * 0.75 / 100;
            }else if($level == 3){
                $description = '3st Upline Team Bonus From '.$from_member->username;
                $commission = $amount * 0.50 / 100;
            }else{
                $description = '';
                $commission = 0;
            }
            
            // $direct = User::where('referral_id','=',$member_id)->where('kit_id','>',0)->count();
            
            if($member->kit_id > 0 && $commission > 0){
                $walletCon->addearningwalletlog($member->id, 1, $earning_type, $description, $commission, $coin_rate, formatdecimal($commission/$coin_rate, 8), $created_at);   
            }
            
            $level++;
            
            if($member->referral_id > 0 && $level <= 3){
                $this->processteamlevelcommission($member->referral_id, $level, $amount, $created_at, $from_id);
            }
        }
    }
    // End process binary payout -----------------------------------------------------------------------------------------------------------------------------------------------
}
