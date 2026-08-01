<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use App\Models\StakeRequest;
use App\Models\UserStaked;
use App\Models\TopupByWalletLog;
use App\Models\SundayOffers;
use Log;
use DB;

class StakeReportController extends Controller
{
    //
    public function stakeReport()
    {
        $page_titel = 'Topup Requets';    
        return view('users.stake-request')->with(['page_titel'=>$page_titel, 'txn_hash_url'=>'https://bscscan.com/tx'])->toJS();
    }
    
    public function topupReport()
    {
        $page_titel = 'Topup Report';    
        return view('users.topup-report')->with(['page_titel'=>$page_titel])->toJS();
    }
    
    public function topupHistory()
    {
        if(Auth::user()->is_topup <= 0)
        {
            $page_titel = '404';
            return view('users.404')->with(['page_titel'=>$page_titel])->toJS();
        }
        
        $page_titel = 'Topup History';    
        return view('users.topup-history')->with(['page_titel'=>$page_titel])->toJS();
    }

    public function getStakeRequest(Request $request)
    {
        $objects = StakeRequest::where('member_id', Auth::user()->id)
                               ->where('stake_id', '>', 0)
                               ->orderBy('created_at','desc')
                               ->get();
                               
        return Datatables::of($objects)->make(true);
    }
    
    public function getStakeReport(Request $request)
    {
        $objects = UserStaked::where('member_id', Auth::user()->id)
                             ->orderBy('created_at','desc')
                             ->get();
                               
        return Datatables::of($objects)->make(true);
    }
    
    public function getTopupHistory(Request $request)
    {
        $objects = TopupByWalletLog::where('topup_by', Auth::user()->id)
                                   ->orderBy('created_at','desc')
                                   ->with(array('member'=>function($query){
        								$query->select('id', 'username', 'firstname', 'lastname');
        							}))
                                   ->get();
                               
        return Datatables::of($objects)->make(true);
    }
    
    // get pool data
    public function getPoolData()
    {
        $obj = SundayOffers::orderBy('id','desc')->first();
        
        $start_now = $obj->start_date;
        $end_now = $obj->end_date;
        
        $total_fill_pool = 0;
        
        $records = UserStaked::where('created_at', '>=', $start_now.' 00:00:00')
                             ->where('created_at', '<=', $end_now.' 23:59:59')
                             ->where('paid_amount', '=', $obj->target)
                             ->with(array('member'=>function($query){
    							   $query->select('id', 'username', 'firstname', 'lastname');
    						   }))
    					     ->orderBy('created_at','desc')
                             ->get();
                             
        // Fetch records
        $data_arr = array();
         
        foreach($records as $record){ 
            
            $total_fill_pool += $record->paid_amount;
            
            $data_arr[] = array(
                "address" => $record->member->username,
                "username" => obscureAddress($record->member->username),
                "name" => $record->member->firstname.' '.$record->member->lastname,
                "amount" => $record->paid_amount,
                "txn_date" => date("d/m/Y H:i A", strtotime($record->created_at)),
            );
        }
        
        $response = array(
            "tfpool" => $total_fill_pool,
            "ppool" => $obj->price,
            "winnesr" => $obj->winners,
            "status" => $obj->status,
            "end_date" => $end_now,
            "data" => $data_arr
        );
    
        return json_encode($response);                     
    }
    
}
