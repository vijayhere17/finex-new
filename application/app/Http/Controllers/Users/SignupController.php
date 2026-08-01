<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\ParentList;
use App\Models\BinaryPoints;
use App\Models\LevelReferral;

use Hash;
use Log;
use DB;

class SignupController extends Controller
{
    //
    
    public function updateall(){
        
        User::where('id','>',84230)->orderBy('id')->chunk(5000, function($data) {
            foreach($data as $member){
                $mem = User::find($member->id);
                $mem->password = Hash::make($mem->password);
                $mem->save();
                
                Log::info('UP '.$mem->id);
            }
        });
    }
    
    public function sendmail(){
        $member = self::getMemberById(2);
        $emailrCon = app('App\Http\Controllers\EmailController');
        $password = '321321';
        $send = $emailrCon->sendRegistrationEmail($member, $password);
    }

    public function signup(){
        $page_titel = 'Sign Up';
        $stakeCon = app('App\Http\Controllers\Users\StakeController');

        $to_address = $stakeCon->getDepositWallet();
        $usdt_con_addr = $stakeCon->getUsdtContract();
        $usdt_con_abi = $stakeCon->getUsdtAbi();
        $registration_fee = 0;

        return view('users.sign-up')->with([
            'page_titel' => $page_titel,
            'to_address' => $to_address,
            'usdt_con_addr' => $usdt_con_addr,
            'usdt_con_abi' => $usdt_con_abi,
            'registration_fee' => $registration_fee,
        ])->toJS();
    }

    public function getMemberById($id){
		$object = User::find($id);
		return $object;
	}

    public function checkSponorid(Request $request){
        try{
            $request->validate([
                'sponsor_id' => 'required',
            ]);

            $data = $request->all();

            $sponsor = User::where('username','=',$data["sponsor_id"])->first();
            if($sponsor == null){
                return response()->json(array('success'=> false, 'error'=> 'Invalid sponsor id'), 200);
            }
            
            $name = $sponsor->firstname.' '.$sponsor->lastname;
            
            return response()->json(array('success'=> true, 'error'=> '', 'name'=>$name), 200);
        }catch(\Exception $exception){
            Log::error($exception);
            return response()->json(array('success'=>false,'error'=> 'Invalid request data send.'), 200);
        }
    }

    public function submitSignup(Request $request){
        try{
            $request->validate([
                // 'firstname' => 'required',
                // 'lastname' => 'required',
                'userwallet' => 'required',
                'sponsor_id' => 'required',
                'leg' => 'required'
            ]);

            $data = $request->all();
            
            $isapi = $data["isapi"];

            $sponsor = User::where('username','=',$data["sponsor_id"])->first();
            if($sponsor == null)
            {
                return response()->json(array('success'=> false, 'error'=> 'Invalid sponsor id'), 200);
            }

            $chk_wallet = User::where('username','=',$data["userwallet"])->first();
            if($chk_wallet != null)
            {
                return response()->json(array('success'=> false, 'error'=> 'Wallet address already registered'), 200);
            }

            $referral_id = $sponsor->id;

            $parent_id = null; // $this->getEligibleParent($referral_id, $data["leg"]);

			$parents = null; // $this->getParents($parent_id);
            
            $referral_uplines = $this->getReferralUplines($referral_id);

            $member = User::create([
                // 'firstname' => $data['firstname'],
                // 'lastname' => $data['lastname'],
                // 'email' => $data['email'],
                'username' => $data['userwallet'],
                'leg' => $data["leg"],
                'referral_id' => $referral_id,
                'referral_uplines' => $referral_uplines
            ]);

            $this->processReferralUplines($member->id, $referral_uplines);

            // $this->setMemberListUpline($member->id, $member->parent_id, $data["leg"]);

            // $this->setMemberPointsEmpty($member->id);    
            
            $emailrCon = app('App\Http\Controllers\EmailController');
            // $send = $emailrCon->sendRegistrationEmail($member, $data['password']);
            
            $parts = explode('-', $member->username);
            $cleanString = $parts[0];
            
            $umember = User::find($member->id);
            $umember->wallet_addr = $cleanString;
            // Registration Fee = $1 paid to company deposit wallet (package activation unchanged)
            $umember->registration_fee = 0;
$umember->save();
    
            return response()->json(array('success'=> true, 'error'=> ''), 200);
        }catch(\Exception $exception){
            Log::error($exception);
            return response()->json(array('success'=>false,'error'=> 'Invalid request data send.'), 200);
        }
    }

