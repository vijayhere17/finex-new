<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopupByWalletLog extends Model
{
    //
	protected $table = 'topup_by_wallet_log';
	
	protected $primaryKey = 'id';	

	public function member()
	{
		return $this->belongsTo(User::class, 'member_id');
	}
	
	public function topupby()
	{
		return $this->belongsTo(User::class, 'topup_by');
	}
}

?>