<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StakeRequest extends Model
{
    //
	protected $table = 'staked_requests';
	
	protected $primaryKey = 'id';	
	
	public function member()
	{
		return $this->belongsTo(User::class, 'member_id');
	}
	
	public function stake()
	{
		return $this->belongsTo(StakeMaster::class, 'stake_id');
	}
}

?>