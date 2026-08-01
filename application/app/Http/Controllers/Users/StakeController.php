<?php

namespace App\Http\Controllers\Users;

use Illuminate\Support\Facades\Validator;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\StakeMaster;
use App\Models\StakeRequest;
use App\Models\UserStaked;
use App\Models\ParentList;
use App\Models\BinaryPoints;
use App\Models\TopupByWalletLog;
use App\Models\RoiTierMaster;
use App\Models\BoosterAchiever;
use App\Models\WithdrawalLog;
use App\Models\MonthlyROIRate;
use DB;
use Log;

class StakeController extends Controller
{
    //
    protected function depositaddress()
    {
        return config('income.deposit_wallet', '0x4d02Eda4EE50E55D97974D0C7b8647Ea9853B0aE');
    }

    protected function contractabi(){
        return '[{"inputs":[],"payable":false,"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"owner","type":"address"},{"indexed":true,"internalType":"address","name":"spender","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Approval","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"previousOwner","type":"address"},{"indexed":true,"internalType":"address","name":"newOwner","type":"address"}],"name":"OwnershipTransferred","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"from","type":"address"},{"indexed":true,"internalType":"address","name":"to","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Transfer","type":"event"},{"constant":true,"inputs":[],"name":"_decimals","outputs":[{"internalType":"uint8","name":"","type":"uint8"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"_name","outputs":[{"internalType":"string","name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"_symbol","outputs":[{"internalType":"string","name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address[]","name":"_user","type":"address[]"},{"internalType":"uint256","name":"value","type":"uint256"}],"name":"airdrop","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"internalType":"address","name":"owner","type":"address"},{"internalType":"address","name":"spender","type":"address"}],"name":"allowance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"approve","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"internalType":"address","name":"account","type":"address"}],"name":"balanceOf","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"basePercent","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"burn","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"decimals","outputs":[{"internalType":"uint8","name":"","type":"uint8"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"subtractedValue","type":"uint256"}],"name":"decreaseAllowance","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"getOwner","outputs":[{"internalType":"address","name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"addedValue","type":"uint256"}],"name":"increaseAllowance","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"maxBurning","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address[]","name":"_user","type":"address[]"},{"internalType":"uint256[]","name":"value","type":"uint256[]"}],"name":"multisender","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"name","outputs":[{"internalType":"string","name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"owner","outputs":[{"internalType":"address","name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[],"name":"renounceOwnership","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"symbol","outputs":[{"internalType":"string","name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"totalBurning","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"totalSupply","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"recipient","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"transfer","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"sender","type":"address"},{"internalType":"address","name":"recipient","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"transferFrom","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"newOwner","type":"address"}],"name":"transferOwnership","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"}]';
    }
    
    protected function contractaddr()
    {
        // USDT (BEP20) on BSC mainnet - 18 decimals
        return config('income.usdt_contract', '0x55d398326f99059fF775485246999027B3197955');
    }

    public function getDepositWallet()
    {
        return $this->depositaddress();
    }

    public function getUsdtContract()
    {
        return $this->contractaddr();
    }

    public function getUsdtAbi()
    {
        return $this->contractabi();
    }

    //

    /**
     * Fixed sequential slot investment amounts (Slot 1 .. Slot 12).
     */
    protected function getFixedSlotAmounts()
    {
        return config('income.slot_amounts', [10, 20, 40, 80, 160, 320, 640, 1280, 2560, 5120, 10240, 20480]);
    }

    /**
     * Build sequential slot card states from purchase history.
     * Exposes current_slot / next_slot / activation_status for the view
     * without changing activation / ROI / referral business logic.
     */
    protected function buildSlotProgress($member, $packages)
    {
        $amounts = $this->getFixedSlotAmounts();

        $packagesSorted = collect($packages)->sortBy('amount')->values();

        $packageByAmount = [];
        foreach ($packagesSorted as $pkg) {
            $packageByAmount[(string) (float) $pkg->amount] = $pkg;
        }

        $purchasedAmounts = UserStaked::where('member_id', '=', $member->id)
            ->pluck('paid_amount')
            ->map(function ($amount) {
                return (float) $amount;
            })
            ->unique()
            ->values()
            ->all();

        // Contiguous purchased slots starting from Slot 1.
        $current_slot = 0;
        foreach ($amounts as $index => $amount) {
            if (in_array((float) $amount, $purchasedAmounts, true)) {
                $current_slot = $index + 1;
            } else {
                break;
            }
        }

        $next_slot = ($current_slot < count($amounts)) ? ($current_slot + 1) : 0;
        $next_slot_amount = ($next_slot > 0) ? $amounts[$next_slot - 1] : 0;

        // Prefer persisted activation_status; fall back for pre-migration rows.
        $dbStatus = strtolower((string) ($member->activation_status ?? ''));
        if ($dbStatus === \App\Services\DirectRoiService::STATUS_ACTIVE || $current_slot > 0) {
            $activation_status = \App\Services\DirectRoiService::STATUS_ACTIVE;
        } else {
            $activation_status = \App\Services\DirectRoiService::STATUS_REGISTERED;
        }

        // Attach computed progress fields for the Blade (no DB migration required for slots).
        $member->current_slot = $current_slot;
        $member->next_slot = $next_slot;
        $member->activation_status = $activation_status;

        $slots = [];
        foreach ($amounts as $index => $amount) {
            $slotNumber = $index + 1;
            // Prefer exact amount match; fall back to ordered package index so cards remain actionable.
            $pkg = $packageByAmount[(string) (float) $amount] ?? $packagesSorted->get($index);

            if ($slotNumber <= $current_slot) {
                $state = 'purchased';
            } elseif ($next_slot > 0 && $slotNumber === $next_slot) {
                $state = 'ready';
            } else {
                $state = 'locked';
            }

            $slots[] = (object) [
                'slot_number' => $slotNumber,
                'amount' => $amount,
                'state' => $state,
                'stake_id' => $pkg->id ?? null,
                'cap_multiplier' => $pkg->cap_multiplier ?? 0,
                'name' => $pkg->name ?? ('Slot '.$slotNumber),
            ];
        }

        return [
            'slots' => $slots,
            'current_slot' => $current_slot,
            'next_slot' => $next_slot,
            'next_slot_amount' => $next_slot_amount,
            'activation_status' => $activation_status,
        ];
    }

