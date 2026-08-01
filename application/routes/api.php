<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(App\Http\Controllers\Users\SignupController::class)->group(function()
{
    Route::post('check-sponsor-id','checkSponorid');

    Route::post('submit-sign-up','submitSignup');
});

Route::controller(App\Http\Controllers\Users\StakeReportController::class)->group(function()
{
    Route::get('pool-details', 'getPoolData');
});

Route::get('/geneate-wallet-address', function(){
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://www.f5sys.com/ecroxnode/script/generate-wallet.php',
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
});