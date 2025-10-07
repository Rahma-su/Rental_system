<?php

namespace App\Events;

use App\Models\TenantForm;
use App\Models\TenantBill;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BillGeneratedEvent
{
    use Dispatchable, SerializesModels;

    public $tenant;
    public $bill;

    public function __construct(TenantForm $tenant, TenantBill $bill)
    {
        $this->tenant = $tenant;
        $this->bill = $bill;
    }
}
