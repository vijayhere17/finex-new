<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CraditDebitLog extends Model
{
    //
	protected $table = 'credit_debit_by_admin';
	
	protected $primaryKey = 'id';	
	
	public function member()
	{
		return $this->belongsTo(User::class, 'member_id');
	}
}

?>