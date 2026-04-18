<x-mail::message>
# Invoice Sent ✓

Invoice **{{ $invoice->invoice_number }}** was dispatched to **{{ $client->email }}** at {{ $sentAt }}.

<x-mail::table>
| | |
|:--|--:|
| **Client** | {{ $client->name }} |
| **Invoice #** | {{ $invoice->invoice_number }} |
| **Total** | {{ $total }} |
| **Due Date** | {{ $invoice->due_date->format('d M Y') }} |
</x-mail::table>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>