    public function buyRobo()
    {
        $page_titel = 'Slot Activation';

        $coin_rate = getcoinrate();
        
        // Only the new amount-tiered ROI packages are offered for fresh activations - legacy named packages stay in the table for history only.
        $packages = StakeMaster::where('is_admin','=',0)->where('is_travel','=',0)->where('ptype','=',2)->orderBy('amount', 'asc')->get();
        $packages = $this->attachTierRanges($packages);

        $member = Auth::user();
        $slotProgress = $this->buildSlotProgress($member, $packages);

        // Direct ROI Income — calculate & store % (no wallet credit yet).
        $directRoi = app(\App\Services\DirectRoiService::class)->getDisplayStats($member->fresh());

        $usdt_con_addr = $this->contractaddr();
        
        $usdt_con_abi = $this->contractabi();

        $to_address = $this->depositaddress();

        return view('users.buy-bot')->with([
    'page_titel'=>$page_titel,
    'coin_rate'=>$coin_rate,
    'packages'=>$packages,
    'slots'=>$slotProgress['slots'],
    'current_slot'=>$slotProgress['current_slot'],
    'next_slot'=>$slotProgress['next_slot'],
    'next_slot_amount'=>$slotProgress['next_slot_amount'],
    'activation_status'=>$slotProgress['activation_status'],
    'qualified_active_directs'=>$directRoi['qualified_active_directs'],
    'direct_roi_percent'=>$directRoi['direct_roi_percent'],
    'usdt_con_addr'=>$usdt_con_addr,
    'usdt_con_abi'=>$usdt_con_abi,
    'to_address'=>$to_address,
])->toJS();
    }
    
    public function submitBotTxn(Request $request)
    {
        try {
            $v = Validator::make($request->all(), [
                'id' => 'required',
                'stake_id' => 'required',
                'payment' => 'required',
                'amount' => 'required',
                'status' => 'required',
            ]);

            $id = $request->get('id');

            if ($id == 0) 
            {
                $rules['hash'] = 'required|unique:staked_requests';
            } 
            else 
            {
                $rules['hash'] = 'required'; // No uniqueness check for existing records
            }

            if ($v->fails())
            {
                return response()->json(array('success'=>false, 'error'=>'Invalid request data send.'), 200);
            }

            if (Auth::user() == null)
            {
				return response()->json(array('success'=>false,'error'=> 'Session is expired.'), 200);
			}
			
			$date = date("Y-m-d H:i:s");
			
			$coin_rate = getcoinrate();

            $stake_id = $request->get('stake_id');
            $amount =  $request->get('amount');
            $payment = $request->get('payment');
            $status = $request->get('status');
            $hash = $request->get('hash');

            // Fixed sequential slots only — no custom / free-typed amounts.
            $slotAmounts = $this->getFixedSlotAmounts();
            $amountFloat = (float) $amount;
            if (!is_numeric($amount) || !in_array($amountFloat, array_map('floatval', $slotAmounts), true))
            {
                return response()->json(array('success'=>false, 'error'=>'Invalid slot amount. Please activate a fixed slot.'), 200);
            }

            // Enforce sequential activation: only the next eligible slot may be purchased.
            $slotProgress = $this->buildSlotProgress(Auth::user(), StakeMaster::where('is_admin','=',0)->where('is_travel','=',0)->where('ptype','=',2)->orderBy('amount', 'asc')->get());
            if ((float) $slotProgress['next_slot_amount'] <= 0 || $amountFloat !== (float) $slotProgress['next_slot_amount'])
            {
                return response()->json(array('success'=>false, 'error'=>'Please activate slots in sequence. Only the next eligible slot can be purchased.'), 200);
            }

            // Rate comes from the amount actually paid, not the client-selected package.
            $kit = $this->resolveRoiTierKit($amount);

            if($kit == null)
            {
                $kit = StakeMaster::find($stake_id);
            }

            // ------------------------------------------------------------------------------------------------------------------
            
            if($id == 0)
            {
                $invoice_no = self::generateInvoice();
                $object = new StakeRequest;
                $object->payment = $payment;
                $object->invoice_no = $invoice_no;
            }
            else
            {
                $object = StakeRequest::find($id);
            }
            
            $object->member_id = Auth::user()->id;
            
            $object->stake_id = $kit->id;
            $object->amount = $amount;

            $object->coin_rate = $coin_rate;
            $object->stake_coin = number_format((float)$amount/$coin_rate, 8, '.', '');

            $object->hash = $hash;
            $object->status = $status;

            $object->return_date = date('Y-m-d H:i:s', strtotime($date. ' + '.$kit->months.' days'));
            $object->apy = $kit->percantage;
         
            $object->d_apy = ($kit->ptype == 2) ? $kit->percantage : ($kit->percantage / $kit->months);
            
            $object->save();

            if($status == 2)
            {
                // check transaction hash
                // $response = Http::withOptions(['verify' => false])->get("https://f5sys.com/dont-delete-bnbnode/tnx-details.php?hash={$hash}");
                
                $rpc_url = 'https://bsc-dataseed1.binance.org/';
                
                $response = shell_exec("node /home/eudstake/node/txn-details.js ".$hash." ".$rpc_url);

                $result = json_decode($response, true);    

                if($result["status"])
                {
                    $status = $this->setStakeActivation($object->member_id, $object->stake_id, $object->amount, $object->id);

                    return response()->json(array('success'=>true, 'message'=>'Your Stake Successfully!', 'error'=>''), 200);
                }
                else
                {
                    $object->status = 0;
                    $object->save();

                    return response()->json(array('success'=>true, 'message'=>'Your Stake Request Submited Successfully!<br>Request Process Few Minutes.', 'error'=>''), 200);
                }
            }

            return response()->json(array(
                'success'=>true,
                'id'=>$object->id,
                'message'=>($status == 0
                    ? 'Your topup request was submitted. Waiting for admin approval.'
                    : ''),
                'error'=>''
            ), 200);
        } catch(Exception $exception) {
            Log::error($exception);
            return response()->json(array('success'=>false,'error'=> 'An error occurred processing'), 200);
        }
    }
    
