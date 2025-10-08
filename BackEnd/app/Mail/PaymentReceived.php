<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\TenantBill;
use Carbon\Carbon;

class PaymentReceived extends Mailable
{
    use Queueable, SerializesModels;

    public $tenant;
    public $bill;
    public $amountPaid;
    public $monthsPaidText; // will hold the text for the paid months

    public function __construct($tenant, TenantBill $bill, $amountPaid)
    {
        $this->tenant = $tenant;
        $this->bill = $bill;
        $this->amountPaid = $amountPaid;

        // Calculate months paid this transaction
        $perMonthAmount = $bill->amount / $bill->months_count;

        // How many months were just paid in this payment
        $monthsPaidNow = floor($amountPaid / $perMonthAmount);

        // Determine the starting month of this payment
        $firstUnpaidMonthIndex = $bill->paid_months - $monthsPaidNow;
        if ($firstUnpaidMonthIndex < 0) $firstUnpaidMonthIndex = 0;

        $startMonth = Carbon::parse($bill->billing_period_start)
            ->addMonths($firstUnpaidMonthIndex)
            ->format('F');

        $endMonth = Carbon::parse($bill->billing_period_start)
            ->addMonths($bill->paid_months - 1)
            ->format('F Y');

        if ($monthsPaidNow > 1) {
            $this->monthsPaidText = "{$startMonth} - {$endMonth}";
        } else {
            $this->monthsPaidText = $endMonth;
        }
    }

    public function build()
    {
        return $this->subject('Payment Received')
            ->markdown('emails.payment-received')
            ->with([
                'tenant' => $this->tenant,
                'bill' => $this->bill,
                'amountPaid' => $this->amountPaid,
                'monthsPaidText' => $this->monthsPaidText,
            ]);
    }
}
