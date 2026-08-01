<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StakeMaster extends Model
{
    //
	protected $table = 'stake_masters';
	
	protected $primaryKey = 'id';	
	
	protected $fillable = ['id', 'name', 'amount', 'coin', 'percantage', 'months', 'direct_ref', 'bonus','cap_multiplier', 'limit', 'ptype', 'locking', 'is_admin', 'is_travel', 'dmc_commission', 'left_dmc', 'right_dmc', 'dmc'];
}

?>