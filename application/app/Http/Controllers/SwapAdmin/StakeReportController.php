<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use App\Models\StakeRequest;
use App\Models\BinaryPoints;
use App\Models\StakeMaster;
use App\Models\UserStaked;
use App\Models\UserWallet;
use App\Models\ParentList;
use App\Models\LevelReferral;
use App\Models\User;
use Log;
use DB;

class StakeReportController extends Controller
{
    //
    public function stakeReport($status, $title)
    {
        $page_titel = $title.' Request';    
        return view('admin.stake-request')->with(['page_titel'=>$page_titel, 'status'=>$status])->toJS();
    }

    public function userStakedReport()
    {
        $page_titel = 'User Topup Report';    
        return view('admin.member-staked')->with(['page_titel'=>$page_titel])->toJS();
    }
    
    public function downlineBusinessReport()
    {
        $page_titel = 'Downline Business Report';    
        return view('admin.downline-business')->with(['page_titel'=>$page_titel])->toJS();
    }

    public function getStakeRequest(Request $request){
        $draw = $request->get('draw');
        $start = $request->get("start");
        $length = $request->get("length"); // Rows display per page
        
        $search_arr = $request->get('search');
        $searchValue = $search_arr['value']; // Search value

        $status = $request->get('status');
    
        // Total records
        $liststakereq = StakeRequest::join('users','staked_requests.member_id','=','users.id')->where('staked_requests.status', $status)->orderBy('staked_requests.created_at','desc');
        
        if($searchValue != null){
            $liststakereq = $liststakereq->where('users.username','=',$searchValue)->orWhere(DB::raw('CONCAT(users.firstname," ",users.lastname)'),'like','%'.$searchValue.'%');
        }
                      
        $totalRecords = $liststakereq->count();
        
        $totalRecordswithFilter = $totalRecords;
        
        $records = $liststakereq->select('staked_requests.id', 'staked_requests.payment', 'staked_requests.invoice_no', 'staked_requests.member_id', 'staked_requests.stake_id', 'staked_requests.amount', 'staked_requests.bonus', 'staked_requests.total_amount', 'staked_requests.coin_rate', 'staked_requests.stake_coin', 'staked_requests.status', 'staked_requests.hash', 'staked_requests.created_at')
                                ->with(array('member'=>function($query){
                                    $query->select('id', 'username', 'firstname', 'lastname');
                                }))                        
                                ->skip($start)
    							->take($length)
                                ->get(); 
        // Fetch records
        $data_arr = array();
         
        foreach($records as $record){ 
            $data_arr[] = array(
                "id" => $record->id,
                "payment" => ($record->payment == 0 ? '<b>USDT</b>' : '<b>EDU</b>'),
                "request_on" => date("d/m/Y H:i A", strtotime($record->created_at)),
                "username" => obscureAddress($record->member->username),
                "name" => $record->member->firstname.' '.$record->member->lastname,
                "stake_amount" => $record->amount,
                "txn_hash" => '<a href="https://polygonscan.com/tx/'.$record->hash.'" target="_blank">View Hash</a>',
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

    public function getStakedReport(Request $request){
        $draw = $request->get('draw');
        $start = $request->get("start");
        $length = $request->get("length"); // Rows display per page
        
        $search_arr = $request->get('search');
        $searchValue = $search_arr['value']; // Search value

        // Total records
        $liststakedrep = UserStaked::join('users','staked_users.member_id','=','users.id')->orderBy('staked_users.created_at','desc');
        
        if($searchValue != null){
            $liststakedrep = $liststakedrep->where('users.username','=',$searchValue)->orWhere(DB::raw('CONCAT(users.firstname," ",users.lastname)'),'like','%'.$searchValue.'%');
        }
                      
        $totalRecords = $liststakedrep->count();
        
        $totalRecordswithFilter = $totalRecords;
        
        $records = $liststakedrep->select('staked_users.id', 'staked_users.member_id', 'staked_users.kit_id', 'staked_users.paid_amount', 'staked_users.bonus', 'staked_users.total_amount', 'staked_users.booster', 'staked_users.topup_type', 'staked_users.description', 'staked_users.created_at')
                                ->with(array('member'=>function($query){
                                    $query->select('id', 'username', 'firstname', 'lastname');
                                }))                        
                                ->skip($start)
    							->take($length)
                                ->get(); 
        // Fetch records
        $data_arr = array();
         
        foreach($records as $record)
        {
            if($record->topup_type == 0)
            {
               $topup_type = 'Normal Topup';
            }
            else if($record->topup_type == 1)
            {
               $topup_type = 'Leader Topup (Only Topup)';
            }
            else if($record->topup_type == 2)
            {
               $topup_type = 'ROI Topup (Topup & Daily ROI)';
            }

            $data_arr[] = array(
                "id" => $record->id,
                "request_on" => date("d/m/Y H:i A", strtotime($record->created_at)),
                "username" => obscureAddress($record->member->username),
                "name" => $record->member->firstname.' '.$record->member->lastname,
                "stake_amount" => $record->paid_amount,
                "topup_type" => $topup_type,
                "description" => $record->description,
                "booster" => $record->booster,
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
    
    //---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    
    public function transferCoin(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'id' => 'required',
            ]);

            if($v->fails())
            {
				return response()->json(array('success'=>false,'error_code'=> 'INVALID_REQUEST_DATA'), 200);
            }

			$user = Auth::user();
			if($user == null){
				return response()->json(array('success'=>false,'error_code'=> 'SESSION_INVALID'), 200);
			}
			
			$staked = UserStaked::find($request->id);
			
			// transfer stake coin
            $wallet = UserWallet::where('member_id',$staked->member_id)->first();
            if($wallet != null){
                $fromaddr = '0x0d91c412aB6DDb0965bdC8858c6d09fa0E329E50';
                $prikey = '94d2ffb3f61b624c63489261d60fd9925427d5a0b31734867a656b2b5fe5401f';
                
                $bmytCon = app('App\Http\Controllers\Users\BMYTWalletController');
                $res = $bmytCon->sendbmyttoken($fromaddr, $prikey, $wallet->address, $staked->payable_coin); 
                
                $staked->txnhash = $res["result"];
                $staked->save();
            }

			return response()->json(array('success'=> true, 'error_code'=> ''), 200);
        }catch(\Exception $exception){
            Log::error($exception);
            return response()->json(array('success'=>false,'error_code'=> 'UNEXPECTED_ERROR_OCCURED'), 200);
        }
    }
    
    
    //////////////////////////////////////////////////////////////////////////////// DMC Package Master Start  //////////////////////////////////////////////////////////////////
    
    public function newAddPack()
    {
        $page_titel = 'Add DMC Package';   
        $apackages = StakeMaster::where('is_admin',1)->get();
        return view('admin.package-master')->with(['page_titel'=>$page_titel, 'packages'=>$apackages])->toJS();
    }
    
    public function addPackage(Request $request){
		try{
			$v = Validator::make($request->all(), [
                'name' => 'required',
                'amount' => 'required',
                'coin' => 'required',
                'percantage' => 'required',
                'months' => 'required',
                'direct_ref' => 'required',
                'locking' => 'required',
                'dmc_commission' => 'required',
                'left_dmc' => 'required',
                'right_dmc' => 'required'
            ]);

            if($v->fails())
            {
				return response()->json(array('success'=>false,'error_code'=> 'INVALID_REQUEST_DATA'), 200);
            }
			 
			$name = $request->get('name');
			$amount = $request->get('amount');
			$coin = $request->get('coin');
			$percantage = $request->get('percantage');
			$months = $request->get('months');
			$direct_ref = $request->get('direct_ref');
			$locking = $request->get('locking');
			$dmc_commission = $request->get('dmc_commission');
			$left_dmc = $request->get('left_dmc');
			$right_dmc = $request->get('right_dmc');
			
            $object = new StakeMaster;
		    $object->name = $name;
			$object->amount = $amount;
			$object->coin = $coin;
			$object->percantage = $percantage;
			$object->months = $months;
			$object->direct_ref = $direct_ref;
			$object->locking = $locking;
			$object->dmc_commission = $dmc_commission;
			$object->left_dmc = $left_dmc;
			$object->right_dmc = $right_dmc;
			$object->is_admin = 1;
			$object->save();	

			return response()->json(array('success'=>true,'error_code'=> ''), 200);	
		}catch(Exception $exception){
			Log::error($exception);
			return response()->json(array('success'=>false,'error_code'=> 'UNEXPECTED_ERROR_OCCURED'), 200);
		}			
	}
	
	public function newAddTravelPack(){
        $page_titel = 'Add Travel Package';   
        $apackages = StakeMaster::where('is_travel',1)->get();
        return view('admin.package-travel-master')->with(['page_titel'=>$page_titel, 'packages'=>$apackages])->toJS();
    }
    
    public function addTravelPackage(Request $request){
		try{
			$v = Validator::make($request->all(), [
                'name' => 'required',
                'amount' => 'required',
                'coin' => 'required',
                'percantage' => 'required',
                'months' => 'required',
                'direct_ref' => 'required',
                'locking' => 'required',
                'dmc' => 'required',
                'dmc_commission' => 'required',
                'left_dmc' => 'required',
                'right_dmc' => 'required'
            ]);

            if($v->fails())
            {
				return response()->json(array('success'=>false,'error_code'=> 'INVALID_REQUEST_DATA'), 200);
            }
			 
			$name = $request->get('name');
			$amount = $request->get('amount');
			$coin = $request->get('coin');
			$percantage = $request->get('percantage');
			$months = $request->get('months');
			$direct_ref = $request->get('direct_ref');
			$locking = $request->get('locking');
			$dmc = $request->get('dmc');
			$dmc_commission = $request->get('dmc_commission');
			$left_dmc = $request->get('left_dmc');
			$right_dmc = $request->get('right_dmc');
			
            $object = new StakeMaster;
		    $object->name = $name;
			$object->amount = $amount;
			$object->coin = $coin;
			$object->percantage = $percantage;
			$object->months = $months;
			$object->direct_ref = $direct_ref;
			$object->locking = $locking;
			$object->dmc = $dmc;
			$object->dmc_commission = $dmc_commission;
			$object->left_dmc = $left_dmc;
			$object->right_dmc = $right_dmc;
			$object->is_travel = 1;
			$object->save();	

			return response()->json(array('success'=>true,'error_code'=> ''), 200);	
		} catch(Exception $exception) {
			Log::error($exception);
			return response()->json(array('success'=>false,'error_code'=> 'UNEXPECTED_ERROR_OCCURED'), 200);
		}			
	}
    
    //////////////////////////////////////////////////////////////////////////////// DMC Package Master End  /////////////////////////////////////////////////////////////////////
    
    
    // admin stake method --------------------------------------------------------------------------------------------------------------------------------------------------------
    
    public function newTopup()
    {
        $page_titel = 'Manaul Topup';   

        return view('admin.manual_topup')->with(['page_titel'=>$page_titel])->toJS();
    }
    
    public function adminStakeIDs(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'assigned_to' => 'required',
                'amount' => 'required',
                'topup_type' => 'required',
                'description' => 'required',
            ]);

            if($v->fails())
            {
				return response()->json(array('success'=>false,'error_code'=> 'INVALID_REQUEST_DATA'), 200);
            }

			$member = User::where('username','=',$request->assigned_to)->first();
			if($member == null){
				return response()->json(array('success'=>false,'error_code'=> 'INVALID_MEMBER'), 200);
			}
			
			$kit = StakeMaster::find(1);
			
			$created_at = date("Y-m-d H:i:s"); 
			
			$stakeCon = app('App\Http\Controllers\Users\StakeController');
            $stakeCon->adminStakeActivation($member->id, $kit->id, $request->amount, $request->topup_type, $request->description);
				
			return response()->json(array('success'=> true, 'error_code'=> ''), 200);
        } catch(\Exception $exception) {
            Log::error($exception);
            return response()->json(array('success'=>false,'error_code'=> 'UNEXPECTED_ERROR_OCCURED'), 200);
        }
    }
    
