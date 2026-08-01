<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoiTierMaster extends Model
{
    //
	protected $table = 'roi_tier_masters';

	protected $primaryKey = 'id';

	protected $fillable = [
    'min_amount',
    'max_amount',
    'daily_percent',
    'cap_multiplier',
    'is_active'
];
}

?>