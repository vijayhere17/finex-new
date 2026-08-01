<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Session;
use App\Jobs\PostActivationWork;

use App\Models\AdminUser;
use App\Models\AdminMenu;
use App\Models\AdminSubMenu;
use App\Models\AdminAssignMenu;

use App\Models\Member;
use App\Models\MemberInfo;

use App\Models\AllUserList;

use App\Models\EWalletLog;
use App\Models\HelpMaster;
use App\Models\ExpireHelpMaster;

use App\Models\PopupMaster;

use Carbon\Carbon;
use Log;
use Mail;
use Yajra\DataTables\DataTables;
use DB;

class ReportController extends Controller
{
    public function getAdminAssignUser(Request $request){
		DB::statement(DB::raw('set @rownum=0'));

		$objects = Member::where('admin_help', '=', 1)
						 ->select(DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'id', 'username', 'created_at', 'updated_at')
						 ->with(array('memberinfo'=>function($query){
								$query->select('id', 'member_id', 'name', 'mobile', 'email', 'state');
							}))
						 ->get();
		return Datatables::of($objects)->make(true);
	}
	
	public function getAdminPopup(Request $request){
		DB::statement(DB::raw('set @rownum=0'));

		$objects = PopupMaster::orderBy('updated_at','desc')
						      ->select(DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'id', 'type', 'title', 'image', 'updated_at')
						      ->get();
		return Datatables::of($objects)->make(true);
	}
	
	public function getAllAdminUser(Request $request){
		DB::statement(DB::raw('set @rownum=0'));

		$objects = AdminUser::where('id','>',1)
						    ->select(DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'id', 'username', 'password', 'created_at', 'updated_at')
						    ->get();
		return Datatables::of($objects)->make(true);
	}
	
