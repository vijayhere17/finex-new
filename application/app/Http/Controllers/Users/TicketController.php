<?php

namespace App\Http\Controllers\Users;

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
        $page_titel = 'Create Ticket';
        return view('users.create-ticket', compact('page_titel'));
    }
    
    public function ticketindex($status, $title){
        $page_titel = $title;
        $member_id = Auth::user()->id;
        return view('users.ticket-history', compact('page_titel', 'status', 'member_id'));
    }
    
    public function createTicket(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'type' => 'required',
                'title' => ['required', 'regex:/^[\pL\s\pN.,!?\'"()-]+$/u'],
                'desc' => ['required', 'regex:/^[\pL\s\pN.,!?\'"()-]+$/u'],
            ]);

            if($v->fails())
            {
                return response()->json(array('success'=>false, 'error'=>'Invalid request data send.'), 200);
            }

            $questions = $request->get('type');
            $title = $request->get('title');
            $message = $request->get('desc');

            if (Auth::user() == null) {
				return response()->json(array('success'=>false,'error'=> 'Session is expired.'), 200);
			}

            $member_id = Auth::user()->id;

            $ticket_no = $this->generateTicketNo();

            $object  = new Ticket;
            $object->member_id = $member_id;
            $object->ticket_no = $ticket_no;
            $object->subject = $questions;
            $object->title = $title;
            $object->save();
            
            $object2  = new TicketMsg;
            $object2->ticket_id = $object->id;
            $object2->from_id = $member_id;
            $object2->to_id = 0;
            $object2->message = $message;
            $object2->save();

            return response()->json(array('success'=> true, 'error'=> '', 'ticket_no'=>$ticket_no), 200);
        }catch(Exception $exception){
            Log::error($exception);
            return response()->json(array('success'=>false,'error'=> 'An error occurred processing'), 200);
        }
    }

    public function generateTicketNo(){
        $unique = rand(11111111,99999999);

        $check = Ticket::where('ticket_no', $unique)->first();

        if ($check) {
            return $this->generateTicketNo();
        }

        return $unique;
    }
    
     public function getTicketList(Request $request){
        $status = $request->get('status');

        $member_id = Auth::user()->id;
        
        ## Read value
      	$draw = $request->get('draw');
        $start = $request->get("start");
        $length = $request->get("length"); // Rows display per page
          
        $records = Ticket::orderBy('id','asc')
                         ->where('member_id', '=', $member_id)
                         ->where('status_master_id', '=', $status)
                         ->get();
        
        $totalRecords = $records->count();              
        $totalRecordswithFilter = $totalRecords;                 
        
        // Fetch records
        $data_arr = array();
         
        foreach($records as $record){ 
            $id = $record->id;
            $ticket_no = $record->ticket_no;
            $subject = $record->subject;
            $title = $record->title;
            $status_master_id = $record->status_master_id;
            $created_at = $record->created_at;
         
            $data_arr[] = array(
                "id" => $id,
                "ticket_no" => $ticket_no,
                "subject" => $subject,
                "title" => $title,
                "status_master_id" => $status_master_id,
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
                'txt_message' => ['required', 'regex:/^[\pL\s\pN.,!?\'"()-]+$/u'],
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
                'txt_message' => ['required', 'regex:/^[\pL\s\pN.,!?\'"()-]+$/u'],
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