    public function getDirectChildObj($parent_id, $leg){
		$object = User::where('parent_id', '=', $parent_id)
					  ->where('leg', '=', $leg)
					  ->first();
		return $object;
	}

    public function getEligibleParent($referral_id, $leg){
		$parent_id = $referral_id;
		$child = $this->getDirectChildObj($parent_id, $leg);

        while($child != null){
			$parent_id = $child->id;
			$child = $this->getDirectChildObj($parent_id, $leg);
		}
		return $parent_id;
	}

    public function getParents($parent_id){
		$object = User::find($parent_id);
		if($object->parents == null){
			return $parent_id;
		}
		else{
			return $object->parents. ',' .$parent_id;
		}
	}

    public function generateUsername(){
        $username = 'ED'.rand(pow(10, 6 - 1), pow(10, 6) -1);

        $check = User::where('username','=',$username)->first();

        if($check != null)
        {
            return $this->generateUsername();
        }

        return $username;
    }

    public function getReferralUplines($referral_id){
		$object = User::find($referral_id);
		return ($object != null && $object->referral_uplines != null) ? ($object->referral_uplines.','.$referral_id) : $referral_id;
	}

    public function processReferralUplines($member_id, $referral_uplines)
    {
		$list_referrals = explode(",", $referral_uplines);
		$level = count($list_referrals);
	
        foreach($list_referrals as $ref)
		{
			$reff = $this->getMemberById($ref);
			$this->insertUpdateLevelReferral($reff->id, $level, $member_id);
			$level--;
		}
	}

    public function insertUpdateLevelReferral($reff_id, $level, $member_id)
    {
		$object = LevelReferral::where('member_id', '=', $reff_id)
								->where('level', '=', $level)
								->first();
		if($object != null)
        {
			$object->downlines = ($object->downlines) . ',' . $member_id;
			$object->team_count = ($object->team_count) + 1;
		}
        else
        {
			$object = new LevelReferral;
			$object->member_id = $reff_id;
			$object->level = $level;
			$object->team_count = 1;
			$object->downlines = $member_id;
		}

		$object->save();
	}

    public function setMemberListUpline($member_id, $parent_id, $leg){
	    $parent = ParentList::find($parent_id);

	    if($parent != null){
    	    if($leg == 'L'){
    			$ob = new ParentList;
    			$ob->member_id = $member_id;
    			$ob->L_parents = $parent->L_parents.','.$parent_id;
    			$ob->R_parents = $parent->R_parents;
    			$ob->save();
    		}

    		if($leg == 'R'){
    			$ob = new ParentList;
    			$ob->member_id = $member_id;
    			$ob->L_parents = $parent->L_parents;
    			$ob->R_parents = $parent->R_parents.','.$parent_id;
    			$ob->save();

    		}
	    }
	}

    public function setMemberPointsEmpty($member_id){
		$check = BinaryPoints::where('member_id', '=', $member_id)->first();
		
        if($check == null){
			$obj = New BinaryPoints;
			$obj->member_id =  $member_id;
			$obj->left_points =  0;
			$obj->right_points = 0;
			$obj->left_cal_points = 0;
			$obj->right_cal_points = 0;
			$obj->save();
		}
	}
	
	//
	
	public function temlLevelReferralSet() 
    {
        $members = User::where('id','>',1)->orderBy('referral_id','asc')->get();

        foreach ($members as $member) 
        {
            $referral_uplines = $this->getReferralUplines($member->referral_id);

            $member->referral_uplines = $referral_uplines;
            $member->save();

            // $this->processReferralUplines($member->id, $member->referral_uplines);
        }
    }

    public function setMemberUpline()
    {
        DB::statement('SET SESSION group_concat_max_len = 10000000');

        $member_id = 136;
  
        $downlines = User::where('referral_id', '=', $member_id)->select(DB::raw('group_concat(id) as downlines'))->first();
        $downlines = $downlines->downlines;

        $this->setUpline($downlines);
    }

    public function setUpline($downlines)
    {
        DB::statement('SET SESSION group_concat_max_len = 10000000');

        $childs = User::whereRaw('FIND_IN_SET(id,"'.$downlines.'")')->orderBy('id','asc')->get();

        foreach ($childs as $child) 
        {
            $referral_uplines = $this->getReferralUplines($child->referral_id);

            $child->referral_uplines = $referral_uplines;
            $child->save();
        }        

        $downline = User::whereRaw('FIND_IN_SET(referral_id,"'.$downlines.'")')->orderBy('id','asc')->select(DB::raw('group_concat(id) as downline'))->first();
        $downline = $downline->downline;
        
        if($downline != null)
        { 
           $this->setUpline($downline);
        }
    }
}
