<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Session;
use App\Jobs\PostActivationWork;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\ParentList;
use App\Models\DMCMaster;
use App\Models\DMCAchiever;
use App\Models\BinaryPoints;

use Log;
use DB;
use Carbon\Carbon;

class DMCController extends Controller
{
    public function index(){
        $page_titel = 'DMC Achievement';    
        $ranks = DMCMaster::get();
        $user_id = Auth::user()->id;
        
        $member = User::find($user_id);
        
        $binary_point = BinaryPoints::where('member_id',$user_id)->first();
        
        $from_date = date("Y-m-d H:i:s", strtotime($member->activation_date)); 
        $to_date = date("Y-m-d H:i:s", strtotime($member->activation_date.' + 30 days'));
        
        $left_dmc_f =  User::join('parent_lists','parent_lists.member_id','=','users.id')
                         ->whereRaw('FIND_IN_SET('.$member->id.',parent_lists.L_parents)')
                         ->where('users.kit_id','>',0)
                         ->where('users.dmc_status', '=', 1)
                         ->where('users.activation_date','>=',$from_date)
                         ->where('users.activation_date','<=',$to_date)
                         ->count();
                         
        $right_dmc_f =  User::join('parent_lists','parent_lists.member_id','=','users.id')
                         ->whereRaw('FIND_IN_SET('.$member->id.',parent_lists.R_parents)')
                          ->where('users.kit_id','>',0)
                          ->where('users.dmc_status', '=', 1)
                          ->where('users.activation_date','>=',$from_date)
                          ->where('users.activation_date','<=',$to_date)
                          ->count();   
        
        $left_dmc =  User::join('parent_lists','parent_lists.member_id','=','users.id')
                         ->whereRaw('FIND_IN_SET('.$member->id.',parent_lists.L_parents)')
                         ->where('users.kit_id','>',0)
                         ->where('users.dmc_status', '=', 1)
                         ->count();
                         
        $right_dmc =  User::join('parent_lists','parent_lists.member_id','=','users.id')
                          ->whereRaw('FIND_IN_SET('.$member->id.',parent_lists.R_parents)')
                          ->where('users.kit_id','>',0)
                          ->where('users.dmc_status', '=', 1)
                          ->count();                     
        
        return view('users.dmc-master')->with(['page_titel'=>$page_titel, 'ranks'=>$ranks, 'user_id'=>$user_id, 'left_dmc'=>($left_dmc+$binary_point->left_dmc), 'right_dmc'=>($right_dmc+$binary_point->right_dmc), 'left_dmc_f'=>($left_dmc_f+$binary_point->left_dmc), 'right_dmc_f'=>($right_dmc_f+$binary_point->right_dmc)])->toJS();
    }
    
    public function findachievement($member_id, $dmc_id){
        $achievement = DMCAchiever::where('member_id',$member_id)->where('dmc_id',$dmc_id)->first();
        return $achievement;
    }
    
    // Process dmc payout ---------------------------------------------------------------------------------------------------------------------------------------------------
    
    public function processCheckRunAchiever(){
        try{
            DB::statement('SET SESSION group_concat_max_len = 100000000');
            
            $member = User::where('id','=',$_REQUEST["member_id"])->where('kit_id','>',0)->first();
            
            $binary_point = BinaryPoints::where('member_id',$_REQUEST["member_id"])->first();
            
            $dmcmaster = DMCMaster::get();

		    foreach($dmcmaster as $rank)
            {
                if($rank->target_days > 0)
                {
                    $from_date = date("Y-m-d H:i:s", strtotime($member->activation_date)); 
                    $to_date = date("Y-m-d H:i:s", strtotime($member->activation_date.' + '.$rank->target_days.' days'));
                    
                    $left_dmc =  User::join('parent_lists','parent_lists.member_id','=','users.id')
                                     ->whereRaw('FIND_IN_SET('.$member->id.',parent_lists.L_parents)')
                                     ->where('users.kit_id','>',0)
                                     ->where('users.dmc_status', '=', 1)
                                     ->where('users.activation_date','>=',$from_date)
                                     ->where('users.activation_date','<=',$to_date)
                                     ->count();
                                     
                    $right_dmc =  User::join('parent_lists','parent_lists.member_id','=','users.id')
                                     ->whereRaw('FIND_IN_SET('.$member->id.',parent_lists.R_parents)')
                                      ->where('users.kit_id','>',0)
                                      ->where('users.dmc_status', '=', 1)
                                      ->where('users.activation_date','>=',$from_date)
                                      ->where('users.activation_date','<=',$to_date)
                                      ->count(); 
                }
                else
                {
                    $left_dmc =  User::join('parent_lists','parent_lists.member_id','=','users.id')
                                     ->whereRaw('FIND_IN_SET('.$member->id.',parent_lists.L_parents)')
                                     ->where('users.kit_id','>',0)
                                     ->where('users.dmc_status', '=', 1)
                                     ->count();
                                     
                    $right_dmc =  User::join('parent_lists','parent_lists.member_id','=','users.id')
                                     ->whereRaw('FIND_IN_SET('.$member->id.',parent_lists.R_parents)')
                                      ->where('users.kit_id','>',0)
                                      ->where('users.dmc_status', '=', 1)
                                      ->count(); 
                }
                
                Log::info('Member '.$member->id.' Req L '.$rank->left_dmc_cal.' - '.$left_dmc.' | Req R '.$rank->right_dmc_cal.' - '.$right_dmc);
			}
		} catch(Exception $exception) {
            Log::error('DMC Achiever ----> '.$exception);
		}
	}
    
