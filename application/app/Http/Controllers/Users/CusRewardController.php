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
use App\Models\BinaryPoints;

use App\Models\SalaryMaster;
use App\Models\SalaryAchiever;

use Log;
use DB;
use Carbon\Carbon;

class CusRewardController extends Controller
{
    public function indexachievers()
    {
        $page_titel = 'Income Potential Bonus';    
        
        $allsalary = SalaryMaster::get();
    
        $user_id = Auth::user()->id;
        
        $leg_data = []; // Initialize as an associative array
        
        $leg1 = User::where('referral_id', '=', $user_id)->orderByRaw('all_investment desc')->first(); // Power leg (60%)
        $leg2 = User::where('referral_id', '=', $user_id)->orderByRaw('all_investment desc')->skip(1)->first(); // Weaker leg (20%)
        // $leg3 = User::where('referral_id', '=', $user_id)->orderByRaw('all_investment desc')->skip(2)->first(); // Weaker leg (20%)
        
        $leg_data['leg_1_username'] = $leg1 ? obscureAddress($leg1->username) : '';
        $leg_data['leg_1_business'] = $leg1 ? $leg1->all_investment : 0;
        
        $leg_data['leg_2_username'] = $leg2 ? obscureAddress($leg2->username) : '';
        $leg_data['leg_2_business'] = $leg2 ? $leg2->all_investment : 0;
        
        $leg_data['leg_3_username'] = 'Other'; // $leg3 ? obscureAddress($leg3->username) : '';
        
        $leg_3_business = (Auth::user()->all_investment - ($leg1 == null ? 0 : $leg1->all_investment) - ($leg2 == null ? 0 : $leg2->all_investment));
        
        $leg_data['leg_3_business'] = $leg_3_business; // $leg3 ? $leg3->all_investment : 0;
        
        return view('users.salary-master')->with([ 'page_titel' => $page_titel,  'allsalary' => $allsalary,  'user_id' => $user_id,  'leg_data' => $leg_data ])->toJS();
    }

    
    public function getstatus($member_id, $salary_id)
    {
        $achievement = SalaryAchiever::where('member_id','=',$member_id)->where('salary_id','=',$salary_id)->first();
        
        return $achievement;
    } 
    
    // ==========================================================================================================================================================================================
    
    public function runSalaryAchiever()
    {
        $current_date = date("Y-m-d");
        
        $dashboardCon = app('App\Http\Controllers\Users\DashboardController');
        
        $allrank = SalaryMaster::get();
        
        foreach($allrank as $rank)
        {
            $members = User::where('kit_id', '>', 0)->where('salary_id', '=', $rank->id-1)->get();
            
            foreach($members as $member)
            {
                $leg1 = User::where('referral_id','=',$member->id)->orderByRaw('all_investment desc')->first(); // power leg (60%)
        		$leg2 = User::where('referral_id','=',$member->id)->orderByRaw('all_investment desc')->skip(1)->first(); // weeker leg (20%)
        		// $leg3 = User::where('referral_id','=',$member->id)->orderByRaw('all_investment desc')->skip(2)->first(); // weeker leg (20%)
        		
        		if($leg1 != null && $leg2 != null)
        		{
        		    $leg1_business = $leg1->all_investment;
        		    $leg2_business = $leg2->all_investment;
        		    $leg3_business = ($member->all_investment - $leg1_business - $leg2_business);
        		    
        		    // $total_business = $dashboardCon->getTeamBusiness($member->id, 0);
                    
                    $power_business = $rank->business * 0.60;
                    $weeker_business_1 = $rank->business * 0.20;
                    $weeker_business_2 = $rank->business * 0.20;
                    
                    if($leg1_business >= $power_business && $leg2_business >= $weeker_business_1 && $leg3_business >= $weeker_business_2)
                    {
                        $check = $this->getstatus($member->id, $rank->id);
                        
                        if($check == null)
                        {
                            // stop old salary earning
                            SalaryAchiever::where('member_id', '=', $member->id)->update([ 'status'=> 1 ]);
                             
                            $objachiever = new SalaryAchiever;
                            $objachiever->member_id = $member->id;
                            $objachiever->salary_id = $rank->id;
                            $objachiever->bonus = $rank->bonus;
                            $objachiever->weeks = $rank->weeks;
                            $objachiever->return_date = date('Y-m-d', strtotime($current_date. ' + 7 days'));
                            $objachiever->status = 0;
                            $objachiever->save();
                            
                            // update member 
                            $member->salary_id = $rank->id;
                            $member->save();
                            
                            if($rank->instead_reward > 0)
                            {
                                $walletCon = app('App\Http\Controllers\Users\EarningWalletController');
                                $description = 'Potential Instead Reward #'.$rank->rank;
                                $walletCon->addpotentialwalletlog($member->id, 1, 1, $description, $rank->instead_reward, 0, 0, date("Y-m-d H:i:s")); 
                            }
                        }
                    }
        		}
            }
        }
    }
    
    public function runSalaryEarning()
    {
        $walletCon = app('App\Http\Controllers\Users\EarningWalletController');

        // $objects = SalaryAchiever::where('status', '=', 0)->where('return_date', '<=', date("Y-m-d"))->where('weeks', '>', 0)->get();
        
        $objects = SalaryAchiever::where('status', '=', 0)->where('weeks', '>', 0)->get();

        foreach($objects as $log)
        {
            $rank = SalaryMaster::where('id','=',$log->salary_id)->first();

            $description = 'Potential Bonus #'.$rank->rank;
        
            $commission = $log->bonus;

            $earning_type = 5;

            $dashboardCon = app('App\Http\Controllers\Users\DashboardController');
            $remain_commission = $dashboardCon->check3xEarningLimit($log->member_id, $commission);

            if($remain_commission > 0)
            {
                $walletCon->addearningwalletlog($log->member_id, 1, $earning_type, $description, $remain_commission, 0, 0, date("Y-m-d H:i:s")); 

                $log->weeks -= 1;
                $log->return_date = date('Y-m-d', strtotime(date("Y-m-d"). ' + 7 days'));
                $log->save();
            }
            else
            {
                $walletCon->addearningwalletlog($log->member_id, 3, $earning_type, $description, $commission, 0, 0, date("Y-m-d H:i:s")); 
            }
        }
    }
}
