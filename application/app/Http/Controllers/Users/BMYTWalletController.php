<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\UserWallet;
use Log;

class BMYTWalletController extends Controller
{
    //
    public function bmytWallet(){
        $page_titel = 'BMYT Wallet';    

        $wallet = UserWallet::where('member_id',Auth::user()->id)->first();

        $wallet_address = ($wallet == null ? '' : $wallet->address);

        $bmyt_balance = self::getbmytbalance($wallet_address);

        $txn_list = self::bmyttxnlist($wallet_address);

        return view('users.bmyt-wallet')->with(['page_titel'=>$page_titel, 'wallet_address'=>$wallet_address, 'bmyt_balance'=>$bmyt_balance, 'txn_list'=>$txn_list])->toJS();
    }

    // ===================================================================================================================================================================

    public function getbmytbalance($walletaddress){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://bmytstaking.com/web3php/token-balance.php?address='.$walletaddress,
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
        
        return $response;
    }
    
    public function sendbmyttoken($fromaddr, $prikey, $toaddr, $amount){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://bmytstaking.com/web3php/send-token.php?fromaddr='.$fromaddr.'&prikey='.$prikey.'&toaddr='.$toaddr.'&amount='.$amount,
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
        
        Log::info($response);
        
        return json_decode($response, true);
    }

    public function bmyttxnlist($walletaddress){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://ecroxscan.com/api/v2/addresses/'.$walletaddress.'/token-transfers?type=',
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
        
        return json_decode($response, true);
    }
}
