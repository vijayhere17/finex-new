<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MalaysiaAchiever extends Model
{
    //
	protected $table = 'malaysia_achiever';
	
	protected $primaryKey = 'id';	
	
	public function member()
	{
		return $this->belongsTo(User::class, 'member_id');
	}
}

?>