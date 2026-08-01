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

Route::controller(App\Http\Controllers\Admin\LoginController::class)->group(function()
{
    Route::get('login', 'index')->name('admin.login');
    Route::post('process-admin-login', 'login');
});

Route::middleware('adminauth:admin')->group(function () {

    Route::get('home', [\App\Http\Controllers\Admin\DashboardController::class,'index']);

    Route::controller(App\Http\Controllers\Admin\CommonController::class)->group(function()
    {
        Route::get('change-password', 'cpassword');
        Route::post('process-change-password', "changePassword");

        Route::get('coin-rate-set', 'coinrateset');
        Route::post('process-update-coin-rate', "changeCoinRate");
        
        Route::get('potential-withdrawal-settings', 'pwSettings');
        Route::post('process-potential-settings', "potentialSettings");
    });

    Route::controller(App\Http\Controllers\Admin\StakeReportController::class)->group(function()
    {
        Route::get('stake-request/{status}/{title}', 'stakeReport');
        Route::get('get-stake-report', 'getStakeRequest');
        Route::post('process-stake-request-action', 'actionStakeRequest');

        Route::get('user-staked-report', 'userStakedReport');
        Route::get('get-staked-report', 'getStakedReport');
        
        Route::get('downline-business-report', 'downlineBusinessReport');
        Route::post('check-downline-business', "checkDownlineBusiness");
        Route::get('get-downline-business-report', 'getDownlineBusinessReport');
        
        //
        
        Route::get('manual-topup', 'newTopup');
        Route::post('process-manual-topup', "adminStakeIDs");
        
        Route::get('new-package', 'newAddPack');
        Route::post('process-new-package', "addPackage");
        
        Route::get('new-travel-package', 'newAddTravelPack');
        Route::post('process-new-travel-package', "addTravelPackage");
        
        // Route::get('process-get-update-dmc', "tempUpdateDMCCount");
        
        Route::get('user-staked-withdrawal', 'userStakedWithdrawal');
        Route::get('get-staked-withdrawal', 'getStakedWithdrawalReport');
        Route::post('process-staked-withdrawal-req', "actionCapitalWithdraw");
        
        Route::get('add-level-achievement', 'userSetLeveLAchievement');
        Route::post('process-add-achievement', 'actionAddLevel');
        
        Route::get('add-power', 'userAddPower');
        Route::post('process-add-power', 'actionAddPower');
        
        //
        
        Route::get('wallet-staked-report', 'userWalletStakedReport');
        Route::get('get-wallet-staked-report', 'getWalletStakedReport');
        
        //
        Route::get('all-packages', 'allPackages');
        Route::get('get-all-packages-report', 'getAllPackagesReport');
        
        Route::post('update-package/{id}', 'update');
        Route::post('add-new-package', 'store');
        
    });
    
    Route::controller(App\Http\Controllers\Admin\MemberController::class)->group(function()
    {
        Route::get('member-report', 'index');
        Route::get('get-member-report', 'getMemberReport');
        Route::post('process-back-login', 'backLogin');
        
        Route::get('edit-{id}-profile', 'editmember');
        Route::post('process-update-member', 'updateMemberDetails');
    });
    
    Route::controller(App\Http\Controllers\Admin\CashbackController::class)->group(function()
    {
        Route::get('set-cashback', 'index');
        Route::post('process-set-cashback', 'setCashback');
        
        Route::get('get-cashback-user-report', 'getCashbackUserReport');
    });

    Route::controller(App\Http\Controllers\Admin\EarningReportController::class)->group(function()
    {
        Route::get('cradit-debit-master', 'craditdebitMaster');
        Route::post('process-cradit-debit-master', 'actionCraditDebit');
        
        Route::get('cradit-debit-report', 'craditdebitReport');
        Route::get('get-cradit-debit-report', 'getCraditDebitReport');
        
        Route::get('outstanding-balance', 'balanceReport');
        Route::get('get-balance-report', 'getBalanceReport');
        
        Route::get('earning-report/{status}/{title}', 'earningReport');
        Route::get('get-earning-report', 'getEarningReport');
        Route::get('get-dmc-earning-report', 'getDMCEarningReport');
    });  
    
    Route::controller(App\Http\Controllers\Admin\SalaryController::class)->group(function()
    {
        Route::get('potential-achiever', 'index');
        
        Route::get('get-dmc-achievers', 'getAchieverReport');
        
        Route::get('malaysia-achiever', 'indexMA');
        
        Route::get('get-malaysia-achievers', 'getMAchieverReport');
        
        Route::get('baku-achiever', 'indexBA');
        
        Route::get('get-baku-achievers', 'getBAchieverReport');
        
        // Route::post('process-set-dmc-level', 'setDMCAchievement');
    });

    Route::controller(App\Http\Controllers\Admin\RoiTierController::class)->group(function()
    {
        Route::get('roi-tier-master', 'index');
        Route::post('add-roi-tier', 'addTier');
        Route::post('update-roi-tier/{id}', 'updateTier');
    });

    Route::controller(App\Http\Controllers\Admin\TurnoverRewardController::class)->group(function()
    {
        Route::get('turnover-reward-master', 'index');
        Route::post('add-turnover-reward', 'addReward');
        Route::post('update-turnover-reward/{id}', 'updateReward');

        Route::get('turnover-reward-achievers', 'indexAchievers');
        Route::get('reward-weekly-salary', 'indexWeeklySalary');
        Route::get('locked-reward-report', 'indexLockedReward');
    });

    Route::controller(App\Http\Controllers\Admin\WithdrawalController::class)->group(function()
    {
        Route::get('withdrawal-request/{status}/{title}', 'indexReport');
        Route::get('get-withdrawal-report', 'getWithdrawRequest');

        Route::post('process-withdrawal-request', "withdrawalReqAction");
        Route::post('process-manual-withdrawal-request', "withdrawalReqActionManual");
    });
    
    Route::controller(App\Http\Controllers\Admin\BonanzaController::class)->group(function()
    {
        Route::get('bonanza-achievers', 'indexachievers');
        Route::get('get-bonanza-achievers', 'getAchieverReport');
        
        //
        
        Route::get('ge-left-and-right-dmc', 'getleftandrightdmc');
    });
    
    Route::controller(App\Http\Controllers\Admin\SundayController::class)->group(function()
    {
        Route::get('create-new-offres', 'createoffres');
        Route::post('submit-create-new-offers', 'addNewOffers');
        Route::post('submit-add-winners', 'addWinners');
        Route::post('submit-close-winners', 'updateWinners');
        
        //
        
        Route::get('get-offers-list', 'getOffersReport');
    });
    
    Route::controller(App\Http\Controllers\Admin\TicketController::class)->group(function()
    {
        Route::get('support-ticket', 'index');
        
        Route::get('get-all-support-ticket', 'getAllSupportTicket');

        Route::post('process-view-ticket-message', "AdminViewTicketMessage");
        
        Route::post('process-send-ticket-message', "AdminSendTicketMessage");
    });
    
    Route::controller(App\Http\Controllers\Admin\SwapController::class)->group(function()
    {
        Route::get('swap-txn-logs', 'index');
        
        Route::get('get-swap-txn-logs', 'getAllSwapTxnList');
    });

    
    // Finex compensation plan admin pages
    Route::controller(App\Http\Controllers\Admin\FinexAdminController::class)->group(function()
    {
        Route::get('finex-slots', 'slotManagement');
        Route::get('finex-direct-roi', 'directRoiMonitor');
        Route::get('finex-daily-roi-logs', 'dailyRoiLogs');
        Route::get('finex-level-roi-logs', 'levelRoiLogs');
        Route::get('finex-auto-upgrade-logs', 'autoUpgradeLogs');
        Route::get('finex-spillover-logs', 'spilloverLogs');
        Route::get('finex-member-slots', 'memberSlotStatus');
    });

Route::get('logout', function () {
        Auth::guard('admin')->logout();
        return redirect('/admin/login');
    });
});