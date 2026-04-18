{{-- resources/views/emails/invoices/client.blade.php --}}
<x-mail::message>
# Invoice {{ $invoice->invoice_number }}

Hi {{ $client->name }},

Please find your invoice attached to this email. Here's a quick summary:

<x-mail::table>
| | |
|:--|--:|
| **Invoice #** | {{ $invoice->invoice_number }} |
| **Invoice Date** | {{ $invoice->invoice_date->format('d M Y') }} |
| **Due Date** | {{ $dueDate }} |
| **Total** | {{ $total }} |
| **Amount Due** | {{ $balance }} |
</x-mail::table>

The full invoice is attached as a PDF for your records.

@if($invoice->notes)
**Notes:** {{ $invoice->notes }}
@endif

@if($invoice->terms)
**Terms:** {{ $invoice->terms }}
@endif

If you have any questions about this invoice, please don't hesitate to get in touch.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>