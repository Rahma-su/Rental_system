<?php

namespace App\Models;
use App\Models\TenantForm;
use App\Models\Units;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lease extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'tenant_id',
        'lease_start_date',
        'lease_end_date',
        'monthly_rent',
        'security_deposit',
        'payment_frequency',
        'grace_period_days',
        'late_fee_penalty_percent',
        'lease_agreement',
        'notes',
        'water_and_electric',
        'water',
        'electric',
        'parking',
        'other',
    ];

    // Relationships
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function tenant()
{
    return $this->belongsTo(TenantForm::class, 'tenant_id');
}

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}

