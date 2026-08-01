<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferWalletLog extends Model
{
    //
	protected $table = 'transfer_wallet_log';
	
	protected $primaryKey = 'id';	
	
	public function member()
	{
		return $this->belongsTo(User::class, 'member_id');
	}
}

?>