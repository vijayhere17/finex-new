<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LifeTimeAchiever extends Model
{
    //
	protected $table = 'life_time_achiever';
	
	protected $primaryKey = 'id';	
	
	public function reward()
	{
		return $this->belongsTo(LifeTimeReward::class, 'life_time_reward');
	}
	
	public function member()
	{
		return $this->belongsTo(User::class, 'member_id');
	}
}

?>