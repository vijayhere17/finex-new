<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BalanceView extends Model
{
    //
	protected $table = 'balance_summary';
	
	public function member()
	{
		return $this->belongsTo(User::class, 'member_id');
	}
}

?>