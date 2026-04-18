<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; color: #333; background: #f9f9f7; margin: 0; padding: 32px 16px; }
        .card { background: white; border-radius: 12px; max-width: 560px; margin: 0 auto; padding: 36px 40px; border: 1px solid #e8e8e0; }
        h2 { font-size: 20px; margin: 0 0 6px; color: #1a1a1a; }
        .warning-banner { background: #fef9ec; border: 1px solid #f5d98e; border-radius: 8px; padding: 12px 16px; margin: 16px 0; font-size: 13px; color: #8a6a1a; }
        p { font-size: 14px; line-height: 1.6; color: #555; margin: 0 0 16px; }
        .meta { background: #fafaf7; border: 1px solid #f0f0e8; border-radius: 8px; padding: 16px 20px; margin: 20px 0; font-size: 13px; }
        .meta dt { color: #aaa; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px; }
        .meta dd { color: #1a1a1a; font-weight: 600; margin: 0 0 12px; }
        .meta dd:last-child { margin-bottom: 0; }
        .footer { margin-top: 24px; font-size: 12px; color: #aaa; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Invoice Not Sent — No Email on File</h2>

        <div class="warning-banner">
            <strong>{{ $schedule->client?->name ?? 'This client' }}</strong> has no email address, so the invoice could not be auto-sent. It has been saved as a <strong>draft</strong>.
        </div>

        <p>Your schedule is set to <em>"Send if email exists"</em>. Because no email address is on file for this client, the invoice was created as a draft instead of being sent automatically.</p>

        <dl class="meta">
            <dt>Schedule</dt>
            <dd>{{ $schedule->name }}</dd>

            <dt>Client</dt>
            <dd>{{ $schedule->client?->name ?? '—' }}</dd>

            <dt>Invoice Number</dt>
            <dd>{{ $invoice->invoice_number }}</dd>

            <dt>Amount</dt>
            <dd>R {{ number_format($invoice->items->sum(fn($i) => $i->quantity * $i->unit_price), 2) }}</dd>
        </dl>

        <p>To fix this: add an email address to the client's profile and the next run will send automatically.</p>

        <div class="footer">This notification was sent because you have "Notify admin" enabled on the <em>{{ $schedule->name }}</em> schedule.</div>
    </div>
</body>
</html>