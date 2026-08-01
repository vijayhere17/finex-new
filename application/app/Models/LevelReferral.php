<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LevelReferral extends Model
{
    //
	protected $table = 'level_referrals';

	protected $primaryKey = 'id';

	public function member()
	{
		return $this->belongsTo(User::class, 'member_id');
	}	
}
?>