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
use App\Models\UserStaked;

use App\Models\RewardMaster;
use App\Models\RewardAchiever
;
use App\Models\MalaysiaAchiever;
use App\Models\BakuAchiever;
use App\Models\SalaryAchiever;

use Log;
use DB;
use Carbon\Carbon;

class RewardController extends Controller
{
    public function indexachievers()
    {
        $page_titel = 'Reward Master';    
        
        $allrewards = RewardMaster::get();

        $user_id = Auth::user()->id;
                           
        return view('users.reward-master')->with(['page_titel'=>$page_titel, 'allrewards'=>$allrewards, 'user_id'=>$user_id])->toJS();
    }
    
    public function getstatus($member_id, $reward_id)
    {
        $achievement = RewardAchiever::where('member_id','=',$member_id)->where('reward_id','=',$reward_id)->first();
        
        return $achievement;
    } 
    
    // =========================================================================================================================================================================
    
    public function runMalaysiaAchiever()
    {
        User::where('id','>',1)->where('is_malaysia','=',0)->where('kit_id','>',0)->select('id as member_id')->orderBy('id','asc')->lazy(5000)->each(function ($data)
        {
            $target_single = 3000; $target_couple = 5000;
            
            $total_stake = UserStaked::where('member_id','=',$data->member_id)->where('created_at','>','2025-03-15 00:00:00')->where('topup_type','=',0)->sum('paid_amount');
            $total_stake = ($total_stake == null ? 0 : $total_stake);
            
            $total_dma = User::where('referral_id','=',$data->member_id)->where('is_malaysia','>',0)->count();
            
            $is_a_type = 0;
            
            if($total_stake >= $target_single)
            {
                $is_a_type = 1;
            }
            else if($total_stake >= $target_couple)
            {
                $is_a_type = 2;
            }
            else if($total_dma >= 4)
            {
                $is_a_type = 3;
            }
            
            if($is_a_type > 0)
            {
                $check = MalaysiaAchiever::where('member_id','=',$data->member_id)->first();
                if($check == null)
                {
                    $object = new MalaysiaAchiever;
                    $object->member_id = $data->member_id;
                    $object->achieve_type = $is_a_type;
                    $object->achieve_date = date("Y-m-d H:i:s");
                    $object->save();
                    
                    //
                    $member = User::find($data->member_id);
                    $member->is_malaysia = $is_a_type;
                    $member->save();
                }
            }
        });
    }
    
    //
    
    public function runBakuAchiever()
    {
        User::where('id','>',1)->where('is_baku','=',0)->where('kit_id','>',0)->select('id as member_id')->orderBy('id','asc')->lazy(5000)->each(function ($data)
        {
            $check_em_rank = SalaryAchiever::where('member_id','=',$data->member_id)
                                           ->where('salary_id','=',4)
                                           ->where('created_at','>=','2025-03-12 00:00:00')
                                           ->where('created_at','<=','2025-04-15 23:59:59')
                                           ->first();
            if($check_em_rank != null)
            {
                $check = BakuAchiever::where('member_id','=',$data->member_id)->first();
                if($check == null)
                {
                    $object = new BakuAchiever;
                    $object->member_id = $data->member_id;
                    $object->achieve_type = 4;
                    $object->achieve_date = date("Y-m-d H:i:s");
                    $object->save();
                    
                    //
                    $member = User::find($data->member_id);
                    $member->is_baku = 1;
                    $member->save();
                }
            }
        });
    }
}
