<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id', // we link to wallet
        'lease_id',
        'type', // e.g., 'credit' or 'debit'
        'amount',
        'debited', // boolean: true if deducted
        'description',
        'payment_method',
        'payment_date',
        'reference_number',
        'remarks',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function lease()
    {
        return $this->belongsTo(Lease::class);
    }
}
