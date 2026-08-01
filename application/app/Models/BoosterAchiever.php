<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoosterAchiever extends Model
{
    //
	protected $table = 'booster_achievers';

	protected $primaryKey = 'id';

	protected $fillable = ['member_id', 'tier_directs', 'bonus_percent', 'achieved_at'];

	public function member()
	{
		return $this->belongsTo(User::class, 'member_id');
	}
}

?>