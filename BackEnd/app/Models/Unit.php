<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_name',          // room number or name
        'size_sqm',           // size in square meters
        'type',               // e.g., single, double, office
        'with_vat',           // boolean if rent includes VAT
        'monthly_rent',       // rent amount
        'security_deposit',   // deposit amount
        'water_and_electric', // total utilities if combined
        'water',              // water charge (if separate)
        'electric',           // electricity charge (if separate)
        'parking',            // parking fee (if any)
        'other',              // any other charges
        'lease_term',         // duration in months/years
        'agreement',          // path to lease agreement file (if needed)
        'status',             // available, occupied, etc.
    ];

    // Relationships
    public function leases()
    {
        return $this->hasMany(Lease::class); // A room can have many leases over time
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class); // A room can have many maintenance requests
    }
}
