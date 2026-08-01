<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Session;
use App\Jobs\PostActivationWork;

use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketMsg;

use Carbon\Carbon;
use Log;
use Mail;
use Intervention\Image\Facades\Image;
use Datatables;
use DB;
use Auth;

class TicketController extends Controller
{
    //
    public function index(){
        $page_titel = 'Support Ticket';
        return view('admin.support-ticket', compact('page_titel'));
    }
    
    public function getAllSupportTicket(Request $request){
        ## Read value
      	$draw = $request->get('draw');
        $start = $request->get("start");
        $length = $request->get("length"); // Rows display per page
          
        $records = Ticket::where('id','>', 0)
		                 ->select('id', 'member_id', 'ticket_no', 'subject', 'title', 'status_master_id','created_at', 'updated_at')
                         ->with(array('member'=>function($query){
        						$query->select('id','username', 'firstname', 'lastname');
							}))
        				 ->get();
        
        $totalRecords = $records->count();              
        $totalRecordswithFilter = $totalRecords;                 
        
        // Fetch records
        $data_arr = array();
         
        foreach($records as $record){ 
            $id = $record->id;
            $member_id = $record->member->id;
            $username = $record->member->username;
            $name = $record->member->firstname.' '.$record->member->lastname;
            $ticket_no = $record->ticket_no;
            $subject = $record->subject;
            $title = $record->title;
            $status_master_id = $record->status_master_id;
            $created_at = $record->created_at;
            $updated_at = $record->updated_at;
         
            $data_arr[] = array(
                "id" => $id,
                "member_id" => $member_id,
                "username" => $username,
                "name" => $name,
                "ticket_no" => $ticket_no,
                "subject" => $subject,
                "title" => $title,
                "status_master_id" => $status_master_id,
                "created_at" => date("d/m-Y H:i A", strtotime($created_at)),
                "updated_at" => date("d/m-Y H:i A", strtotime($updated_at))
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

    public function ViewTicketMessage(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'lastmsgid' => 'required',
                'ticket_id' => 'required',
            ]);

            if($v->fails())
            {
                return response()->json(array('success'=>false, 'error'=>'Invalid request data send.'), 200);
            }

            $lastmsgid = $request->get('lastmsgid');
            $ticket_id = $request->get('ticket_id');

            if (Auth::user() == null) {
				return response()->json(array('success'=>false,'error'=> 'Session is expired.'), 200);
			}

            $member_id = Auth::user()->id;

            $objects = Ticket::where('id','=',$ticket_id)->where('member_id','=',$member_id)->first();
            if($objects == null){
               return response()->json(array('success'=>false, 'error'=>'Invalid ticket id'), 200);
            }

            $ticket_obj = TicketMsg::where('ticket_id','=',$ticket_id)->where('id', '>', $lastmsgid)->get();

            return response()->json(array('success'=> true, 'ticket_no'=>$objects->ticket_no, 'message'=>$ticket_obj), 200);
        }catch(Exception $exception){
            Log::error($exception);
            return response()->json(array('success'=>false,'error'=> 'An error occurred processing'), 200);
        }
    }

    public function SendTicketMessage(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'ticket_id' => 'required',
                'txt_message' => 'required',
            ]);

            if($v->fails())
            {
                return response()->json(array('success'=>false, 'error'=>'Invalid request data send.'), 200);
            }

            $ticket_id = $request->get('ticket_id');
            $txt_message = $request->get('txt_message');

            if (Auth::user() == null) {
				return response()->json(array('success'=>false,'error'=> 'Session is expired.'), 200);
			}

            $member_id = Auth::user()->id;

            $objects = Ticket::where('id','=',$ticket_id)->where('member_id','=',$member_id)->first();
            if($objects == null){
               return response()->json(array('success'=>false, 'error'=>'Invalid ticket id'), 200);
            }

            $object2  = new TicketMsg;
            $object2->ticket_id = $ticket_id;
            $object2->from_id = $member_id;
            $object2->to_id = 0;
            $object2->message = $txt_message;
            $object2->save();

            return response()->json(array('success'=> true, 'message'=>'Send Successful'), 200);
        }catch(Exception $exception){
            Log::error($exception);
            return response()->json(array('success'=>false,'error'=> 'An error occurred processing'), 200);
        }
    }
    
    public function AdminViewTicketMessage(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'lastmsgid' => 'required',
                'ticket_id' => 'required',
            ]);

            if($v->fails())
            {
                return response()->json(array('success'=>false, 'error'=>'Invalid request data send.'), 200);
            }

            $lastmsgid = $request->get('lastmsgid');
            $ticket_id = $request->get('ticket_id');

            $objects = Ticket::where('id','=',$ticket_id)->first();

            $ticket_obj = TicketMsg::where('ticket_id','=',$ticket_id)->where('id', '>', $lastmsgid)->get();

            return response()->json(array('success'=> true, 'ticket_no'=>$objects->ticket_no, 'ticket_status'=>$objects->status_master_id, 'message'=>$ticket_obj), 200);
        }catch(Exception $exception){
            Log::error($exception);
            return response()->json(array('success'=>false,'error'=> 'An error occurred processing'), 200);
        }
    }
    
    public function AdminSendTicketMessage(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'ticket_id' => 'required',
                'txt_message' => 'required',
                'ticket_status' => 'required',
            ]);

            if($v->fails())
            {
                return response()->json(array('success'=>false, 'error'=>'Invalid request data send.'), 200);
            }

            $ticket_id = $request->get('ticket_id');
            $txt_message = $request->get('txt_message');
            $ticket_status = $request->get('ticket_status');

            $objects = Ticket::where('id','=',$ticket_id)->first();

            $object2  = new TicketMsg;
            $object2->ticket_id = $ticket_id;
            $object2->from_id = 0;
            $object2->to_id = $objects->member_id;
            $object2->message = $txt_message;
            $object2->save();

            if($objects != null){
               $objects->status_master_id = $ticket_status;
               $objects->save();
            }

            return response()->json(array('success'=> true, 'message'=>'Send Successful'), 200);
        }catch(Exception $exception){
            Log::error($exception);
            return response()->json(array('success'=>false,'error'=> 'An error occurred processing'), 200);
        }
    }
}
