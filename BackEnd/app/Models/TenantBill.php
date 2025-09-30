<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantBill extends Model
{
    use HasFactory;

    protected $table = 'tenant_bills';

    // 
    protected $fillable = [
        'lease_id',
        'tenant_id',
        'user_id',
        'amount',
        'paid_amount',
        'months_count',
        'paid_months',
        'billing_period_start',
        'billing_period_end',
        'billing_type',
        'status',
        'billing_year',
        'billing_month',
        'days_used',
    ];
    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'billing_period_start' => 'date',
        'billing_period_end' => 'date',
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
