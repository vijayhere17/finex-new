<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawalLog extends Model
{
    //
	protected $table = 'withdrawal_requests';
	
	protected $primaryKey = 'id';	

	public function member()
	{
		return $this->belongsTo(User::class, 'member_id');
	}
}

?>