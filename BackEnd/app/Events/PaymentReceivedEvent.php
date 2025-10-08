<?php

// namespace App\Events;

// use App\Models\TenantBill;
// use App\Models\TenantForm;
// use Illuminate\Foundation\Events\Dispatchable;
// use Illuminate\Queue\SerializesModels;
// use Carbon\Carbon;
// class PaymentReceivedEvent
// {
//     use Dispatchable, SerializesModels;

//     public $tenant;
//     public $bill;
//     public $amountPaid;
//     public $paidMonth;

//     public function __construct(TenantForm $tenant, TenantBill $bill, $amountPaid)
//     {
//         $this->tenant = $tenant;
//         $this->bill = $bill;
//         $this->amountPaid = $amountPaid;
//         $this->paidMonth = Carbon::parse($bill->billing_period_start)
//     ->addMonths($bill->paid_months - 1)
//     ->format('F Y');
//     }
    
// }
namespace App\Events;

use App\Models\TenantBill;
use App\Models\TenantForm;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class PaymentReceivedEvent
{
    use Dispatchable, SerializesModels;

    public $tenant;
    public $bill;
    public $amountPaid;
    public $paidMonth; // This is fine, but we'll improve calculation

    public function __construct(TenantForm $tenant, TenantBill $bill, $amountPaid)
    {
        $this->tenant = $tenant;
        $this->bill = $bill;
        $this->amountPaid = $amountPaid;

        // ✅ Safe calculation for paid month(s)
        $monthsPaidNow = floor($amountPaid / ($bill->amount / $bill->months_count));
        $firstUnpaidIndex = max(0, $bill->paid_months - $monthsPaidNow);

        $startMonth = Carbon::parse($bill->billing_period_start)
            ->addMonths($firstUnpaidIndex);

        $endMonth = Carbon::parse($bill->billing_period_start)
            ->addMonths($bill->paid_months - 1);

        if ($monthsPaidNow > 1) {
            $this->paidMonth = $startMonth->format('F Y') . ' - ' . $endMonth->format('F Y');
        } else {
            $this->paidMonth = $endMonth->format('F Y');
        }
    }
}
