<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TurnoverRewardMaster extends Model
{
    //
	protected $table = 'turnover_reward_masters';

	protected $primaryKey = 'id';

	protected $fillable = [
		'milestone_order',
		'title',
		'required_directs',
		'required_team',
		'required_self_business',
		'required_team_business',
		'turnover_amount',
		'cash_reward',
		'weekly_salary',
	];
}

?>