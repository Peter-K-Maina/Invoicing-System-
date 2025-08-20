@component('mail::message')
# Hello {{ $invoice->client->name }},

Please find your invoice below:

- Invoice Number: {{ $invoice->id }}
- Amount: KES {{ number_format($invoice->amount, 2) }}
- Status: {{ ucfirst($invoice->status) }}

@component('mail::button', ['url' => route('invoices.show', $invoice->id)])
View Invoice
@endcomponent

Thanks,  
{{ config('app.name') }}
@endcomponent
