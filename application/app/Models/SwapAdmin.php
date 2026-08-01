<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class SwapAdmin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'id';

    protected $guard = 'swapadmin';
    
    protected $fillable = [
        'name', 'email', 'password',
    ];
    
    protected $hidden = [
        'password',
    ];
}