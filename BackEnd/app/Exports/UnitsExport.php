<?php

namespace App\Exports;

use App\Models\Unit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UnitsExport implements FromCollection , WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Unit::select(
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
        'status',)->get();
    }
    public function headings(): array
    {
       return[
        'Unit_name',          
        'Size_sqm',           
        'Type',              
        'With_vat',           
        'Monthly_rent',       
        'Security_deposit',   
        'Water_and_electric', 
        'Water',             
        'Electric',           
        'Parking',            
        'Other',              
        'Lease_term',         
        'Agreement',          
        'Status',
       ];
    }
}
