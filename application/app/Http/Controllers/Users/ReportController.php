<?php

namespace App\Http\Controllers\Users;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use App\Models\LevelReferral;
use App\Models\BinaryPoints;
use App\Models\ParentList;
use App\Models\LevelMaster;

use App\Models\User;
use Log;
use DB;

class ReportController extends Controller
{
    public function myreferral(){
        $page_titel = 'My Referral';          
        return view('users.my-referral', compact('page_titel'));
    }

    public function downlinerep($leg){
        if ($leg == 'A') { $position = ''; } 
        else if ($leg == 'L') { $position = 'Left '; }
        else if ($leg == 'R') { $position = 'Right '; }
        
        $page_titel = $position.'Downline Report';       

        return view('users.downline-report', compact('page_titel', 'leg'));
    }

    public function treeview(){
        $page_titel = 'Tree View';          
        return view('users.tree-view', compact('page_titel'));
    }
    
    public function levelachievement()
    {
        $page_titel = 'Level Achievement';
        
        $alllevel = LevelMaster::get();
        
        $direct = User::where('referral_id','=', Auth::user()->id)->where('kit_id','>', 0)->count();
        
        $a_a_level = Auth::user()->level;
        
        return view('users.level-achievement', compact('page_titel', 'alllevel', 'direct', 'a_a_level'));
    }

    // ====================================================================================================================================================================

