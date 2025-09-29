<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantBill extends Model
{
    use HasFactory;

    protected $table = 'tenant_bills';

    protected $fillable = [
        'lease_id',
        'tenant_id', 
        'user_id',
        'amount',
        'billing_period_start',
        'billing_period_end',
        'billing_type',
        'status',
        'billing_year',
        'billing_month',
        'days_used',
    ];

    public function lease()
    {
        return $this->belongsTo(Lease::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class); // optional: who created the bill
    }
}
