<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\TenantForm;

class BillGenerated extends Mailable
{
    use Queueable, SerializesModels;

    public $tenant;
    public $amount;
    public $months;
    public $billingType;

    public function __construct(TenantForm $tenant, $amount, $months, $billingType)
    {
        $this->tenant = $tenant;
        $this->amount = $amount;
        $this->months = $months;
        $this->billingType = $billingType;
    }

    public function build()
    {
        return $this->subject("New {$this->billingType} Bill Generated")
                    ->markdown('emails.bill_generated')
                    ->with([
                        'tenantName' => $this->tenant->full_name ?? 'Valued Tenant',
                        'amount' => $this->amount,
                        'months' => $this->months,
                        'billingType' => $this->billingType,
                    ]);
    }
}
