<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'id';

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'username',
        'leg',
        'referral_id',
        'referral_uplines',
        'registration_fee',
        'activation_status',
        'qualified_active_directs',
        'direct_roi_percent',
        'auto_upgrade_balance',
        'current_slot',
        'next_slot',
    ];

    protected $hidden = [
        'password',
        'google_secret'
    ];

    protected $casts = [
        'password' => 'hashed',
        'qualified_active_directs' => 'integer',
        'direct_roi_percent' => 'float',
    ];

    // ---------------------------------------------------------------------------------------

    public function referral()
	{
		return $this->belongsTo(User::class, 'referral_id');
	}

    public function kit(){
        return $this->belongsTo(StakeMaster::class, 'kit_id');
    }

    public function binaryPoint(){
        return $this->belongsTo(BinaryPoints::class, 'id', 'member_id');
    }
    
    public function parentList(){
        return $this->hasOne(ParentList::class, 'member_id', 'id');
    }
}
