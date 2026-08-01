<?php

namespace App\Http\Controllers\Users;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserWallet;
use DB;

class AirDropController extends Controller
{
    //
    public function processAirdrop(){
        $bmytCon = app('App\Http\Controllers\Users\BMYTWalletController');
        
        $fromaddr = '0x0d91c412aB6DDb0965bdC8858c6d09fa0E329E50';
        $prikey = '94d2ffb3f61b624c63489261d60fd9925427d5a0b31734867a656b2b5fe5401f';
            
        $wallets = UserWallet::where('air_drop_status',0)->take(50)->get();
        
        foreach($wallets as $data){
            
            $wallet = UserWallet::find($data->id);
            
            $res = $bmytCon->sendbmyttoken($fromaddr, $prikey, $wallet->address, 25); 
            $wallet->air_drop_status = 1;
            $wallet->air_drop_hash = $res["result"];
            $wallet->save();
        }
    }
}
