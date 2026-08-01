<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DMCAchiever extends Model
{
    //
	protected $table = 'dmc_achievers';
	
	protected $primaryKey = 'id';	
	
	public function dmcmaster()
	{
		return $this->belongsTo(DMCMaster::class, 'dmc_id');
	}
	
	public function member()
	{
		return $this->belongsTo(User::class, 'member_id');
	}
}

?>