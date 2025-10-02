<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_name',          
        'size_sqm',           
        'type',              
        'with_vat',           
        'monthly_rent',       
        'security_deposit',   
        'water_and_electric', 
        'water',             
        'electric',           
        'parking',            
        'other',              
        'lease_term',         
        'agreement',          
        'status',           
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
