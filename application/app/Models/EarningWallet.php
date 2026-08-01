<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EarningWallet extends Model
{
    //
	protected $table = 'ewallet_logs';
	
	protected $primaryKey = 'id';	

	public function member()
	{
		return $this->belongsTo(User::class, 'member_id');
	}
}

?>