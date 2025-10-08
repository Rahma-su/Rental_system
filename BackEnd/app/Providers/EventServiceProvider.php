<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\BillGeneratedEvent;
use App\Listeners\SendBillNotification;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        BillGeneratedEvent::class => [
            SendBillNotification::class,
            PaymentReceivedEvent::class ,
        SendPaymentReceivedNotification::class,
        ],
    ];
    


}
