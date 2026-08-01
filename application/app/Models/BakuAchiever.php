<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BakuAchiever extends Model
{
    //
	protected $table = 'baku_achiever';
	
	protected $primaryKey = 'id';	
	
	public function member()
	{
		return $this->belongsTo(User::class, 'member_id');
	}
}

?>