	public function viewAdminLinks(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'id' => 'required',
            ]);

            if($v->fails())
            {
				return response()->json(array('success'=>false,'error_code'=> 'INVALID_REQUEST_DATA'), 200);
            }

            $id = $request->id;
            
            $admin = AdminUser::where('id','=',$id)->first();
            
            $main_manu = AdminMenu::join('admin_assign_menu','admin_menu.id','=','admin_assign_menu.menu_id')
                                  ->where('admin_assign_menu.admin_id','=',$admin->id)
                                  ->whereNotNull('admin_assign_menu.menu_id')
                                  ->where('admin_menu.status','=',0)
                                  ->orderBy('admin_menu.display_order','asc')
                                  ->select('admin_menu.id','admin_menu.name','admin_menu.link','admin_menu.icons')
                                  ->get();
            
            $menu_html = '<ul class="wtree">';
            
            foreach($main_manu as $m_manu)
            {
                $menu_html = $menu_html.'<li><span>'.$m_manu->name.'</span>';
                
                $sub_manu = AdminSubMenu::join('admin_assign_menu','admin_sub_menu.id','=','admin_assign_menu.sub_menu_id')
                                        ->where('admin_sub_menu.menu_id','=',$m_manu->id)
                                        ->where('admin_assign_menu.admin_id','=',$admin->id)
                                        ->whereNotNull('admin_assign_menu.sub_menu_id')
                                        ->where('admin_sub_menu.status','=',0)
                                        ->orderBy('admin_sub_menu.id','asc')
                                        ->select('admin_sub_menu.id','admin_sub_menu.name','admin_sub_menu.link')
                                        ->get();
                
                if(count($sub_manu) > 0)
                {
                    $menu_html = $menu_html.'<ul>'; 
                   
                    foreach($sub_manu as $s_manu)
                    {
                       $menu_html = $menu_html.'<li><span>'.$s_manu->name.'</span></li>'; 
                    }
                    
                    $menu_html = $menu_html.'</ul>'; 
                }
            }
            
            $menu_html = $menu_html.'</ul>'; 
            
            return response()->json(array('success'=> true, 'error_code'=> '', 'menu_html'=>$menu_html), 200);
        }catch(\Exception $exception){
            Log::error($exception);
            return response()->json(array('success'=>false,'error_code'=> 'UNEXPECTED_ERROR_OCCURED'), 200);
        }
    }
    
    public function deleteAdminAndLinks(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'id' => 'required',
            ]);

            if($v->fails())
            {
				return response()->json(array('success'=>false,'error_code'=> 'INVALID_REQUEST_DATA'), 200);
            }

            $id = $request->id;
            
            $admin = AdminUser::where('id','=',$id)->where('is_super_user','=',0)->first();
            if($admin == null)
            {
				return response()->json(array('success'=>false,'error_code'=> 'Invalid admin user'), 200);
            }
            
            $admin_links = AdminAssignMenu::where('admin_id',$admin->id)->get();
            
            foreach($admin_links as $links)
            {
                AdminAssignMenu::find($links->id)->delete();
            }
            
            $admin->delete();
            
            return response()->json(array('success'=> true, 'error_code'=> ''), 200);
        }catch(\Exception $exception){
            Log::error($exception);
            return response()->json(array('success'=>false,'error_code'=> 'UNEXPECTED_ERROR_OCCURED'), 200);
        }
    }
	
	// =================================================================================================================================================================================================
	
	public function walletstatus($wallet, $title){
	    $title = ucfirst($title).' Balance Status';
        return view('admin.wallet-status', compact('title', 'wallet'));
	}
	
	public function walletdue($wallet, $title){
	    $title = 'Due '.ucfirst($title).' Balance Report';
	    
	    $logs = EWalletLog::select('member_id')
						  ->selectRaw('SUM(IF(type_id = 1 and income_type = '.$wallet.' and assign_withdrawal != 3, amount, 0)) - SUM(IF(type_id = 2 and income_type = '.$wallet.' and assign_withdrawal != 3, amount, 0)) as balance')
						  ->groupBy('member_id')
						  ->orderBy('balance','desc')
						  ->paginate(50);
	    
        return view('admin.wallet-due', compact('title', 'logs', 'wallet'));
	}
	
	public function walletdueReportSearch(Request $request){
		if($request->ajax())
	    {
	        if($request->mobile != null ) 
	        {
				$member = Member::where('username',$request->mobile)->first();
				$member_id = ($member == null ? 0 : $member->id);
				
				$logs = EWalletLog::where('member_id',$member_id)
				                  ->select('member_id')
    						      ->selectRaw('SUM(IF(type_id = 1 and income_type = '.$request->wallet.' and assign_withdrawal != 3, amount, 0)) - SUM(IF(type_id = 2 and income_type = '.$request->wallet.' and assign_withdrawal != 3, amount, 0)) as balance')
    						      ->groupBy('member_id')
    						      ->orderBy('balance','desc')
    						      ->paginate(50);
			}
			else
			{
    			$logs = EWalletLog::select('member_id')
    						      ->selectRaw('SUM(IF(type_id = 1 and income_type = '.$request->wallet.' and assign_withdrawal != 3, amount, 0)) - SUM(IF(type_id = 2 and income_type = '.$request->wallet.' and assign_withdrawal != 3, amount, 0)) as balance')
    						      ->groupBy('member_id')
    						      ->orderBy('balance','desc')
    						      ->paginate(50);
			}
						  
			return view('admin.include.wallet_due_rep', compact('logs'))->render();
		} 
	}
	
	public function walletque($wallet, $title){
	    $title = 'Que '.ucfirst($title).' Report';
	    
	    $logs = HelpMaster::where('help_type',$wallet)
		                  ->where('member_id','>',0)   
	                      ->where('assign_status','<',2)     
						  ->orderBy('created_at','asc')
						  ->paginate(200);
	    
        return view('admin.wallet-que', compact('title', 'logs', 'wallet'));
	}
	
	public function walletqueReportSearch(Request $request){
		if($request->ajax())
	    {
	        if($request->mobile != null ) 
	        {
				$member = Member::where('username',$request->mobile)->first();
				$member_id = ($member == null ? 0 : $member->id);
				
				$logs = HelpMaster::where('help_type',$request->wallet)
    			                  ->where('member_id',$member_id)
        	                      ->where('assign_status','<',2)     
        						  ->orderBy('created_at','asc')
        						  ->paginate(200);			      
			}
			else
			{
    			$logs = HelpMaster::where('help_type',$request->wallet)
				                  ->where('member_id','>',0)
        	                      ->where('assign_status','<',2)     
        						  ->orderBy('created_at','asc')
        						  ->paginate(200);
			}
			
			$color = $request->color; 
						  
			return view('admin.include.wallet_que_rep', compact('logs', 'color'))->render();
		} 
	}
	
	public function deleteQueHelp(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'id' => 'required',
            ]);

            if($v->fails())
            {
				return response()->json(array('success'=>false,'error_code'=> 'INVALID_REQUEST_DATA'), 200);
            }

            $id = $request->id;
            
            $logs = HelpMaster::where('id',$id)->where('assign_status','<',2)->first();
			if ($logs == null) {
			    return response()->json(array('success'=>false,'error_code'=> 'Invalid request updated'), 200);
            } else {
                $this->addexpirelink($logs);
                
                $logs->delete();
                
			    return response()->json(array('success'=> true, 'error_code'=> ''), 200);	
            }
        }catch(\Exception $exception){
            Log::error($exception);
            return response()->json(array('success'=>false,'error_code'=> 'UNEXPECTED_ERROR_OCCURED'), 200);
        }
    }
    
    private function addexpirelink($help){
	    $object = new ExpireHelpMaster;
	    $object->member_id = $help->member_id;
	    $object->amount = $help->amount;
	    $object->help_type = $help->help_type;
	    $object->assign_status = $help->assign_status;
	    $object->assign_date = $help->assign_date;
	    $object->assign_complited = $help->assign_complited;
	    $object->assign_member_id = $help->assign_member_id;
	    $object->topup_id = $help->topup_id;
	    $object->paymet_id = $help->paymet_id;
	    $object->payment_sleep = $help->payment_sleep;
	    $object->payment_remark = $help->payment_remark;
	    $object->payment_status = $help->payment_status;
	    $object->rej_reson = $help->rej_reson;
	    $object->is_deleted = 1;
	    $object->created_at  = $help->created_at ;
	    $object->updated_at = $help->updated_at;
	    $object->save();
	}
	
	public function walletdeleterep($wallet, $title){
	    $title = 'Deleted '.ucfirst($title).' Report';
	    
	    $logs = ExpireHelpMaster::where('help_type',$wallet)
        	                    // ->where('assign_status','<',2)
        	                    ->where('is_deleted','=',1) 
        						->orderBy('created_at','asc')
        						->paginate(50);
	    
        return view('admin.wallet-deleted', compact('title', 'logs', 'wallet'));
	}
	
	
	public function walletdeletedReportSearch(Request $request){
		if($request->ajax())
	    {
	        if($request->mobile != null ) 
	        {
				$member = Member::where('username',$request->mobile)->first();
				$member_id = ($member == null ? 0 : $member->id);
				
				$logs = ExpireHelpMaster::where('help_type',$request->wallet)
        			                    ->where('member_id',$member_id)
            	                        // ->where('assign_status','<',2) 
            	                        ->where('is_deleted','=',1)  
            						    ->orderBy('created_at','asc')
            						    ->paginate(50);			      
			}
			else
			{
    			$logs = ExpireHelpMaster::where('help_type',$request->wallet)
            	                         // ->where('assign_status','<',2)  
            	                         ->where('is_deleted','=',1) 
            						    ->orderBy('created_at','asc')
            						    ->paginate(50);
			}
			
			$color = $request->color; 
						  
			return view('admin.include.wallet_delete_rep', compact('logs', 'color'))->render();
		} 
	}
	
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	public function mappinglist($status, $title){
	    $title = 'Mapping '.ucfirst($title).' List';
	    
	    $logs = HelpMaster::where('assign_status','=',$status)  
		                  ->where('member_id','>',0)   
						  ->orderBy('created_at','desc')
						  ->paginate(50);
	    
        return view('admin.mapping-list', compact('title', 'logs', 'status'));
	}
	
	public function mappinglistReportSearch(Request $request){
		if($request->ajax())
	    {
	        if($request->mobile != null ) 
	        {
				$member = Member::where('username',$request->mobile)->first();
				$member_id = ($member == null ? 0 : $member->id);
				
				$logs = HelpMaster::where('assign_status',$request->status)
    			                  ->where('member_id',$member_id) 
        						  ->orderBy('created_at','desc')
        						  ->paginate(50);			      
			}
			else
			{
    			$logs = HelpMaster::where('assign_status',$request->status)
				                  ->where('member_id','>',0)  
        						  ->orderBy('created_at','desc')
        						  ->paginate(50);
			}
			
			return view('admin.include.mapping_list_rep', compact('logs'))->render();
		} 
	}
	
	public function helpMakeItPending(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'id' => 'required',
            ]);

            if($v->fails())
            {
				return response()->json(array('success'=>false,'error_code'=> 'INVALID_REQUEST_DATA'), 200);
            }

            $id = $request->id;
            
            $logs = HelpMaster::where('id',$id)->where('assign_status',3)->first();
			if ($logs == null) {
			    return response()->json(array('success'=>false,'error_code'=> 'Invalid request updated'), 200);
            } else {
                $logs->assign_status = 1;
                $logs->save();
                
                $assign_mem = Member::find($logs->assign_member_id);
                if($assign_mem != null){
                    $assign_mem->status = 0;
                    $assign_mem->save();
                }
                
                // minus wallet log
                $object = new EWalletLog;
                $object->member_id = $assign_mem->member_id;
                $object->description = 'Dispute resolved by admin';
                $object->amount = $assign_mem->amount;
                $object->type_id = 2;
                $object->income_type = $assign_mem->help_type;
                $object->save();
                
			    return response()->json(array('success'=> true, 'error_code'=> ''), 200);	
            }
        }catch(\Exception $exception){
            Log::error($exception);
            return response()->json(array('success'=>false,'error_code'=> 'UNEXPECTED_ERROR_OCCURED'), 200);
        }
    }
    
    //  
    
    public function walletapproveque($wallet, $title){
	    $title = 'Que '.ucfirst($title).' Report';
	    
	    $logs = HelpMaster::where('help_type',$wallet)
		                  ->where('member_id','>',0)
	                      ->where('assign_status','=',1)   
						  ->where('payment_status','>',0)     
						  ->orderBy('created_at','asc')
						  ->paginate(50);
	    
        return view('admin.wallet-approve-que', compact('title', 'logs', 'wallet'));
	}
	
	public function walletapprovequeReportSearch(Request $request){
		if($request->ajax())
	    {
	        if($request->mobile != null ) 
	        {
				$member = Member::where('username',$request->mobile)->first();
				$member_id = ($member == null ? 0 : $member->id);
				
				$logs = HelpMaster::where('help_type',$request->wallet)
    			                  ->where('member_id',$member_id)
        	                      ->where('assign_status','=',1)   
								  ->where('payment_status','>',0)    
        						  ->orderBy('created_at','asc')
        						  ->paginate(50);			      
			}
			else
			{
    			$logs = HelpMaster::where('help_type',$request->wallet)
				                  ->where('member_id','>',0)
        	                      ->where('assign_status','<',2)   
								  ->where('payment_status','>',0)    
        						  ->orderBy('created_at','asc')
        						  ->paginate(50);
			}
			
			$color = $request->color; 
						  
			return view('admin.include.wallet_que_approve_rep', compact('logs', 'color'))->render();
		} 
	}
	
	public function helpApproveByAdmin(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'id' => 'required',
            ]);

            if($v->fails())
            {
				return response()->json(array('success'=>false,'error_code'=> 'INVALID_REQUEST_DATA'), 200);
            }

            $id = $request->id;
            
            $logs = HelpMaster::where('id',$id)->where('assign_status',1)->first();
			if ($logs == null) {
			    return response()->json(array('success'=>false,'error_code'=> 'Invalid request updated'), 200);
            } else {
                $logs->assign_status = 2;
                $logs->payment_status = 2;
                $logs->assign_complited = date("Y-m-d H:i:s");
                $logs->save();
                
                $helpActionCon = app('App\Http\Controllers\Accounts\HelpActionController'); 
                $helpActionCon->processactivation($id, $logs->assign_member_id);
                
			    return response()->json(array('success'=> true, 'error_code'=> ''), 200);	
            }
        }catch(\Exception $exception){
            Log::error($exception);
            return response()->json(array('success'=>false,'error_code'=> 'UNEXPECTED_ERROR_OCCURED'), 200);
        }
    }
    
    //
    public function checkdistribution($wallet, $title){
	    $title = 'Check '.ucfirst($title).' Distribution Report';
        $logs = EWalletLog::where('id',0)->paginate(50);
		return view('admin.check_distribution', compact('title','logs', 'wallet'));
    }
    
    public function checkdistributionReportSearch(Request $request){
		if($request->ajax())
	    {
	        $wallet = $request->wallet;
	        
            $member = Member::where('username',$request->mobile)->first();
            
            if($member == null){
                return response()->json(array('success'=> false, 'error_code'=> 'Invalid member mobile no'), 200);
            }

            $member_info = MemberInfo::where('member_id',$member->id)->first();

            $name = $member_info->name;

            $logs = EWalletLog::where('income_type','=',$wallet)->where('type_id','=',1)->where('description','like','%'.$request->mobile.'%')->paginate(50);

            return view('admin.include.check_distribution_rep', compact('logs', 'name', 'wallet'))->render();
		}
	}

	// generate help master --------------------------------------------------------------------------------------------------------------------------------------

	public function generatehelpmaster($help_type)
	{
		if ($help_type == 1) {
			$title = 'Instant Donation Generate';
		} else if ($help_type == 2) {
			$title = 'Matching Donation Generate';
		} else if ($help_type == 3) {
			$title = 'Level Donation Generate';
		}

		return view('admin.generate-help-master', compact('title', 'help_type'));
	}

	public function adminGenerateHelpaMaster(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'mobile' => 'required',
				'helptype' => 'required',
            ]);

            if($v->fails())
            {
				return response()->json(array('success'=>false,'error_code'=> 'INVALID_REQUEST_DATA'), 200);
            }

            $mobile = $request->mobile;
			$helptype = $request->helptype;

			$member = Member::where('username',$mobile)->first();
			if($member == null)
			{
				return response()->json(array('success'=>false,'error_code'=> 'Invalid mobile no'), 200);
			}

			$member_info = MemberInfo::where('member_id',$member->id)->first();

			if($helptype == 3){
                if($member->start_level == 0){
					$member->start_level = 1;
					$member->save();
				}
			}

			// generate level help
			$helpCon = app('App\Http\Controllers\Accounts\HelpController');
			$helpCon->generatedonation($member->id, $helptype); 
            
            return response()->json(array('success'=> true, 'error_code'=> ''), 200);
        }catch(\Exception $exception){
            Log::error($exception);
            return response()->json(array('success'=>false,'error_code'=> 'UNEXPECTED_ERROR_OCCURED'), 200);
        }
    }

	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
}
