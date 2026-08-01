<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpilloverLog extends Model
{
    protected $table = 'spillover_logs';

    protected $fillable = [
        'member_id', 'from_sponsor_id', 'to_sponsor_id', 'reason',
    ];
}
