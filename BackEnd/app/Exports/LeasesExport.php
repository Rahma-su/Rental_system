<?php

namespace App\Exports;

use App\Models\Lease;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LeasesExport implements FromCollection , WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Lease::select(
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
        )->get();
    }
    public function headings(): array
    {
       return[
        'Unit_id',
        'Tenant_id',
        'Lease_start_date',
        'Lease_end_date',
        'Monthly_rent',
        'Security_deposit',
        'Payment_frequency',
        'Grace_period_days',
        'Late_fee_penalty_percent',
        'Lease_agreement',
        'Notes',
        'Water_and_electric',
        'Water',
        'Electric',
        'Parking',
        'Other',
       ];
    }
}