    public function runProcessStakeReq()
    {
        $rpc_url = 'https://bsc-dataseed1.binance.org/';
        
        $object = StakeRequest::find(0);
        
        if($object != null)
        {
            // check transaction hash
            $response = shell_exec("node /home/eudstake/node/txn-details.js ".$object->hash." ".$rpc_url);
            Log::info($response);
            $result = json_decode($response, true);    
            if($result["status"])
            {
                $status = $this->setStakeActivation($object->member_id, $object->stake_id, $object->amount, $object->id);

                return response()->json(array('success'=>true, 'message'=>'Your Stake Successfully!', 'error'=>''), 200);
            }
            else
            {
                return response()->json(array('success'=>true, 'message'=>'Your Stake Request Submited Successfully!<br>Request Process Few Minutes.', 'error'=>''), 200);
            }
        }
    }
    
    // by wallet
    public function buyRoboWallet()
    {
        if(Auth::user()->is_topup <= 0)
        {
            $page_titel = '404';
            return view('users.404')->with(['page_titel'=>$page_titel])->toJS();
        }
        
        $page_titel = 'New Topup By Earing Wallet'; 
        
        $userid = Auth::user()->id;

        $coin_rate = getcoinrate();
        
        $packages = StakeMaster::where('is_admin','=',0)->where('is_travel','=',0)->where('ptype','=',2)->get();
        $packages = $this->attachTierRanges($packages);

        $walletCon = app('App\Http\Controllers\Users\DepositWalletController');
        $balance = $walletCon->gewalletbalance($userid);

        return view('users.buy-bot-wallet')->with(['page_titel'=>$page_titel, 'coin_rate'=>$coin_rate, 'packages'=>$packages, 'balance'=>$balance])->toJS();
    }
    
    public function submitBotTxnByWallet(Request $request)
    {
        try {
            $v = Validator::make($request->all(), [
                'stake_id' => 'required',
                'payment' => 'required',
                'amount' => 'required',
                'username' => 'required',
            ]);

            if ($v->fails())
            {
                return response()->json(array('success'=>false, 'error'=>'Invalid request data send.'), 200);
            }

            if (Auth::user() == null)
            {
				return response()->json(array('success'=>false,'error'=> 'Session is expired.'), 200);
			}
			
			$coin_rate = getcoinrate();
			
			$userid = Auth::user()->id;
			
			$username = $request->get('username');

            $stake_id = $request->get('stake_id');
            $payment = $request->get('payment');
            $amount = $request->get('amount');

            // Server-side mirror of the client rule so direct API calls can't bypass it.
            if(!is_numeric($amount) || $amount < 50 || fmod((float)$amount, 50) != 0.0)
            {
                return response()->json(array('success'=>false, 'error'=>'Topup amount must be a multiple of $50 (minimum $50).'), 200);
            }

            // Rate comes from the amount actually paid, not the client-selected package.
            $stake_kit = $this->resolveRoiTierKit($amount);

            if($stake_kit == null)
            {
                $stake_kit = StakeMaster::find($stake_id);
            }
            
            if(Auth::user()->is_topup <= 0)
            {
                return response()->json(array('success'=>false,'error'=> 'Unauthorized top-up request.'), 200);
            }
            
            $walletCon = app('App\Http\Controllers\Users\DepositWalletController');

            // ------------------------------------------------------------------------------------------------------------------
            $member = User::where('username', '=', $username)->first();
            if($member == null)
            {
                return response()->json(array('success'=>false,'error'=> 'User wallet address is not found.'), 200);
            }
            
            $balance = $walletCon->gewalletbalance($userid);
            if($amount > $balance)
            {
                return response()->json(array('success'=>false,'error'=> 'Insufficient account balance.'), 200);
            }
            
            // debit e wallet
            $description = obscureAddress($username).' ID Activated successfully';
            $log = $walletCon->addwalletlog($userid, 2, 0, $description, $amount, date("Y-m-d H:i:s")); 
            
            // submit Topup
            $status = $this->setStakeActivation($member->id, $stake_kit->id, $amount, 0);
            
            // add logs
            $nlobj = new TopupByWalletLog;
            $nlobj->ref_id = $log->id;
            $nlobj->member_id = $member->id;
            $nlobj->kit_id = $stake_kit->id;
            $nlobj->amount = $amount;
            $nlobj->topup_by = $userid;
            $nlobj->save();

            return response()->json(array('success'=>true, 'message'=>'Your ID Topup Successfully!', 'error'=>''), 200);
        } catch(Exception $exception) {
            Log::error($exception);
            return response()->json(array('success'=>false,'error'=> 'An error occurred processing'), 200);
        }
    }
    
    public function generateInvoice()
    {
        $invoice_no = rand(pow(10, 6 - 1), pow(10, 6) -1);

        $check = StakeRequest::where('invoice_no','=',$invoice_no)->first();

        if($check != null)
        {
            return $this->generateInvoice();
        }

        return $invoice_no;
    }
   
    // -------------------------------------------------------------------------------------------------------------------------------

