<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentList extends Model
{
    //
	protected $table = 'parent_lists';
	
	protected $primaryKey = 'member_id';	

	public function member()
	{
		return $this->belongsTo(User::class, 'member_id');
	}	
	
	public function user() {
        return $this->belongsTo(User::class, 'member_id', 'id');
    }
}
