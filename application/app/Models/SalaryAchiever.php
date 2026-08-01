<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryAchiever extends Model
{
    //
	protected $table = 'salary_achiever';
	
	protected $primaryKey = 'id';	
	
	public function salarymaster()
	{
		return $this->belongsTo(SalaryMaster::class, 'salary_id');
	}
	
	public function member()
	{
		return $this->belongsTo(User::class, 'member_id');
	}
}

?>