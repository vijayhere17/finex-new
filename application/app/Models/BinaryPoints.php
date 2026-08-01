<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BinaryPoints extends Model
{
	protected $table = 'binary_points';

	protected $primaryKey = 'member_id';

	public function member()
	{
		return $this->belongsTo(User::class, 'member_id');
	}	
}