    public function checkDownlineBusiness(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'username' => 'required',
            ]);

            if($v->fails())
            {
				return response()->json(array('success'=>false,'error_code'=> 'INVALID_REQUEST_DATA'), 200);
            }

			$member = User::where('username','=',$request->username)->first();
			if($member == null)
			{
				return response()->json(array('success'=>false,'error_code'=> 'INVALID_MEMBER'), 200);
			}
			
			$dashboardCon = app('App\Http\Controllers\Users\DashboardController');
			$total_business = $dashboardCon->getTeamBusiness($member->id, 0);
				
			return response()->json(array('success'=> true, 'member_id'=>$member->id, 'name'=>($member->firstname.' '.$member->lastname), 'total_business'=>$total_business, 'error_code'=> ''), 200);
        } catch(\Exception $exception) {
            Log::error($exception);
            return response()->json(array('success'=>false,'error_code'=> 'UNEXPECTED_ERROR_OCCURED'), 200);
        }
    }
    
    public function getDownlineBusinessReport(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $length = $request->get("length"); // Rows display per page
        
        $search_arr = $request->get('search');
        $searchValue = $search_arr['value']; // Search value
        
        $userid = $request->get('userid');
        
        //
        DB::statement('SET SESSION group_concat_max_len = 10000000');

		$downlines = LevelReferral::where('member_id', '=', $userid)->select(DB::raw('group_concat(downlines) as downlines'))->first();
        $downlines = ($downlines == null ? '' : $downlines->downlines);

        // Total records
        $liststakedrep = UserStaked::join('users','staked_users.member_id','=','users.id')->whereRaw('FIND_IN_SET(member_id,"'.$downlines.'")')->orderBy('staked_users.created_at','desc');
        
        if($searchValue != null)
        {
            $liststakedrep = $liststakedrep->where('users.username','=',$searchValue)->orWhere(DB::raw('CONCAT(users.firstname," ",users.lastname)'),'like','%'.$searchValue.'%');
        }
                      
        $totalRecords = $liststakedrep->count();
        
        $totalRecordswithFilter = $totalRecords;
        
        $records = $liststakedrep->select('staked_users.id', 'staked_users.member_id', 'staked_users.kit_id', 'staked_users.paid_amount', 'staked_users.bonus', 'staked_users.total_amount', 'staked_users.booster', 'staked_users.topup_type', 'staked_users.description', 'staked_users.created_at')
                                ->with(array('member'=>function($query){
                                    $query->select('id', 'username', 'firstname', 'lastname');
                                }))                        
                                ->skip($start)
    							->take($length)
                                ->get(); 
        // Fetch records
        $data_arr = array();
         
        foreach($records as $record)
        {
            if($record->topup_type == 0)
            {
               $topup_type = 'Normal Topup';
            }
            else if($record->topup_type == 1)
            {
               $topup_type = 'Leader Topup (Only Topup)';
            }
            else if($record->topup_type == 2)
            {
               $topup_type = 'ROI Topup (Topup & Daily ROI)';
            }

            $data_arr[] = array(
                "id" => $record->id,
                "request_on" => date("d/m/Y H:i A", strtotime($record->created_at)),
                "username" => obscureAddress($record->member->username),
                "name" => $record->member->firstname.' '.$record->member->lastname,
                "stake_amount" => $record->paid_amount,
                "topup_type" => $topup_type,
                "description" => $record->description,
                "booster" => $record->booster,
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

    // ===========================================================================================================================================================================
}
