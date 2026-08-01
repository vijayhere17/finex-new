<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserStaked extends Model
{
    //
	protected $table = 'staked_users';
	
	protected $primaryKey = 'id';	

	public function member()
	{
		return $this->belongsTo(User::class, 'member_id');
	}
	
	public function kit()
	{
		return $this->belongsTo(StakeMaster::class, 'kit_id');
	}
}

?>