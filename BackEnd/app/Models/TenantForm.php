<?php

namespace App\Models;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class TenantForm extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'full_name',
        'business_name',
        'gender',
        'phone',
        'email',
        'national_id',
        'tin_no',
        'nationality',
        'emergency_contact_name',
        'emergency_contact_phone',
        'address',
        'notes',
    ];
protected static function booted()
    {
        static::created(function ($tenant) {
            // Create a wallet automatically for the tenant
            Wallet::create([
                'tenant_id' => $tenant->id,
                'balance' => 0,
                'deposit_balance' => 0,
            ]);
        });
    }

    // Relationships
    public function leases()
    {
        return $this->hasMany(Lease::class); // Tenant can have multiple leases over time
    }

    public function payments()
    {
        return $this->hasMany(Payment::class); // Tenant payments
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class); // Tenant maintenance requests
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class); // Tenant wallet
    }
    public function user()
{
    return $this->belongsTo(User::class);
}

}
