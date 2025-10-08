<?php
namespace App\Listeners;

use App\Events\PaymentReceivedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentReceived;

class SendPaymentReceivedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(PaymentReceivedEvent $event)
    {
        $tenant = $event->tenant;
        $bill = $event->bill;
        $amountPaid = $event->amountPaid;

        // ✅ Use the paidMonth from event for the blade
        $monthsPaidText = $event->paidMonth;

        if (!empty($tenant->email)) {
            Mail::to($tenant->email)->send(
                new PaymentReceived($tenant, $bill, $amountPaid, $monthsPaidText)
            );
        }

        // Optional: SMS Notification
        // if (!empty($tenant->phone)) {
        //     SmsService::send($tenant->phone, "Payment of {$amountPaid} received for {$bill->billing_type}");
        // }
    }
}
