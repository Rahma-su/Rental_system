@component('mail::message')
# Hi {{ $tenantName }},

A New Bill Has Been Generated.
-  <p><strong>Billing Type:</strong> {{ $billingType }}</p>
-  <p><strong>Amount:</strong>  {{ number_format($amount, 2) }} ETB</p>
-  <p><strong>Duration:</strong> {{ $months }} month(s)</p>


Please make sure to settle your {{ strtolower($billingType) }} bill on time.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
