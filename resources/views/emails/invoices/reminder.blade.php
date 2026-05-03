<x-mail::message>
Hi {{ $client->name }},

@if ($isOverdue)
This is a notice that **Invoice {{ $invoice->invoice_number }}** is now **overdue**.
We'd appreciate your prompt attention to settle the outstanding balance.
@else
This is a friendly reminder that **Invoice {{ $invoice->invoice_number }}** is due on **{{ $dueDate }}**.
@endif

**Amount Due:** {{ $balance }}

Please find the invoice attached for your reference. If you have any questions or have already made payment, please disregard this reminder.

Thanks,
{{ config('app.name') }}
</x-mail::message>