    // Attaches the display amount-range to each tiered package: a tier runs up to $50 below the next
    // tier's minimum (all topups are $50 multiples); the top tier is open-ended (max_amount = 0).
    private function attachTierRanges($packages)
    {
        $packages = $packages->sortBy('amount')->values();

        foreach($packages as $i => $pkg)
        {
            $next = $packages->get($i + 1);
            $pkg->max_amount = ($next != null) ? ($next->amount - 1) : 0;
        }

        return $packages;
    }

    // Resolves the correct ROI tier for an amount and returns the matching synthetic stake_masters (ptype=2) row.
    // Rate is always derived server-side from the amount actually paid - never trust a client-supplied kit_id for rate.
    public function resolveRoiTierKit($amount)
    {
        $tier = RoiTierMaster::where('is_active', 1)
                    ->where('min_amount', '<=', $amount)
                    ->where(function($q) use ($amount) {
                        $q->whereNull('max_amount')->orWhere('max_amount', '>=', $amount);
                    })
                    ->orderBy('min_amount', 'desc')
                    ->first();

        if($tier == null)
        {
            return null;
        }

        // Prefer the stake_masters row whose fixed amount matches the paid amount.
        // All slots may share the same percantage, so amount must disambiguate the kit.
        $kit = StakeMaster::where('ptype', 2)
                    ->whereRaw('ROUND(percantage, 3) = ?', [$tier->daily_percent])
                    ->where('amount', '=', $amount)
                    ->first();

        if ($kit != null) {
            return $kit;
        }

        // Fallback for legacy range packages: highest amount tier at or below paid amount.
        return StakeMaster::where('ptype', 2)
                    ->whereRaw('ROUND(percantage, 3) = ?', [$tier->daily_percent])
                    ->where('amount', '<=', $amount)
                    ->orderBy('amount', 'desc')
                    ->first();
    }

    public function setStakeActivation($member_id, $kit_id, $amount, $s_r_id)
    {
        // Idempotent: same stake request must not create a second slot (double Approve).
        if ($s_r_id > 0) {
            $existing = UserStaked::where('s_r_id', $s_r_id)->first();
            if ($existing != null) {
                return $existing;
            }
        }

        $walletCon = app('App\Http\Controllers\Users\EarningWalletController');

        $kit = $this->resolveRoiTierKit($amount);

        if($kit == null)
        {
            $kit = StakeMaster::where('id','=',$kit_id)->first();
        }

        $member = User::where('id','=',$member_id)->first();

        $is_first_activation = ($member != null && $member->activation_date == null);
        
        if($member != null)
        {
            $member->kit_id = $kit->id;
            if($is_first_activation)
            {
                $member->activation_date = date("Y-m-d H:i:s");
            }
            $member->self_investment = ($member->self_investment+$amount);
            $member->save();

            // Direct ROI display: mark slot-active + refresh stored ROI % for member & sponsor.
            app(\App\Services\DirectRoiService::class)->markActiveAndRefreshSponsor($member->fresh());

            // Persist current/next slot on user for dashboard.
            $progress = app(\App\Services\AutoUpgradeService::class)->resolveSlotProgress($member->fresh());
            $member->current_slot = $progress['current_slot'];
            $member->next_slot = $progress['next_slot'];
            $member->save();
        }

        // Add Purchased Kit Log
        $log = $this->addpurchasedkitlog($member->id, $kit->id, $amount, 0, null, $s_r_id);
        
        // Update Level Business
        User::whereRaw('FIND_IN_SET(id,"'.$member->referral_uplines.'")')
            ->update(['team_investment'=> DB::raw('team_investment+'.$amount)]);

        // Locked Reward Bonus: legacy — disabled for Finex
        if($is_first_activation && config('income.legacy_locked_reward_enabled', false))
        {
            $this->allocateLockedRewardBonus($member->id);
        }

        // Update Direct Business
        if($member->referral_id > 0)
        {
            $refer = User::where('id','=',$member->referral_id)->first();
            if($refer != null)
            {
                $refer->direct_business = ($refer->direct_business+$amount);
                $refer->save();

                // Legacy Booster Income (disabled for Finex — see income.legacy_booster_enabled)
                if (config('income.legacy_booster_enabled', false)) {
                    $this->processBoosterIncome($refer);
                }

                // Locked Reward Unlock: legacy — disabled for Finex
                if($is_first_activation && config('income.legacy_locked_reward_enabled', false))
                {
                    $this->unlockLockedRewardBonus($refer->id, $member->id, $amount);
                }
            }

            // Finex: no Direct Sponsor / referral wallet income on slot buy.
            // 2nd/3rd direct still can fund Auto Upgrade wallet per plan.
            if($refer != null && $refer->kit_id > 0)
            {
                if (config('income.direct_sponsor_enabled', false)) {
                    self::processreferralcommission($refer->id, 1, $amount, $member->id, $kit->id, date("Y-m-d H:i:s"));
                } else {
                    app(\App\Services\AutoUpgradeService::class)
                        ->creditFromDirectActivation($refer, (int) $member->id, (float) $amount);
                }
            }
        }
    }

    /**
     * Allocate Locked Reward Bonus once per member on first package activation.
     */
    public function allocateLockedRewardBonus($member_id)
    {
        $member = User::find($member_id);

        if($member == null)
        {
            return;
        }

        // Only once — already allocated if lock date is set or locked/unlocked/expired > 0
        if($member->locked_reward_lock_date != null || ($member->locked_reward_bonus + $member->unlocked_reward_bonus + $member->expired_reward_bonus) > 0)
        {
            return;
        }

        $bonus = (float) config('income.locked_reward_bonus', 1000);
        $days = (int) config('income.locked_reward_validity_days', 30);
        $now = date('Y-m-d H:i:s');

        $member->locked_reward_bonus = $bonus;
        $member->unlocked_reward_bonus = 0;
        $member->expired_reward_bonus = 0;
        $member->locked_reward_lock_date = $now;
        $member->locked_reward_expiry_date = date('Y-m-d H:i:s', strtotime($now.' + '.$days.' days'));
        $member->save();
    }

