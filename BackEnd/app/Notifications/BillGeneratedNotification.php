<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\TenantBill;

class BillGeneratedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $bill;

    /**
     * Create a new notification instance.
     */
    public function __construct(TenantBill $bill)
    {
        $this->bill = $bill;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        // You can send it via mail, database, or both
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Bill Generated')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new bill has been generated for your lease.')
            ->line('Amount: $' . number_format($this->bill->amount, 2))
            ->line('Billing Period: ' . $this->bill->billing_period_start . ' to ' . $this->bill->billing_period_end)
            ->action('View Bill', url('/bills/' . $this->bill->id))
            ->line('Thank you for staying with us!');
    }

    /**
     * Get the array representation for database notifications.
     */
    public function toArray($notifiable)
    {
        return [
            'bill_id' => $this->bill->id,
            'amount' => $this->bill->amount,
            'billing_period' => $this->bill->billing_period_start . ' to ' . $this->bill->billing_period_end,
            'message' => 'A new bill has been generated for you.'
        ];
    }
}
