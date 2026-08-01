<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TurnoverRewardAchiever extends Model
{
    //
	protected $table = 'turnover_reward_achievers';

	protected $primaryKey = 'id';

	public function reward()
	{
		return $this->belongsTo(TurnoverRewardMaster::class, 'reward_id');
	}

	public function member()
	{
		return $this->belongsTo(User::class, 'member_id');
	}
}

?>