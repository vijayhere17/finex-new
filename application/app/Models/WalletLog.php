<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletLog extends Model
{
    //
	protected $table = 'wallet_logs';
	
	protected $primaryKey = 'id';	

	public function member()
	{
		return $this->belongsTo(User::class, 'member_id');
	}
}

?>