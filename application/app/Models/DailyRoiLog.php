<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyRoiLog extends Model
{
    protected $table = 'daily_roi_logs';

    protected $fillable = [
        'member_id', 'stake_id', 'stake_amount', 'roi_percent', 'amount', 'roi_date', 'day_number',
    ];
}
