<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::controller(App\Http\Controllers\SwapAdmin\LoginController::class)->group(function()
{
    Route::get('login', 'index')->name('swapadmin.login');
    Route::post('process-admin-login', 'login');
});

Route::middleware('swapauth:swapadmin')->group(function () {

    Route::get('home', [\App\Http\Controllers\SwapAdmin\DashboardController::class,'index']);

    Route::controller(App\Http\Controllers\SwapAdmin\CommonController::class)->group(function()
    {
        Route::get('change-password', 'cpassword');
        Route::post('process-change-password', "changePassword");

        Route::get('coin-rate-set', 'coinrateset');
        Route::post('process-update-coin-rate', "changeCoinRate");
        
        Route::get('swap-logs', 'swapLogs');
        
        Route::get('liqudity-withdrawal', 'liqudityWithdrawal');
    });

    Route::get('logout', function () {
        Auth::guard('swapadmin')->logout();
        return redirect('/swapadmin/login');
    });
});