    /**
     * Unlock 10% of referral package amount from sponsor's remaining locked bonus.
     * Once per referral (sponsor_unlock_done on the activating member). No TDS/fees.
     */
    public function unlockLockedRewardBonus($sponsor_id, $from_member_id, $package_amount)
    {
        $from = User::find($from_member_id);

        if($from == null || (int) $from->sponsor_unlock_done === 1)
        {
            return;
        }

        $sponsor = User::find($sponsor_id);

        if($sponsor == null)
        {
            return;
        }

        // Mark unlock attempted for this referral immediately to prevent duplicates
        $from->sponsor_unlock_done = 1;
        $from->save();

        $remaining = (float) $sponsor->locked_reward_bonus;

        if($remaining <= 0)
        {
            return;
        }

        $percent = (float) config('income.locked_reward_unlock_percent', 10);
        $unlock_amount = formatdecimal(($package_amount * $percent) / 100, 4);

        if($unlock_amount <= 0)
        {
            return;
        }

        if($unlock_amount > $remaining)
        {
            $unlock_amount = $remaining;
        }

        $sponsor->locked_reward_bonus = formatdecimal($remaining - $unlock_amount, 4);
        $sponsor->unlocked_reward_bonus = formatdecimal(((float) $sponsor->unlocked_reward_bonus) + $unlock_amount, 4);
        $sponsor->save();

        $walletCon = app('App\Http\Controllers\Users\EarningWalletController');
        $from_address = obscureAddress($from->username);
        $description = 'Locked Reward Unlock From '.$from_address.' (Package $'.$package_amount.')';

        // No TDS / admin / processing fee — 100% to earning wallet (earning_type 10)
        $walletCon->addearningwalletlog($sponsor->id, 1, 10, $description, $unlock_amount, 0, 0, date('Y-m-d H:i:s'));
    }

    /**
     * Expire remaining locked reward after validity window. Unlocked remains forever.
     */
    public function runLockedRewardExpiry()
    {
        $now = date('Y-m-d H:i:s');

        $members = User::where('locked_reward_bonus', '>', 0)
            ->whereNotNull('locked_reward_expiry_date')
            ->where('locked_reward_expiry_date', '<=', $now)
            ->get();

        foreach($members as $member)
        {
            $remaining = (float) $member->locked_reward_bonus;

            if($remaining <= 0)
            {
                continue;
            }

            $member->expired_reward_bonus = formatdecimal(((float) $member->expired_reward_bonus) + $remaining, 4);
            $member->locked_reward_bonus = 0;
            $member->save();
        }
    }

    /**
     * Required active directs for ROI Override level qualification.
     */
    public function getLevelIncomeRequiredDirects($level)
    {
        $rules = config('income.level_income_direct_rules', []);

        foreach($rules as $rule)
        {
            if($level >= $rule['from'] && $level <= $rule['to'])
            {
                if(($rule['mode'] ?? null) === 'equal')
                {
                    return (int) $level;
                }

                return (int) ($rule['directs'] ?? 0);
            }
        }

        return PHP_INT_MAX;
    }

    public function processBoosterIncome($refer)
    {
        // Finex plan has no Booster Income — legacy Ginance path disabled.
        if (!config('income.legacy_booster_enabled', false)) {
            return;
        }

        if($refer == null || $refer->is_booster > 0 || $refer->activation_date == null)
        {
            return;
        }

        $window_hours = config('income.booster_window_hours');
        $required_directs = config('income.booster_required_directs');

        $window_end = date('Y-m-d H:i:s', strtotime($refer->activation_date.' + '.$window_hours.' hours'));

        $own_stake = UserStaked::where('member_id','=',$refer->id)->where('topup_type','=',0)->orderBy('created_at','asc')->first();

        if($own_stake == null)
        {
            return;
        }

        $own_amount = $own_stake->paid_amount;

        $directs = User::where('referral_id','=',$refer->id)
                        ->where('activation_date','>=',$refer->activation_date)
                        ->where('activation_date','<=',$window_end)
                        ->where('kit_id','>',0)
                        ->get();

        $count = 0;

        foreach($directs as $direct)
        {
            $direct_stake = UserStaked::where('member_id','=',$direct->id)->where('topup_type','=',0)->orderBy('created_at','asc')->first();
            $direct_amount = $direct_stake ? $direct_stake->paid_amount : 0;

            if($direct_amount >= $own_amount)
            {
                $count++;
            }
        }

        if($count < $required_directs)
        {
            return;
        }

        $refer->is_booster = 1;
        $refer->booster_on = date("Y-m-d H:i:s");
        $refer->save();

        $walletCon = app('App\Http\Controllers\Users\EarningWalletController');
        $dashboardCon = app('App\Http\Controllers\Users\DashboardController');

        $description = 'Booster Income - 100% Return of Topup $'.$own_amount;

        $remain_commission = $dashboardCon->check3xEarningLimit($refer->id, $own_amount);

        if($remain_commission > 0)
        {
            $walletCon->addearningwalletlog($refer->id, 1, 8, $description, $remain_commission, 0, 0, date("Y-m-d H:i:s"));
        }
        else
        {
            $walletCon->addearningwalletlog($refer->id, 3, 8, $description, $own_amount, 0, 0, date("Y-m-d H:i:s"));
        }
    }

