<?php

namespace App\Http\Controllers\Admin;

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
    public function indexachievers(){
        $page_titel = 'Life Time Status';    
        
        $allrewards = LifeTimeReward::get();
        
        $member_id = Auth::user()->id;
        
        return view('admin.life-time-master')->with(['page_titel'=>$page_titel, 'allrewards'=>$allrewards, 'member_id'=>$member_id])->toJS();
    }

    public function getAchieverReport(Request $request){
        $draw = $request->get('draw');
        $start = $request->get("start");
        $length = $request->get("length"); // Rows display per page
        
        $search_arr = $request->get('search');
        $searchValue = $search_arr['value']; // Search value
        
        $bonanza_id = $request->get('bonanza_id');

        // Total records
        $listachiever = BonanzaAchiever::join('users','bonanza_achiever.member_id','=','users.id')->orderBy('bonanza_achiever.created_at','desc');
        
        if($searchValue != null){
            $listachiever = $listachiever->where('username','=',$searchValue)->orWhere(DB::raw('CONCAT(firstname," ",lastname)'),'like','%'.$searchValue.'%')->orWhere('email','like','%'.$searchValue.'%')->orWhere('mobile','like','%'.$searchValue.'%');
        }
                      
        $totalRecords = $listachiever->count();
        
        $totalRecordswithFilter = $totalRecords;
        
        $records = $listachiever->select('bonanza_achiever.id', 'bonanza_achiever.member_id', 'bonanza_achiever.bonanza_id', 'bonanza_achiever.achieve_date')
                                ->with(array('member'=>function($query){
                                    $query->select('id', 'username', 'firstname', 'lastname');
                                }))  
                                ->with(array('bonanzamaster'=>function($query){
                                    $query->select('id', 'reward', 'left_dmc', 'right_dmc');
                                }))  
                                ->skip($start)
    							->take($length)
                                ->get(); 
        // Fetch records
        $data_arr = array();
         
        foreach($records as $record){ 
            $data_arr[] = array(
                "id" => $record->id,
                "achieve_on" => date("d/m/Y H:i A", strtotime($record->achieve_date)),
                "username" => $record->member->username,
                "name" => $record->member->firstname.' '.$record->member->lastname,
                "bonanza" => $record->bonanzamaster->left_dmc.' : '.$record->bonanzamaster->right_dmc.' ('.$record->bonanzamaster->reward.')',
            );
        }
        
        $response = array(
            "draw" => intval($draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecordswithFilter,
            "data" => $data_arr
        );
    
        return json_encode($response);
    }
    
    // ============================================================================================================================================================================
    public function findachievement($member_id, $rank_id){
        $achievement = LifeTimeReward::where('member_id', $member_id)->where('life_time_reward', $rank_id)->first();
        return $achievement;
    }
    
    public function setRunLifeReward()
    {
        $walletCon = app('App\Http\Controllers\Users\EarningWalletController');
        
        $coin_rate = getwithdrawrate();
         
        $allrewards = LifeTimeReward::get();
    
        foreach($allrewards as $reward)
        {
            $c_reward_id = $reward->id - 1;
            
            $members = User::where('kit_id','>',0)->where('life_time_id','=',$c_reward_id)->get();
            
            foreach($members as $member)
            {
                $binary_point = BinaryPoints::where('member_id',$member->id)->first();
                  
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
                                  
                // ======================================================================================                  
                
                $left_dmc = ($left_dmc+$binary_point->left_dmc); $right_dmc = ($right_dmc+$binary_point->right_dmc);
                
                if($left_dmc >= $reward->left_dmc && $right_dmc >= $reward->right_dmc)
                {
                    $achievement = LifeTimeAchiever::where('member_id','=',$member->id)->where('life_time_reward','=',$reward->id)->first();
                    
                    if($achievement == null)
                    {
                        $object = new LifeTimeAchiever;
                        $object->member_id = $member->id;
                        $object->life_time_reward = $reward->id;
                        $object->achieve_date = date("Y-m-d H:i:s");
                        $object->bonus = $reward->rewards;
                        $object->save();
                        
                        $member->life_time_id = $reward->id;
                        $member->save();
                        
                        $description = 'Life Time Reward Level '.$reward->id.' Bonus'; $created_at = date("Y-m-d H:i:s");
                        
                        $walletCon->addearningwalletlog($member->id, 1, 9, $description, $reward->rewards, $coin_rate, formatdecimal($reward->rewards/$coin_rate, 8), $created_at);
                    }
                }
            }
        }
    }
}