    public function processRunAchiever(){
        try{
            DB::statement('SET SESSION group_concat_max_len = 100000000');
            
            $dmcmaster = DMCMaster::get();

		    foreach($dmcmaster as $rank)
            {
                $dmc_rank = $rank->id-1;
                
			    $members = User::where('kit_id','>',0)->where('dmc_id','=',$dmc_rank)->get();
			    
			    foreach($members as $member){
			        
			        $binary_point = BinaryPoints::where('member_id',$member->id)->first();
			         
			        if($rank->target_days > 0){
                        $from_date = date("Y-m-d H:i:s", strtotime($member->activation_date)); 
                        $to_date = date("Y-m-d H:i:s", strtotime($member->activation_date.' + '.$rank->target_days.' days'));
                
                        $left_dmc =  User::join('parent_lists','parent_lists.member_id','=','users.id')
                                         ->whereRaw('FIND_IN_SET('.$member->id.',parent_lists.L_parents)')
                                         ->where('users.kit_id','>',0)
                                         ->where('users.dmc_status', '=', 1)
                                         ->where('users.activation_date','>=',$from_date)
                                         ->where('users.activation_date','<=',$to_date)
                                         ->count();
                        $left_dmc = $left_dmc+$binary_point->left_dmc;                 
                                         
                        $right_dmc =  User::join('parent_lists','parent_lists.member_id','=','users.id')
                                         ->whereRaw('FIND_IN_SET('.$member->id.',parent_lists.R_parents)')
                                          ->where('users.kit_id','>',0)
                                          ->where('users.dmc_status', '=', 1)
                                          ->where('users.activation_date','>=',$from_date)
                                          ->where('users.activation_date','<=',$to_date)
                                          ->count();
                        $right_dmc = $right_dmc+$binary_point->right_dmc;                  
                    }else{
                        $left_dmc =  User::join('parent_lists','parent_lists.member_id','=','users.id')
                                         ->whereRaw('FIND_IN_SET('.$member->id.',parent_lists.L_parents)')
                                         ->where('users.kit_id','>',0)
                                         ->where('users.dmc_status', '=', 1)
                                         ->count();
                        $left_dmc = $left_dmc+$binary_point->left_dmc;                   
                                         
                        $right_dmc =  User::join('parent_lists','parent_lists.member_id','=','users.id')
                                          ->whereRaw('FIND_IN_SET('.$member->id.',parent_lists.R_parents)')
                                          ->where('users.kit_id','>',0)
                                          ->where('users.dmc_status', '=', 1)
                                          ->count();
                        $right_dmc = $right_dmc+$binary_point->right_dmc;                  
                    }
                    
                    if($left_dmc >= $rank->left_dmc_cal && $right_dmc >= $rank->right_dmc_cal){
                        $check = DMCAchiever::where('member_id',$member->id)->where('dmc_id',$rank->id)->first();
                        if($check == null){
                            $achievement = new DMCAchiever;
                            $achievement->member_id = $member->id;
                            $achievement->dmc_id = $rank->id;
                            $achievement->daily = $rank->daily;
                            $achievement->days = $rank->days;
                            $achievement->status = 0;
                            $achievement->save();
                            
                            $u_member = User::find($member->id);
                            $u_member->dmc_id = $rank->id;
			                $u_member->save();
                        }
                    }
			    }
			}
		} catch(Exception $exception) {
            Log::error('DMC Achiever ----> '.$exception);
		}
	}
	
	public function releaseDMCPayout(){
	    $walletCon = app('App\Http\Controllers\Users\EarningWalletController');
        
        $coin_rate = getwithdrawrate();
        
	    $achiever = DMCAchiever::where('status','=',0)
	                           ->where('days','>',0)
	                           ->get();
	                           
	    foreach($achiever as $logs)
	    {
	        $update = DMCAchiever::find($logs->id);
    	    $update->days -= 1;
    	    $update->save();
    	    
    	    $description = 'Daily DMC Bonus'; $created_at = date("Y-m-d H:i:s");
    	    
    	    if(date('D') != 'Sat' && date('D') != 'Sun') 
    	    {
    	        $walletCon->addwalletlog($logs->member_id, 1, 6, $description, $logs->daily, $coin_rate, formatdecimal($logs->daily/$coin_rate, 8), $created_at);  
    	    }
    	    else 
    	    {
    	        $walletCon->addearningwalletlog($logs->member_id, 1, 6, $description, $logs->daily, $coin_rate, formatdecimal($logs->daily/$coin_rate, 8), $created_at); 
    	    }
	    }                        
	}
	
    // End process dmc payout -----------------------------------------------------------------------------------------------------------------------------------------------
}
