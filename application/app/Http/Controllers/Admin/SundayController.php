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
use App\Models\SundayOffers;
use App\Models\SalaryAchiever;

use App\Models\MalaysiaAchiever;
use App\Models\BakuAchiever;

use Log;
use DB;
use Carbon\Carbon;

class SundayController extends Controller
{
    
    public function createoffres()
    {
        $page_titel = 'Create Offres';   
        
        return view('admin.create-new-offres')->with(['page_titel'=>$page_titel])->toJS();
    }
    
    public function addNewOffers(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'start_date' => 'required',
				'start_end' => 'required',
				'target_amount' => 'required',
				'price' => 'required',
				'winner_no' => 'required',
				'winner_per' => 'required'
            ]);

            if($v->fails())
            {
				return response()->json(array('success'=>false,'error_code'=> 'INVALID_REQUEST_DATA'), 200);
            }
            
            $start_date = $request->start_date;
            $start_end = $request->start_end;
            $target_amount = $request->target_amount;
            $price = $request->price;
            $winner_no = $request->winner_no;
            $winner_per = $request->winner_per;

			$user = Auth::user();
			if($user == null)
			{
				return response()->json(array('success'=>false,'error_code'=> 'SESSION_INVALID'), 200);
			}
			
			$object = new SundayOffers;
	        $object->start_date = $start_date;
	        $object->end_date = $start_end;
	        $object->target = $target_amount;
	        $object->price = $price;
	        $object->no_winners = $winner_no;
	        $object->percentages = json_encode($winner_per, true);
	        $object->save();

        	return response()->json(array('success'=> true, 'error_code'=> ''), 200);
        } catch(\Exception $exception) {
            Log::error($exception);
            return response()->json(array('success'=>false,'error_code'=> 'UNEXPECTED_ERROR_OCCURED'), 200);
        }
    }
    
    public function addWinners(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'jackpot_id' => 'required',
				'winners' => 'required'
            ]);

            if($v->fails())
            {
				return response()->json(array('success'=>false,'error_code'=> 'INVALID_REQUEST_DATA'), 200);
            }
            
            $jackpot_id = $request->jackpot_id;
            $winners = $request->winners;

			$user = Auth::user();
			if($user == null)
			{
				return response()->json(array('success'=>false,'error_code'=> 'SESSION_INVALID'), 200);
			}
			
			$object = SundayOffers::find($jackpot_id);
	        $object->winners = $winners;
	        $object->save();

        	return response()->json(array('success'=> true, 'error_code'=> ''), 200);
        } catch(\Exception $exception) {
            Log::error($exception);
            return response()->json(array('success'=>false,'error_code'=> 'UNEXPECTED_ERROR_OCCURED'), 200);
        }
    }
    
    public function updateWinners(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'jackpot_id' => 'required',
            ]);

            if($v->fails())
            {
				return response()->json(array('success'=>false,'error_code'=> 'INVALID_REQUEST_DATA'), 200);
            }
            
            $jackpot_id = $request->jackpot_id;

			$user = Auth::user();
			if($user == null)
			{
				return response()->json(array('success'=>false,'error_code'=> 'SESSION_INVALID'), 200);
			}
			
			$object = SundayOffers::find($jackpot_id);
	        $object->status = 1;
	        $object->save();

        	return response()->json(array('success'=> true, 'error_code'=> ''), 200);
        } catch(\Exception $exception) {
            Log::error($exception);
            return response()->json(array('success'=>false,'error_code'=> 'UNEXPECTED_ERROR_OCCURED'), 200);
        }
    }
    
    public function getOffersReport(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $length = $request->get("length"); // Rows display per page
        
        $search_arr = $request->get('search');
        $searchValue = $search_arr['value']; // Search value

        // Total records
        $listuers = SundayOffers::orderBy('created_at','desc');
        
        if($searchValue != null)
        {
            $listuers = $listuers->where('target','=',$searchValue);
        }
                      
        $totalRecords = $listuers->count();
        
        $totalRecordswithFilter = $totalRecords;
        
        $records = $listuers->select('id', 'start_date', 'end_date', 'target', 'no_winners', 'percentages', 'status', 'winners', 'created_at')
                            ->skip($start)
    						->take($length)
                            ->get(); 
        // Fetch records
        $data_arr = array();
         
        foreach($records as $record){ 
            $data_arr[] = array(
                "id" => $record->id,
                "start_date" => date("d/m/Y", strtotime($record->start_date)),
                "end_date" => date("d/m/Y", strtotime($record->end_date)),
                "target" => $record->target,
                "no_winners" => $record->no_winners,
                "percentages" => $record->percentages,
                "status" => $record->status,
                "winners" => $record->winners,
                "created_at" => date("d/m/Y H:i A", strtotime($record->created_at)),
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
    
    public function getAchieverReport(Request $request){
        $draw = $request->get('draw');
        $start = $request->get("start");
        $length = $request->get("length"); // Rows display per page
        
        $search_arr = $request->get('search');
        $searchValue = $search_arr['value']; // Search value
        
        $dmc_id = $request->get('dmc_id');

        // Total records
        $listachiever = SalaryAchiever::join('users','salary_achiever.member_id','=','users.id')->orderBy('salary_achiever.created_at','desc');
        
        if($dmc_id > 0)
        {
            $listachiever = $listachiever->where('salary_achiever.salary_id','=',$dmc_id);
        }
        
        if($searchValue != null){
            $listachiever = $listachiever->where('username','=',$searchValue)->orWhere(DB::raw('CONCAT(firstname," ",lastname)'),'like','%'.$searchValue.'%')->orWhere('email','like','%'.$searchValue.'%')->orWhere('mobile','like','%'.$searchValue.'%');
        }
        
        $totalRecords = $listachiever->count();
        
        $totalRecordswithFilter = $totalRecords;
        
        $records = $listachiever->select('salary_achiever.id', 'salary_achiever.member_id', 'salary_achiever.salary_id', 'salary_achiever.bonus', 'salary_achiever.weeks', 'salary_achiever.status', 'salary_achiever.created_at')
                                ->with(array('member'=>function($query){
                                    $query->select('id', 'username', 'firstname', 'lastname');
                                }))  
                                ->with(array('salarymaster'=>function($query){
                                    $query->select('id', 'rank');
                                }))  
                                ->skip($start)
    							->take($length)
                                ->get(); 
        // Fetch records
        $data_arr = array();
         
        foreach($records as $record){ 
            $data_arr[] = array(
                "id" => $record->id,
                "achieve_on" => date("d/m/Y H:i A", strtotime($record->created_at)),
                "username" => $record->member->username,
                "name" => $record->member->firstname.' '.$record->member->lastname,
                "dmc_level" => $record->salarymaster->rank,
                "daily_amount" => $record->bonus,
                "remaining_days" => $record->weeks
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
    
    //
    
    public function setDMCAchievement(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'assigned_to' => 'required',
				'achieve_level' => 'required'
            ]);

            if($v->fails())
            {
				return response()->json(array('success'=>false,'error_code'=> 'INVALID_REQUEST_DATA'), 200);
            }
            
            $username = $request->assigned_to;
            $achieve_level = $request->achieve_level;

			$user = Auth::user();
			if($user == null){
				return response()->json(array('success'=>false,'error_code'=> 'SESSION_INVALID'), 200);
			}
			
			$member = User::where('username',$username)->first();
			if($member == null){
			    return response()->json(array('success'=>false,'error_code'=> 'INVALID_MEMBER'), 200);
			}
			
			for($l = 1; $l <= $achieve_level; $l++){
			    $check_rank = DMCAchiever::where('member_id',$member->id)->where('dmc_id',$l)->first();
			    if($check_rank == null){
			        $rank = DMCMaster::find($l);
			        
			        $object = new DMCAchiever;
			        $object->member_id = $member->id;
			        $object->dmc_id = $rank->id;
			        $object->daily = $rank->daily;
			        $object->days = $rank->days;
			        $object->is_admin = 1;
			        $object->save();
			        
			        $member->dmc_id = $rank->id;
			        $member->save();
			    }
			}

        	return response()->json(array('success'=> true, 'error_code'=> ''), 200);
        }catch(\Exception $exception){
            Log::error($exception);
            return response()->json(array('success'=>false,'error_code'=> 'UNEXPECTED_ERROR_OCCURED'), 200);
        }
    }
    
    //
    
    public function indexMA()
    {
        $page_titel = 'Malaysia Achievers';   
        
        return view('admin.malaysia-achiever')->with(['page_titel'=>$page_titel])->toJS();
    }
    
    public function getMAchieverReport(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $length = $request->get("length"); // Rows display per page
        
        $search_arr = $request->get('search');
        $searchValue = $search_arr['value']; // Search value
        
        // Total records
        $listachiever = MalaysiaAchiever::join('users','malaysia_achiever.member_id','=','users.id')->orderBy('malaysia_achiever.created_at','desc');
    
        if($searchValue != null){
            $listachiever = $listachiever->where('username','=',$searchValue)->orWhere(DB::raw('CONCAT(firstname," ",lastname)'),'like','%'.$searchValue.'%')->orWhere('email','like','%'.$searchValue.'%')->orWhere('mobile','like','%'.$searchValue.'%');
        }
        
        $totalRecords = $listachiever->count();
        
        $totalRecordswithFilter = $totalRecords;
        
        $records = $listachiever->select('malaysia_achiever.id', 'malaysia_achiever.member_id', 'malaysia_achiever.achieve_type', 'malaysia_achiever.created_at')
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
            if($record->achieve_type == 1)
            {
                $achieve_type = 'Self';
            }
            else if($record->achieve_type == 2)
            {
                $achieve_type = 'Couple';
            }
            else if($record->achieve_type == 2)
            {
                $achieve_type = 'Leader';
            }
            
            $data_arr[] = array(
                "id" => $record->id,
                "achieve_on" => date("d/m/Y H:i A", strtotime($record->created_at)),
                "username" => $record->member->username,
                "name" => $record->member->firstname.' '.$record->member->lastname,
                "achieve_type" => $achieve_type,
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
    
    public function indexBA()
    {
        $page_titel = 'Baku Achievers';   
        
        return view('admin.baku-achiever')->with(['page_titel'=>$page_titel])->toJS();
    }
    
    public function getBAchieverReport(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $length = $request->get("length"); // Rows display per page
        
        $search_arr = $request->get('search');
        $searchValue = $search_arr['value']; // Search value
        
        // Total records
        $listachiever = BakuAchiever::join('users','baku_achiever.member_id','=','users.id')->orderBy('baku_achiever.created_at','desc');
    
        if($searchValue != null){
            $listachiever = $listachiever->where('username','=',$searchValue)->orWhere(DB::raw('CONCAT(firstname," ",lastname)'),'like','%'.$searchValue.'%')->orWhere('email','like','%'.$searchValue.'%')->orWhere('mobile','like','%'.$searchValue.'%');
        }
        
        $totalRecords = $listachiever->count();
        
        $totalRecordswithFilter = $totalRecords;
        
        $records = $listachiever->select('baku_achiever.id', 'baku_achiever.member_id', 'baku_achiever.achieve_type', 'baku_achiever.created_at')
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
            $achieve_type = 'Executive Manager';
            
            $data_arr[] = array(
                "id" => $record->id,
                "achieve_on" => date("d/m/Y H:i A", strtotime($record->created_at)),
                "username" => $record->member->username,
                "name" => $record->member->firstname.' '.$record->member->lastname,
                "achieve_type" => $achieve_type,
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
}
