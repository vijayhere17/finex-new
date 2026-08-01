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

use App\Models\LifeTimeReward;
use App\Models\LifeTimeAchiever;

use Log;
use DB;
use Carbon\Carbon;

class LifeTimeRewardController extends Controller
{
    public function indexachievers()
    {
        $page_titel = 'Life Time Status';    
        
        $allrewards = LifeTimeReward::get();
        
        $member_id = Auth::user()->id;
        
        $binary_point = BinaryPoints::where('member_id',$member_id)->first();
        
        $left_dmc =  User::join('parent_lists','parent_lists.member_id','=','users.id')
                         ->whereRaw('FIND_IN_SET('.$member_id.',parent_lists.L_parents)')
                         ->where('users.kit_id','>',0)
                         ->where('users.dmc_status', '=', 1)
                         ->count();
                         
        $right_dmc =  User::join('parent_lists','parent_lists.member_id','=','users.id')
                          ->whereRaw('FIND_IN_SET('.$member_id.',parent_lists.R_parents)')
                          ->where('users.kit_id','>',0)
                          ->where('users.dmc_status', '=', 1)
                          ->count();           
        
        return view('users.life-time-master')->with(['page_titel'=>$page_titel, 'allrewards'=>$allrewards, 'member_id'=>$member_id, 'left_dmc'=>($left_dmc+$binary_point->left_dmc), 'right_dmc'=>($right_dmc+$binary_point->right_dmc)])->toJS();
    }

    // ============================================================================================================================================================================

    public function findachievement($member_id, $rank_id)
    {
        $achievement = LifeTimeAchiever::where('member_id', $member_id)->where('life_time_reward', $rank_id)->first();
        
        return $achievement;
    }
}
