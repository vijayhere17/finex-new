<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    //
    protected $table = 'support_tickets';

    protected $primaryKey = 'id';

    public function member()
	{
		return $this->belongsTo('App\Models\User', 'member_id');
	}

}