    public function getReferralList(Request $request)
    {
        /* $objects = User::where('referral_id', Auth::user()->id)
                       ->orderBy('created_at','desc')
                       ->select('id', 'username', 'firstname', 'lastname', 'activation_date', 'self_investment', 'kit_id', 'created_at')
                       ->get();
        return Datatables::of($objects)->make(true); */
        
        ## Read value
        $paidsearch = $request->get('paidsearch');
        $legsearch = $request->get('legsearch');
        
      	$draw = $request->get('draw');
        $start = $request->get("start");
        $length = $request->get("length"); // Rows display per page
    
        // Total records
        $listdownline = User::where('referral_id', Auth::user()->id)->orderBy('created_at','desc');
        
        if(isset($paidsearch))
        {
            if($paidsearch == 1)
            {
                $listdownline = $listdownline->where('kit_id','>',0);
            }
            else if($paidsearch == 0)
            {
                $listdownline = $listdownline->where('kit_id','<=',0);
            }
        }
                      
        $totalRecords = $listdownline->count();
        
        $totalRecordswithFilter = $totalRecords;
        
        $records = $listdownline->select('id', 'leg', 'username', 'firstname', 'lastname', 'activation_date', 'self_investment', 'kit_id', 'created_at')
                                ->skip($start)
    							->take($length)
                                ->get(); 
        // Fetch records
        $data_arr = array();
         
        foreach($records as $record){ 
            $id = $record->id;
            $position = ($record->leg == 'L' ? 'Left' : 'Right');
            $username = $record->username;
            $firstname = $record->firstname;
            $lastname = $record->lastname;
            $kit_id = $record->kit_id;
            $self_investment = $record->self_investment;
            $activation_date = $record->activation_date;
            $created_at = $record->created_at;
    
            $data_arr[] = array(
                "id" => $id,
                "position" => $position,
                "name" => $firstname.' '.$lastname.'<br>'.obscureAddress($username),
                "isactive" => ($kit_id == 0 ? false : true),
                "self_investment" => $self_investment,
                "activation_date" => ($activation_date == null ? '' : date("d/m-Y H:i A", strtotime($activation_date))),
                "created_at" => date("d/m-Y H:i A", strtotime($created_at))
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

    public function getDownlineList(Request $request)
    {
      	## Read value
      	$level = $request->get('level');
      	$paidsearch = $request->get('paidsearch');
      
      	$draw = $request->get('draw');
        $start = $request->get("start");
        $length = $request->get("length"); // Rows display per page
        
        if($level > 0)
        {  
            $level_d = LevelReferral::where('member_id','=',Auth::user()->id)->where('level','=',$level)->first();
            $downlines = ($level_d == null ? '' : $level_d->downlines);
        }
        else
        {
            $downlines = LevelReferral::where('member_id', '=', Auth::user()->id)->select(DB::raw('group_concat(downlines) as downlines'))->first();
            $downlines = $downlines->downlines;
        }
    
        // Total records
        $listdownline = User::whereRaw('FIND_IN_SET(id,"'.$downlines.'")')->orderBy('created_at','desc');
        
        if(isset($paidsearch))
        {
            if($paidsearch == 1)
            {
                $listdownline = $listdownline->where('kit_id','>',0);
            }
            else if($paidsearch == 0)
            {
                $listdownline = $listdownline->where('kit_id','<=',0);
            }
        }
                      
        $totalRecords = $listdownline->count();              
        $totalRecordswithFilter = $totalRecords;
        
        $records = $listdownline->select('id', 'id as uid', 'username', 'firstname', 'lastname', 'referral_id', 'leg', 'kit_id', 'self_investment', 'activation_date', 'created_at')
                                ->with(array('referral'=>function($query){
                                        $query->select('id', 'username', 'firstname', 'lastname');
                                    }))
                                ->skip($start)
    							->take($length)
                                ->get(); 
        // Fetch records
        $data_arr = array();
         
        foreach($records as $record)
        { 
            $id = $record->id;
            $username = $record->username;
            $firstname = $record->firstname;
            $lastname = $record->lastname;
            $kit_id = $record->kit_id;
            $self_investment = $record->self_investment;
            $activation_date = $record->activation_date;
            $created_at = $record->created_at;
            $referral = $record->referral;
            
            $data_arr[] = array(
                "id" => $id,
                "name" => $firstname.' '.$lastname.'<br>'.obscureAddress($username),
                "isactive" => ($kit_id == 0 ? false : true),
                "self_investment" => $self_investment,
                "activation_date" => ($activation_date == null ? '' : date("d/m-Y H:i A", strtotime($activation_date))),
                "created_at" => date("d/m-Y H:i A", strtotime($created_at)),
                "referral" => ($referral == null ? '' : obscureAddress($referral->username))
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

    public function getTreeView(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'id' => 'required',
            ]);

            if($v->fails())
            {
                return response()->json(array('success'=>false, 'error'=>'Invalid request data send.'), 200);
            }

            if(Auth::user() == null){
				return response()->json(array('success'=>false,'error'=> 'Session is expired.'), 200);
			}

            $id = $request->get('id');

            $tree = new TreeNode;
            $tree->p0 = $this->getSelf($id);
            // Log::info('$tree->p0 ---->' . $tree->p0);

            //first 2
            $tree->p1 = $this->getBinaryChild($tree->p0->id, 'L');
            $tree->p2 = $this->getBinaryChild($tree->p0->id, 'R');
            // Log::info('$tree->p1 ---->' . $tree->p1);

            //below p1
            if($tree->p1 != null){
                $tree->p3 = $this->getBinaryChild($tree->p1->id, 'L');
                $tree->p4 = $this->getBinaryChild($tree->p1->id, 'R');

                if($tree->p3 != null){
                    $tree->p7 = $this->getBinaryChild($tree->p3->id, 'L');
                    $tree->p8 = $this->getBinaryChild($tree->p3->id, 'R');
                }

                if($tree->p4 != null){
                    $tree->p9 = $this->getBinaryChild($tree->p4->id, 'L');
                    $tree->p10 = $this->getBinaryChild($tree->p4->id, 'R');
                }
            }

            //below p2
            if($tree->p2 != null){
                $tree->p5 = $this->getBinaryChild($tree->p2->id, 'L');
                $tree->p6 = $this->getBinaryChild($tree->p2->id, 'R');

                if($tree->p5 != null){
                    $tree->p11 = $this->getBinaryChild($tree->p5->id, 'L');
                    $tree->p12 = $this->getBinaryChild($tree->p5->id, 'R');
                }

                if($tree->p6 != null){
                    $tree->p13 = $this->getBinaryChild($tree->p6->id, 'L');
                    $tree->p14 = $this->getBinaryChild($tree->p6->id, 'R');
                }
            }

            return response()->json(array('success'=>true, 'tree'=>$tree, 'error'=>''), 200);
        }catch(Exception $exception){
            Log::error($exception);
            return response()->json(array('success'=>false,'error'=> 'An error occurred processing'), 200);
        }
    }

    public function getSelf($id){
        $objects = User::where('id', '=', $id)
                       ->select('id', 'username', 'leg as position', 'kit_id', 'parent_id', 'dmc_status')
                       ->with(array('kit'=>function($query){
                            $query->select('id','name'); }))
                       ->first();
        return $objects;
    }

    public function getBinaryChild($parent_id, $leg){
        $objects = User::where('parent_id', '=', $parent_id)
                       ->where('leg', '=', $leg)
                       ->select('id', 'username', 'leg as position', 'kit_id', 'parent_id', 'dmc_status')
                       ->with(array('kit'=>function($query){
                            $query->select('id','name'); }))
                       ->first();
        return $objects;
    }

    public function getViewUserID(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'id' => 'required',
            ]);

            if($v->fails())
            {
                return response()->json(array('success'=>false, 'error'=>'Invalid request data send.'), 200);
            }

            if(Auth::user() == null){
				return response()->json(array('success'=>false,'error'=> 'Session is expired.'), 200);
			}

            $id = $request->get('id');

            $objects = User::where('id', '=', $id)
                           ->select('id', 'id as main_id', 'username', 'firstname', 'lastname', 'leg', 'kit_id', 'referral_id', 'activation_date', 'dmc_status', 'created_at', DB::raw("(select count(*) as right_count from parent_lists WHERE FIND_IN_SET(main_id, R_parents) ) as right_count"), DB::raw("(select count(*) as left_count from parent_lists WHERE FIND_IN_SET(main_id, L_parents) ) as left_count"))
                           ->with(array('referral'=>function($query){
                                $query->select('id', 'username', 'firstname', 'lastname', 'leg');
                            }))
                           ->with(array('kit'=>function($query){
                                $query->select('id','name as package');
                            }))
                           ->with(array('binaryPoint'=>function($query){
                                $query->select('member_id', 'left_points', 'right_points', 'left_cal_points', 'right_cal_points');
                            }))
                           ->first();
                           
            $binary_point = BinaryPoints::where('member_id',$id)->first();               
                           
            $left_dmc =  User::join('parent_lists','parent_lists.member_id','=','users.id')
                             ->whereRaw('FIND_IN_SET('.$id.',parent_lists.L_parents)')
                             ->where('users.kit_id','>',0)
                             ->where('users.dmc_status', '=', 1)
                             ->count();
            $left_dmc = $left_dmc+$binary_point->left_dmc;                   
                             
            $right_dmc =  User::join('parent_lists','parent_lists.member_id','=','users.id')
                              ->whereRaw('FIND_IN_SET('.$id.',parent_lists.R_parents)')
                              ->where('users.kit_id','>',0)
                              ->where('users.dmc_status', '=', 1)
                              ->count();
            $right_dmc = $right_dmc+$binary_point->right_dmc;                    

            return response()->json(array('success'=>true, 'data'=>$objects, 'left_dmc'=>$left_dmc, 'right_dmc'=>$right_dmc, 'error'=>''), 200);
        }catch(Exception $exception){
            Log::error($exception);
            return response()->json(array('success'=>false,'error'=> 'An error occurred processing'), 200);
        }
    }

