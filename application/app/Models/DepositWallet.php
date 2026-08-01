<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepositWallet extends Model
{
    //
	protected $table = 'dwallet_logs';
	
	protected $primaryKey = 'id';	

	public function member()
	{
		return $this->belongsTo(User::class, 'member_id');
	}
}

?>