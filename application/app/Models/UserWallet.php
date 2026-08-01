<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWallet extends Model
{
    //
	protected $table = 'user_wallets';
	
	protected $primaryKey = 'id';	

	protected $hidden = [
        'private_key',
    ];
	
}

?>