<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketMsg extends Model
{
    //
    protected $table = 'support_messages';

    protected $primaryKey = 'id';

    public function frommember()
	{
		return $this->belongsTo('App\Models\User', 'from_id');
	}

	public function tomember()
	{
		return $this->belongsTo('App\Models\User', 'to_id');
	}

	public function ticket()
	{
		return $this->belongsTo('App\Models\Ticket', 'ticket_id');
	}
}
