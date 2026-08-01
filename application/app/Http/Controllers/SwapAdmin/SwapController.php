<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use App\Models\SwapLogs;
use Log;
use DB;

class SwapController extends Controller
{
    public function index()
    {
        $page_titel = 'Swap Txn. Logs';
    
        return view('admin.swap-txn-logs', compact('page_titel'));
    }
    
    public function getAllSwapTxnList(Request $request){
        ## Read value
      	$draw = $request->get('draw');
        $start = $request->get("start");
        $length = $request->get("length"); // Rows display per page
          
        $records = SwapLogs::orderBy('updated_at','desc')->get();
        
        $totalRecords = $records->count();              
        $totalRecordswithFilter = $totalRecords;                 
        
        // Fetch records
        $data_arr = array();
         
        foreach($records as $record){ 
            
            $data_arr[] = array(
                "id" => $record->id,
                "txn_date" => date("d/m-Y H:i A", strtotime($record->created_at)),
                "last_update" => date("d/m-Y H:i A", strtotime($record->updated_at)),
                "address" => $record->address,
                "receive_wallet" => $record->receive_wallet,
                "from_coin" => $record->swap_from,
                "to_coin" => $record->swap_to,
                "amount" => $record->amount,
                "rate" => $record->rate,
                "change" => $record->charge,
                "swap_amount" => $record->swap_amount,
                "txn_hash" => $record->txn_hash,
                "status" => $record->status,
                "receiver_hash" => $record->receiver_hash
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
    
    // =========================================================================================================================================================================
    
    // sell bmyt
    public function runsellusdt()
    {
        $from = '0x426aDDc42059726095aA0beea533715B2b6A4CFF';
        
        $prikey = '74c4275600d422f809bfb8f161ef7d4646543776a1ae3414dfa3407d3111f3b6';
        
        $object = SwapLogs::where('swap_to','=','USDT')->where('swap_amount','<=',25)->where('status','=',0)->take(5)->get();    
        
        foreach($object as $data)
        {
            $data->status = 1;
            $data->save();
            
            //
            
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://f5sys.com/dont-delete-bnbnode/send-bep20.php?faddress='.$from.'&taddress='.$data->receive_wallet.'&amount='.$data->swap_amount.'&pkey='.$prikey,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));
            
            $response = curl_exec($curl);
            
            curl_close($curl);
            
            $result = json_decode($response, true);
            
            if($result["result"])
            {
                $data->status = 2;
                $data->receiver_hash = $result["txid"];
                $data->save();
            }
            else
            {
                $data->status = 3;
                $data->save(); 
            }
        }
    }
    
    // buy usdt
    public function runbuyusdt()
    {
        $bmytCon = app('App\Http\Controllers\Users\BMYTWalletController');
            
        $from = '0x531DAE02415F3067b704490753819854450E7aAa';
        
        $prikey = 'aa87c5ba3578bcd5c323aa2bbf8e0f05a368f4906e17d8d59096ec68ba76ebd2';
        
        $object = SwapLogs::where('swap_to','=','BMYT')->where('status','=',0)->take(2)->get();    
        
        foreach($object as $data)
        {
            $data->status = 1;
            $data->save();
            
            //
            $res = $bmytCon->sendbmyttoken($from, $prikey, $data->receive_wallet, $data->swap_amount); 
            
            $data->status = 2;
            $data->receiver_hash = $res["result"];
            $data->save();
        }
    }
}