    private function addpurchasedkitlog($member_id, $kit_id, $amount, $topup_type, $description, $s_r_id)
    {
        $kit = StakeMaster::where('id','=',$kit_id)->first();

        $date = date("Y-m-d H:i:s");

        if($s_r_id > 0)
        {
            $req = StakeRequest::find($s_r_id);

            $coin_rate = $req->coin_rate;
            $paid_coin = number_format((float)$req->stake_coin, 8, '.', '');
        }
        else
        {
            $coin_rate = getcoinrate();
            $paid_coin = number_format((float)$amount/$coin_rate, 8, '.', '');
        }

        $object = new UserStaked;
        $object->s_r_id = $s_r_id;
        $object->member_id = $member_id;
        $object->kit_id = $kit->id;

        $object->paid_amount = $amount;
        $object->total_amount = $amount;
        $object->coin_rate = $coin_rate;
        $object->payable_coin = $paid_coin;

        if($kit->ptype == 2)
{
    $roi_tier = RoiTierMaster::where('is_active', 1)
                    ->where('min_amount', '<=', $amount)
                    ->where(function ($q) use ($amount) {
                        $q->whereNull('max_amount')
                          ->orWhere('max_amount', '>=', $amount);
                    })
                    ->first();

    if (!$roi_tier) {
        // Soft: tier optional under new fixed-slot plan; kit still activates.
        $roi_tier = null;
    }

    $object->roi_tier_id = $roi_tier->id ?? null;

    // Live Daily ROI uses users.direct_roi_percent — no monthly ROI snapshot required.
    $object->joining_roi_percent = 0;
    $object->apy = 0;
    $object->d_apy = 0;

    $object->cap_multiplier = $kit->cap_multiplier;
    $object->maximum_income = $amount * $kit->cap_multiplier;
    $object->total_roi_paid = 0;
    $object->roi_days_paid = 0;
    $object->max_roi_days = (int) config('income.daily_roi.max_days', 300);

    $slotAmounts = config('income.slot_amounts', []);
    $slotNumber = array_search((float) $amount, array_map('floatval', $slotAmounts), true);
    $object->slot_number = ($slotNumber === false) ? null : ($slotNumber + 1);

    // Keep legacy fields for compatibility
    $object->return_days = $object->max_roi_days;
    $object->return_date = date('Y-m-d H:i:s', strtotime($date.' + '.$object->max_roi_days.' days'));
}
        else
        {
            $object->return_days = $kit->months;
            $object->apy = $kit->percantage;
            $object->d_apy = ($kit->percantage / $kit->months);
            $object->return_date = date('Y-m-d H:i:s', strtotime($date. ' + '.$kit->months.' days'));
        }

        $object->topup_type = $topup_type;
        $object->description = $description;

        $object->save();

        return $object;
    }

    public function processreferralcommission($member_id, $level, $amount, $from_id, $kit_id, $created_at)
    {
        $walletCon = app('App\Http\Controllers\Users\EarningWalletController');

        $member = User::where('id','=',$member_id)->first();

        $from_member = User::where('id','=',$from_id)->first();

        $from_address = obscureAddress($from_member->username);

        if($member != null)
        {
            $levels = config('income.direct_sponsor_levels');
            $per = $levels[$level] ?? 0;

            $description = 'Direct Sponsor Income (Level '.$level.') From '.$from_address;
            $commission = ($amount * $per) / 100;

            $earning_type = 1;

            if($member->kit_id > 0 || ($member->activation_status ?? '') === 'active')
            {
                if($commission > 0)
                {
                    // 2nd/3rd direct income may divert into Auto Upgrade wallet instead of main wallet.
                    $diverted = false;
                    if ($level === 1) {
                        $diverted = app(\App\Services\AutoUpgradeService::class)
                            ->maybeDivertToAutoUpgrade($member, (int) $from_id, (float) $commission, $description);
                    }

                    if (!$diverted) {
                        $walletCon->addearningwalletlog($member->id, 1, $earning_type, $description, $commission, 0, 0, $created_at);
                    }
                }
            }

            $level++;

            if($member->referral_id > 0 && $level <= 3)
            {
                $this->processreferralcommission($member->referral_id, $level, $amount, $from_id, $kit_id, $created_at);
            }
        }
    }
    
    public function getTopUplines($uplines, $count)
    {
		$list_parents = explode(",", $uplines);

		if(count($list_parents) > $count)
        {
			return array_slice($list_parents,-$count, $count);
		}
		else
        {
			return $list_parents;
		}
	}

    // 

    public function adminStakeActivation($member_id, $kit_id, $amount, $topup_type, $description)
    {
        $walletCon = app('App\Http\Controllers\Users\EarningWalletController');

        $kit = ($topup_type == 0) ? $this->resolveRoiTierKit($amount) : null;

        if($kit == null)
        {
            $kit = StakeMaster::where('id','=',$kit_id)->first();
        }

        $kit_id = $kit->id;

        //
        $coin_rate = getcoinrate();
        
        $date = date("Y-m-d H:i:s");
        
        $invoice_no = self::generateInvoice();
    
        $object = new StakeRequest;
    
        $object->payment = 1;
        $object->invoice_no = $invoice_no;
        $object->member_id = $member_id;
        
        $object->stake_id = $kit_id;
        $object->amount = $amount;
        
        $object->coin_rate = $coin_rate;
        $object->stake_coin = number_format((float)$amount/$coin_rate, 8, '.', '');
        
        $object->hash = $description;
        $object->status = 2;
        
        $object->return_date = date('Y-m-d H:i:s', strtotime($date. ' + '.$kit->months.' days'));
        $object->apy = $kit->percantage;
        $object->d_apy = ($kit->percantage / $kit->months);
        
        $object->save();
        //

        $member = User::where('id','=',$member_id)->first();
        
        if($member != null)
        {
            $member->kit_id = $kit->id;
            
            if($member->activation_date == null)
            {
                $member->activation_date = date("Y-m-d H:i:s");
            }
            
            $member->self_investment = ($member->self_investment+$amount);
            
            $member->save();

            // Direct ROI display fields only (no income credit).
            app(\App\Services\DirectRoiService::class)->markActiveAndRefreshSponsor($member->fresh());
        }

        // Add Purchased Kit Log
        $log = $this->addpurchasedkitlog($member->id, $kit->id, $amount, $topup_type, $description, $object->id);
        
        if($topup_type == 0)
        {
            // Update Level Business
            User::whereRaw('FIND_IN_SET(id,"'.$member->referral_uplines.'")')
                ->update(['team_investment'=> DB::raw('team_investment+'.$amount)]);
        }
        
        if($topup_type == 0)
        {
            // Update Direct Business
            if($member->referral_id > 0)
            {
                $refer = User::where('id','=',$member->referral_id)->first();
                if($refer != null)
                {
                    $refer->direct_business = ($refer->direct_business+$amount);
                    $refer->save();

                    // Legacy Booster Income (disabled for Finex — see income.legacy_booster_enabled)
                    if (config('income.legacy_booster_enabled', false)) {
                        $this->processBoosterIncome($refer);
                    }
                }

                if($refer != null && $refer->kit_id > 0)
                {
                    if (config('income.direct_sponsor_enabled', false)) {
                        self::processreferralcommission($refer->id, 1, $amount, $member->id, $kit_id, date("Y-m-d H:i:s"));
                    } else {
                        app(\App\Services\AutoUpgradeService::class)
                            ->creditFromDirectActivation($refer, (int) $member->id, (float) $amount);
                    }
                }
            }
        }
    }    
    
