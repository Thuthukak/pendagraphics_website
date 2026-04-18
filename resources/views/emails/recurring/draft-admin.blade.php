<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; color: #333; background: #f9f9f7; margin: 0; padding: 32px 16px; }
        .card { background: white; border-radius: 12px; max-width: 560px; margin: 0 auto; padding: 36px 40px; border: 1px solid #e8e8e0; }
        h2 { font-size: 20px; margin: 0 0 6px; color: #1a1a1a; }
        p { font-size: 14px; line-height: 1.6; color: #555; margin: 0 0 16px; }
        .meta { background: #fafaf7; border: 1px solid #f0f0e8; border-radius: 8px; padding: 16px 20px; margin: 20px 0; font-size: 13px; }
        .meta dt { color: #aaa; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px; }
        .meta dd { color: #1a1a1a; font-weight: 600; margin: 0 0 12px; }
        .meta dd:last-child { margin-bottom: 0; }
        .badge { display: inline-block; padding: 3px 10px; background: #fdf6e8; border: 1px solid #f5d98e; border-radius: 20px; font-size: 12px; color: #8a6a1a; font-weight: 500; }
        .footer { margin-top: 24px; font-size: 12px; color: #aaa; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Draft Invoice Created</h2>
        <p>A recurring invoice schedule ran successfully and created a <strong>draft</strong> invoice. Please review and send it when ready.</p>

        <dl class="meta">
            <dt>Schedule</dt>
            <dd>{{ $schedule->name }}</dd>

            <dt>Client</dt>
            <dd>{{ $schedule->client?->name ?? '—' }}</dd>

            <dt>Invoice Number</dt>
            <dd>{{ $invoice->invoice_number }}</dd>

            <dt>Amount</dt>
            <dd>R {{ number_format($invoice->items->sum(fn($i) => $i->quantity * $i->unit_price), 2) }}</dd>

            <dt>Due Date</dt>
            <dd>{{ $invoice->due_date?->format('d M Y') ?? '—' }}</dd>

            <dt>Status</dt>
            <dd><span class="badge">Draft — awaiting your review</span></dd>
        </dl>

        <p>Log in to your dashboard to review and send this invoice to the client.</p>

        <div class="footer">This notification was sent because you have "Notify admin" enabled on the <em>{{ $schedule->name }}</em> schedule.</div>
    </div>
</body>
</html>