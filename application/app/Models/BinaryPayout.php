<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BinaryPayout extends Model
{
    //
	protected $table = 'binary_payouts';
	
	protected $primaryKey = 'id';	
	
	public function member()
	{
		return $this->belongsTo(User::class, 'member_id');
	}					
}