    public function setBooster()
    {
        $referral_id = 397;
        
        $refer = User::where('id','=',$referral_id)->first();
        
        $last_date = date('Y-m-d H:i:s', strtotime($refer->activation_date. ' + 48 hours'));
    
        $booster_direct = User::where('referral_id','=',$refer->id)
                              ->where('activation_date','>=',$refer->activation_date)
                              ->where('activation_date','<=',$last_date)
                              ->where('kit_id','>',0)
                              ->count();
                              
        if($booster_direct >= 2 && $refer->is_booster == 0)
        {
            $refer->is_booster = 1;
            $refer->booster_on = date("Y-m-d H:i:s");
            $refer->save();
        }  
    }
    
    public function capitalWithdrawal(Request $request)
    {
        try {
            $v = Validator::make($request->all(), [
                'withdrawal_id' => 'required',
            ]);

            if ($v->fails())
            {
                return response()->json(array('success'=>false, 'error'=>'Invalid request data send.'), 200);
            }

            if (Auth::user() == null)
            {
				return response()->json(array('success'=>false,'error'=> 'Session is expired.'), 200);
			}

            $withdrawal_id = $request->get('withdrawal_id');

            // A capital withdrawal must belong to the requesting member - this ownership check was previously missing.
            $object = UserStaked::where('id','=',$withdrawal_id)->where('member_id','=',Auth::user()->id)->first();

            if($object == null)
            {
                return response()->json(array('success'=>false,'error'=> 'Invalid capital withdrawal'), 200);
            }

            if($object->topup_type != 0)
            {
                return response()->json(array('success'=>false,'error'=> 'Invalid capital withdrawal'), 200);
            }

            if($object->is_deleted > 0)
            {
                return response()->json(array('success'=>false,'error'=> 'Already capital withdrawal'), 200);
            }

            $window_months = config('income.capital_withdrawal_window_months');
            $cutoff = date('Y-m-d H:i:s', strtotime($object->created_at.' + '.$window_months.' months'));

            if(date('Y-m-d H:i:s') > $cutoff)
            {
                return response()->json(array('success'=>false,'error'=> 'Capital withdrawal window has closed for this stake; only income withdrawals remain available.'), 200);
            }

            $charge_percent = config('income.capital_withdrawal_charge_percent');
            $charge = $object->paid_amount * $charge_percent / 100;
            $net = $object->paid_amount - $charge;

            DB::beginTransaction();

            try {
                $wlog = new WithdrawalLog;
                $wlog->mode = 2; // 2 = Capital withdrawal
                $wlog->w_type = 1;
                $wlog->ref_id = 0;
                $wlog->member_id = Auth::user()->id;
                $wlog->staked_user_id = $object->id;
                $wlog->amount = $object->paid_amount;
                $wlog->admin = $charge;
                $wlog->charge_percent = $charge_percent;
                $wlog->net = $net;
                $wlog->rate = 0;
                $wlog->payable = $net;
                $wlog->address = Auth::user()->wallet_addr;
                $wlog->status = 0; // pending admin approval, same flow as income withdrawals
                $wlog->is_wallet = 0;
                $wlog->auto_withdraw = 0;
                $wlog->created_at = date('Y-m-d H:i:s');
                $wlog->updated_at = date('Y-m-d H:i:s');
                $wlog->save();

                $object->is_deleted = 1;
                $object->up_status = 1;
                $object->capital_withdrawn_at = date('Y-m-d H:i:s');
                $object->save();

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

            return response()->json(array('success'=>true, 'message'=>'Capital withdrawal request submitted for admin approval.', 'error'=>''), 200);
        } catch(Exception $exception) {
            Log::error($exception);
            return response()->json(array('success'=>false,'error'=> 'An error occurred processing'), 200);
        }
    }

    // Evaluates the Booster Income 48-hour window exactly once per member.
    // Called daily from ProcessDaily; is a no-op for members already evaluated (booster_evaluated_at is set) or whose window hasn't closed yet.
    public function runBoosterEvaluation()
    {
        $window_hours = config('income.booster_window_hours');
        $tiers = config('income.booster_tiers'); // ordered highest-directs-first, e.g. [10=>0.25, 7=>0.20, 5=>0.15, 3=>0.10]

        $members = User::whereNotNull('activation_date')
                        ->whereNull('booster_evaluated_at')
                        ->whereRaw('DATE_ADD(activation_date, INTERVAL '.$window_hours.' HOUR) <= NOW()')
                        ->get();

        foreach($members as $member)
        {
            $window_end = date('Y-m-d H:i:s', strtotime($member->activation_date.' + '.$window_hours.' hours'));

            $own_stake = UserStaked::where('member_id','=',$member->id)->where('topup_type','=',0)->orderBy('created_at','asc')->first();
            $own_amount = $own_stake ? $own_stake->paid_amount : 0;

            $qualified_directs = User::where('referral_id','=',$member->id)
                                      ->where('activation_date','>=',$member->activation_date)
                                      ->where('activation_date','<=',$window_end)
                                      ->where('kit_id','>',0)
                                      ->get();

            $count = 0;

            foreach($qualified_directs as $direct)
            {
                $direct_stake = UserStaked::where('member_id','=',$direct->id)->where('topup_type','=',0)->orderBy('created_at','asc')->first();
                $direct_amount = $direct_stake ? $direct_stake->paid_amount : 0;

                if($direct_amount >= $own_amount)
                {
                    $count++;
                }
            }

            foreach($tiers as $required_directs => $bonus_percent)
            {
                if($count >= $required_directs)
                {
                    BoosterAchiever::updateOrCreate(
                        ['member_id' => $member->id],
                        ['tier_directs' => $required_directs, 'bonus_percent' => $bonus_percent, 'achieved_at' => date('Y-m-d H:i:s')]
                    );

                    break;
                }
            }

            $member->booster_evaluated_at = date('Y-m-d H:i:s');
            $member->save();
        }
    }

    // ==================================================================================================================================================================
    
    public function runReferralEarning()
    {
        $walletCon = app('App\Http\Controllers\Users\EarningWalletController');

        $objects = UserStaked::where('earn_status', '=', 0)->get();

        foreach($objects as $log)
        {
            $log->earn_status = 1;
            $log->save();
            
            $member = User::where('id','=',$log->member_id)->first();

            if($member->referral_id > 0)
            {
                // self::processreferralcommission($member->referral_id, 1, $log->paid_amount, $member->id, $log->kit_id, date("Y-m-d H:i:s"));
            }
        }
    }

    public function runDailyROI()
    {
        // New Finex plan: Direct ROI % × active slots, Level ROI to uplines, duplicate-safe logs.
        return app(\App\Services\DailyRoiService::class)->distribute();
    }
    
    public function processlevelcommission($member_id, $level, $amount, $from_id, $kit_id, $created_at)
    {
        // Legacy entry point retained for compatibility.
        // New Level ROI Income is distributed from DailyRoiService → LevelRoiService.
        return;
    }

    //

    public function tempSetReferralLevelEarning()
    {
        $member_id  = $_REQUEST["member_id"];
        
        $member = User::where('id','=',$member_id)->first();
        
        $refer = User::where('id','=',$member->referral_id)->first();
        
        $stakeduser = UserStaked::where('member_id', '=', $member_id)->first();
        
        self::processreferralcommission($refer->id, 1, $stakeduser->paid_amount, $member->id, $stakeduser->kit_id, $stakeduser->created_at);
    }
    
    //
    
    public function businessMinus()
    {
        $member_id = 0;
        
        $amount = 0;
        
        $member = User::where('id','=',$member_id)->first();
        
        if($member != null)
        {
            $member->self_investment -= $amount;
            $member->all_investment -= $amount;
            $member->save();
        }

        // Update Level Business
        User::whereRaw('FIND_IN_SET(id,"'.$member->referral_uplines.'")')
            ->update(['team_investment'=> DB::raw('team_investment-'.$amount)]);

        // Update All Business
        User::whereRaw('FIND_IN_SET(id,"'.$member->referral_uplines.'")')
            ->update(['all_investment'=> DB::raw('all_investment-'.$amount)]);

        // Update Direct Business
        if($member->referral_id > 0)
        {
            $refer = User::where('id','=',$member->referral_id)->first();
            if($refer != null)
            {
                $refer->direct_business = ($refer->direct_business-$amount);
                $refer->save();
            }
        }    
    }
    
    // temp run set
    public function runTempDailyROI()
    {
        $date = '2025-03-04 00:05:00';
        
        $walletCon = app('App\Http\Controllers\Users\EarningWalletController');
        
        // $objects = UserStaked::whereRaw('FIND_IN_SET(member_id,"1632,1347,1348,1349,1350,1351,1352,1354,1355,1356")')->where('return_days', '>', 0)->where('topup_type', '!=', 1)->where('is_deleted', '=', 0)->get();

        $objects = UserStaked::where('return_days', '>', 0)->where('topup_type', '!=', 1)->where('is_deleted', '=', 0)->where('updated_at', '<=', '2025-03-03 23:59:59')->get();
 
        foreach($objects as $log)
        {
            // Log::info('member '.$log->member_id);
            $member = User::where('id','=',$log->member_id)->first();

            $kit = StakeMaster::where('id','=',$log->kit_id)->first();

            if($member->is_booster > 0)
            {
                $per = $kit->percantage + 0.1;

                $description = 'Booster Dividend From $'.$log->paid_amount;
            
                $commission = $log->paid_amount * $per / 100;

                $earning_type = 2;
            }
            else
            {
                $per = $kit->percantage;

                $description = 'Daily Dividend From $'.$log->paid_amount;
            
                $commission = $log->paid_amount * $per / 100;

                $earning_type = 1;
            }
            
            // Log::info('commission '.$commission);
            
            $dashboardCon = app('App\Http\Controllers\Users\DashboardController');
            $remain_commission = $dashboardCon->check2xEarningLimit($log->member_id, $commission);

            if($remain_commission > 0)
            {
                $walletCon->addearningwalletlog($log->member_id, 1, $earning_type, $description, $remain_commission, 0, 0, $date); 

                
                $log->receive_return += $remain_commission;
                $log->save();

                $refer = User::where('id','=',$member->referral_id)->first();
                if($refer != null && $log->topup_type == 0)
                {
                    $this->processlevelcommission($refer->id, 1, $remain_commission, $member->id, $log->kit_id, $date);
                }   
            }
            else
            {
                $walletCon->addearningwalletlog($log->member_id, 3, $earning_type, $description, $commission, 0, 0, $date); 
            }
        }
    }
}
