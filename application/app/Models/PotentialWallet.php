<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PotentialWallet extends Model
{
    //
	protected $table = 'pwallet_logs';
	
	protected $primaryKey = 'id';	

	public function member()
	{
		return $this->belongsTo(User::class, 'member_id');
	}
}

?>