    public function checkTreeUser(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'username' => 'required',
            ]);

            if($v->fails())
            {
                return response()->json(array('success'=>false, 'error'=>'Invalid request data send.'), 200);
            }

            if(Auth::user() == null){
				return response()->json(array('success'=>false,'error'=> 'Session is expired.'), 200);
			}

            $username = $request->get('username');

            $member_id = Auth::user()->id;

            $search = User::where('username',$username)->first();
            if($search == null){
                return response()->json(array('success'=> false, 'error'=> 'Invalid username! Pelase enter a valid username.'), 200);
            }

            $object = ParentList::where('member_id','=',$search->id)->whereRaw('FIND_IN_SET('.$member_id.',R_parents)')->first();
            if($object == null){ 
                $object = ParentList::where('member_id','=',$search->id)->whereRaw('FIND_IN_SET('.$member_id.',L_parents)')->first();
                if($object == null){ 
                    return response()->json(array('success'=>false,'error'=> 'Member is not your downline!'), 200);
                } 
            } 

            return response()->json(array('success'=> true, 'member'=> $search, 'error_code'=> ''), 200);
        }catch(Exception $exception){
            Log::error($exception);
            return response()->json(array('success'=>false,'error'=> 'An error occurred processing'), 200);
        }
    }
}

class TreeNode{
    public $p0;
    public $p1;
    public $p2;
    public $p3;
    public $p4;
    public $p5;
    public $p6;
    public $p7;
    public $p8;
    public $p9;
    public $p10;
    public $p11;
    public $p12;
    public $p13;
    public $p14;
}