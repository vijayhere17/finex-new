<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockchainTransaction extends Model
{
    protected $table = 'blockchain_transactions';

    protected $fillable = [
        'member_id',
        'tx_type',
        'tx_hash',
        'onchain_investment_id',
        'offchain_ref_id',
        'amount',
        'status',
        'payload',
        'error_message',
    ];

    protected $casts = [
        'payload' => 'array',
        'amount' => 'float',
    ];

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }
}
