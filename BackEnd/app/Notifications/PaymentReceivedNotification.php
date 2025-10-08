<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\TenantBill;

class PaymentReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $bill;
    public $transaction;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\TenantBill  $bill
     * @param  \App\Models\WalletTransaction  $transaction
     */


    public function __construct($bill, $transaction, $paidMonth)
{
    $this->bill = $bill;
    $this->transaction = $transaction;
    $this->paidMonth = $paidMonth;
}

public function toMail($notifiable)
{
    return (new MailMessage)
        ->subject('Payment Received')
        ->greeting("Dear {$notifiable->name},")
        ->line("We have received your payment of " . number_format($this->transaction->amount, 2) . " for the bill:")
        ->line("Type: {$this->bill->billing_type}")
        ->line("Month Paid: {$this->paidMonth}")
        ->line("Thank you for your prompt payment!")
        ->salutation("Thanks,\nRental System");
}

//     public function __construct($bill, $transaction)
//     {
//         $this->bill = $bill;
//         $this->transaction = $transaction;
//     }

//     public function via($notifiable)
//     {
//         // mail & database. Add 'nexmo' or 'vonage' if you configure SMS channel.
//         return ['mail', 'database'];
//     }

//     public function toMail($notifiable)
//     {
//         $amount = number_format($this->transaction->amount, 2);
//         // compute months paid by this transaction (safe guard)
//         $perMonth = $this->bill->months_count ? ($this->bill->amount / max(1, $this->bill->months_count)) : 0;
//         $monthsPaid = $perMonth > 0 ? (int) floor($this->transaction->amount / $perMonth) : 0;

//         return (new MailMessage)
//                     ->subject('Payment received — Invoice #' . $this->bill->id)
//                     ->greeting('Hello ' . ($notifiable->full_name ?? ''))
//                     ->line("We have received a payment for your bill.")
//                     ->line("**Bill ID:** {$this->bill->id}")
//                     ->line("**Type:** {$this->bill->billing_type}")
//                     ->line("**Amount paid:** {$amount}")
//                     ->line("**Months paid (this payment):** {$monthsPaid}")
//                     ->line("**Reference:** " . ($this->transaction->reference_number ?? '-'))
//                     ->action('View your bills', url('/')) // replace with real route if you have one
//                     ->salutation('Thanks — Rental System');
//     }

//     public function toArray($notifiable)
//     {
//         return [
//             'bill_id' => $this->bill->id,
//             'amount' => $this->transaction->amount,
//             'billing_type' => $this->bill->billing_type,
//             'reference' => $this->transaction->reference_number,
//             'transaction_id' => $this->transaction->id,
//         ];
//     }
 }
