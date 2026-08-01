<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyROIRate extends Model
{
    protected $table = 'monthly_roi_rates';

    protected $fillable = [
        'start_date',
        'end_date',
        'daily_roi',
        'status',
    ];
}