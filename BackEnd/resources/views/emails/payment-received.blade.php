@component('mail::message')
# Payment Received

Dear {{ $tenant->full_name ?? 'Tenant' }},

We have received your payment of **{{ number_format($amountPaid, 2) }}** for the bill:
- **Type:** {{ ucfirst($bill->billing_type) }}
- **Month(s) Paid:** {{ $monthsPaidText }}
- **Period:** {{ $bill->billing_period_start->format('M d, Y') }} - {{ $bill->billing_period_end->format('M d, Y') }}

Thank you for your prompt payment!

Thanks,<br>
{{ config('app.name') }}
@endcomponent
