<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Session;
use App\Jobs\PostActivationWork;

use App\Models\User;
use App\Models\ParentList;
use App\Models\BinaryPoints;

use App\Models\BonanzaMaster;
use App\Models\BonanzaAchiever;

use Log;
use DB;
use Carbon\Carbon;

class BonanzaController extends Controller
{
    public function indexachievers(){
        $page_titel = 'Bonanza Users';    
        $allbonanza = BonanzaMaster::get();
        return view('admin.bonanza-achiever')->with(['page_titel'=>$page_titel, 'allbonanza'=>$allbonanza])->toJS();
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
        
        if($bonanza_id > 0){
            $listachiever = $listachiever->where('bonanza_achiever.bonanza_id','=',$bonanza_id);
        }
        
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
    
    public function setRunBonanza()
    {
        $allbonanza = BonanzaMaster::get();
    
        foreach($allbonanza as $bonanza)
        {
            $from_date = $bonanza->from_date;
            
            $to_date = $bonanza->to_date;
            
            User::orderBy('id')->where('kit_id','>',0)->where('dmc_status','=',1)->chunk(1000, function($member_data) use ($bonanza, $from_date, $to_date) 
            {
                foreach($member_data as $data)
                {
                    $dmcdata = $this->getleftandrightdmc($data, $from_date, $to_date);

                    $left_dmc = $dmcdata["left_dmc"]; $right_dmc = $dmcdata["right_dmc"];

                    if($left_dmc >= $bonanza->left_dmc && $right_dmc >= $bonanza->right_dmc)
                    {
                        $achievement = BonanzaAchiever::where('member_id','=',$data->id)->where('bonanza_id','=',$bonanza->id)->first();
                        
                        if($achievement == null)
                        {
                            $object = new BonanzaAchiever;
                            $object->member_id = $data->id;
                            $object->bonanza_id = $bonanza->id;
                            $object->achieve_date = date("Y-m-d H:i:s");
                            $object->save();
                        }
                    }    
                }
            });
        }
    }
    
    // ===========================================================================================================================================================================
    
    public function getleftandrightdmc($member, $from_date, $to_date)
    {
        $binaryPoint = BinaryPoints::where('member_id', $member->id)->first();

        $leftDmcQuery = User::query()
            ->whereHas('parentList', function ($query) use ($member) { 
                $query->whereRaw('FIND_IN_SET(?, parent_lists.L_parents)', [$member->id]); 
            })
            ->where('kit_id', '>', 0)
            ->where('dmc_status', 1)
            ->whereBetween('activation_date', [$from_date, $to_date]);

        $rightDmcQuery = User::query()
            ->whereHas('parentList', function ($query) use ($member) {
                $query->whereRaw('FIND_IN_SET(?, parent_lists.R_parents)', [$member->id]);
            })
            ->where('kit_id', '>', 0)
            ->where('dmc_status', 1)
            ->whereBetween('activation_date', [$from_date, $to_date]);

        $leftDmcCount = $leftDmcQuery->count();
        $rightDmcCount = $rightDmcQuery->count();

        return [
            'left_dmc' => $leftDmcCount + $binaryPoint->left_dmc,
            'right_dmc' => $rightDmcCount + $binaryPoint->right_dmc
        ];
    }
}
