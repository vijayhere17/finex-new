<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoUpgradeLog extends Model
{
    protected $table = 'auto_upgrade_logs';

    protected $fillable = [
        'member_id', 'event_type', 'slot_number', 'amount', 'balance_after', 'from_id', 'stake_id', 'description',
    ];
}
