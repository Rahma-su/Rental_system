<?php

namespace App\Exports;

use App\Models\TenantForm;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TenantsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // Select the columns you want to export
        return TenantForm::select(
            'id',
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
            'created_at'
        )->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Full Name',
            'Business Name',
            'Gender',
            'Phone',
            'Email',
            'National ID',
            'TIN No',
            'Nationality',
            'Representative Contact Name',
            'Representative Contact Phone',
            'Address',
            'Notes',
            'Created At',
        ];
    }
}

