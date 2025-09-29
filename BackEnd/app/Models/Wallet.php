<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'balance',
        'deposit_balance',
    ];

    // Each wallet belongs to a tenant
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // One wallet can have many transactions
    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }
}
