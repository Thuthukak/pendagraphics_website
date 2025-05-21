@component('mail::message')
# Thank You for Your Interest in Penda Graphics

Dear {{ $estimate->name }},

We've received your estimate request and are excited to work with you on your project!

## Your Estimate Summary

@component('mail::table')
| Service | Price |
|:--------|------:|
@foreach($services as $estimateService)
| {{ $estimateService->service->name }} | ${{ number_format($estimateService->price, 2) }} |
@endforeach
| **Total Estimate** | **${{ number_format($estimate->total_amount, 2) }}** |
@endcomponent

This is an initial estimate based on the services you selected. Our team will review your requirements and may adjust this estimate based on the specific details of your project.

## What's Next?

One of our project managers will contact you within 1 business day to discuss your project in more detail and provide you with a comprehensive quote.

@component('mail::button', ['url' => config('app.url')])
Visit Our Website
@endcomponent

If you have any questions in the meantime, please don't hesitate to contact us.

Thank you for choosing Penda Graphics for your design needs!

Regards,<br>
{{ config('app.name') }} Team
@endcomponent