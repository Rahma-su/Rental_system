<?php

namespace App\Listeners;

use App\Events\BillGeneratedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\BillGenerated;

class SendBillNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(BillGeneratedEvent $event)
    {
        $tenant = $event->tenant;
        $bill = $event->bill;

        $monthsCount = $bill->months_count;
        $billingType = $bill->billing_type ?? 'General'; // fallback if null

        // ✅ Debug line (optional)
        \Log::info('📨 Sending mail with billing type', [
            'tenant' => $tenant->name ?? '',
            'email' => $tenant->email ?? '',
            'billing_type' => $billingType,
        ]);

        if (!empty($tenant->email)) {
            Mail::to($tenant->email)->queue(
                new BillGenerated($tenant, $bill->amount, $monthsCount, $billingType)
            );
        }
    }
}
