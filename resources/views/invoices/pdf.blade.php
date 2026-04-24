{{-- resources/views/invoices/pdf.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>

  @font-face {
    font-family: 'Roboto';
    font-style: normal;
    font-weight: 400;
    src: url("{{ storage_path('fonts/Roboto-Regular.ttf') }}") format('truetype');
  }
  @font-face {
    font-family: 'Roboto';
    font-style: normal;
    font-weight: 500;
    src: url("{{ storage_path('fonts/Roboto-Medium.ttf') }}") format('truetype');
  }
  @font-face {
    font-family: 'Roboto';
    font-style: normal;
    font-weight: 700;
    src: url("{{ storage_path('fonts/Roboto-Bold.ttf') }}") format('truetype');
  }

  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    font-family: 'Roboto', sans-serif;
    font-weight: 400;
    font-size: 12px;
    color: #1a1a1a;
    background: #fff;
  }

  .page { padding: 40px 48px; }

  /* ── Header ── */
  .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
  .header-table td { vertical-align: top; padding: 0; border: none; }

  .logo-img         { max-height: 60px; max-width: 160px; }
  .logo-placeholder { font-size: 20px; font-weight: 700; color: #1a1a1a; }

  .invoice-heading {
    font-size: 34px;
    font-weight: 700;
    color: #1a1a1a;
    letter-spacing: 1px;
    line-height: 1;
    margin-bottom: 6px;
  }
  .company-name-text {
    font-weight: 700;
    font-size: 12px;
    line-height: 1.4;
  }
  .company-detail {
    font-size: 11px;
    color: #333;
    line-height: 1.5;  /* was 1.7 — tightened */
  }

  /* ── Divider ── */
  .divider { border: none; border-top: 1px solid #ccc; margin-bottom: 20px; }

  /* ── Bill To + Meta ── */
  .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
  .meta-table td { vertical-align: top; padding: 0; border: none; }

  .bill-label {
    font-size: 10px;
    color: #888;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 3px;  /* was 4px */
    font-weight: 500;
  }
  .bill-name   { font-weight: 700; font-size: 13px; line-height: 1.3; }
  .bill-detail {
    font-size: 11px;
    color: #333;
    line-height: 1.5;  /* was 1.7 — tightened */
    margin-top: 2px;
  }

  .meta-right  { text-align: right; }
  .meta-row    { font-size: 12px; line-height: 1.6; }  /* was separate margin-bottom */
  .meta-label  { color: #555; }

  .amount-due-box {
    display: inline-table;
    background: #f0f0f0;
    border-radius: 4px;
    margin-top: 6px;
  }
  .amount-due-inner  { display: table-cell; padding: 5px 14px; }
  .amount-due-label  { font-size: 11px; font-weight: 700; line-height: 1.3; }
  .amount-due-value  { font-size: 14px; font-weight: 700; line-height: 1.3; }

  /* ── Items table ── */
  .items-table { width: 100%; border-collapse: collapse; }

  .items-table thead tr { background: #04657d; }
  .items-table thead th {
    padding: 9px 14px;
    font-size: 11px;
    font-weight: 700;
    color: #fff;
    text-align: left;
    letter-spacing: 0.2px;
  }
  .items-table thead th.text-right { text-align: right; }

  .items-table tbody td {
    padding: 10px 14px;
    font-size: 12px;
    color: #1a1a1a;
    border-bottom: 1px solid #eee;
    vertical-align: top;
  }
  .items-table tbody td.text-right { text-align: right; }
  .item-title { font-weight: 700; font-size: 12px; line-height: 1.3; }
  .item-sub   { font-size: 11px; color: #555; margin-top: 1px; line-height: 1.3; }

  /* ── Totals ── */
  .totals-outer { width: 100%; border-collapse: collapse; margin-top: 2px; }
  .totals-outer td { border: none; padding: 0; }
  .totals-inner { width: 260px; float: right; }

  .t-row {
    width: 100%;
    border-collapse: collapse;
    border-bottom: 1px solid #eee;
  }
  .t-row td { padding: 6px 0; font-size: 12px; }  /* was 7px — slightly tighter */
  .t-label { color: #555; text-align: right; padding-right: 18px; }
  .t-value { text-align: right; white-space: nowrap; }

  .t-row.discount .t-label,
  .t-row.discount .t-value { color: #2a7a6e; }

  .t-row.grand-total { border-top: 1px solid #bbb; border-bottom: 1px solid #bbb; }
  .t-row.grand-total td { font-weight: 700; font-size: 13px; padding: 8px 0; }

  .t-row.amount-due { border-bottom: none; }
  .t-row.amount-due td { font-weight: 700; font-size: 13px; padding-top: 8px; }

  /* ── Notes ── */
  .notes-section { margin-top: 28px; clear: both; }
  .notes-label {
    font-weight: 700;
    font-size: 12px;
    margin-bottom: 4px;  /* was 6px */
  }
  .notes-text {
    font-size: 11px;
    color: #333;
    line-height: 1.5;  /* was 1.8 — tightened */
  }

  /* ── Footer ── */
  .footer {
    margin-top: 52px;
    text-align: center;
    font-size: 11px;
    color: #aaa;
  }

  .clearfix::after { content: ''; display: table; clear: both; }

</style>
</head>
<body>
<div class="page">

  <!-- Header -->
  <table class="header-table">
    <tr>
      <td style="width: 45%;">
        @php
            $logoFile = public_path('assets/images/penda_logo2.png');
            $logoSrc  = null;
            if (file_exists($logoFile)) {
                $type    = pathinfo($logoFile, PATHINFO_EXTENSION);
                $data    = base64_encode(file_get_contents($logoFile));
                $logoSrc = 'data:image/' . $type . ';base64,' . $data;
            }
        @endphp

        @if($logoSrc)
            <img src="{{ $logoSrc }}" class="logo-img" alt="Logo">
        @else
            <div class="logo-placeholder">{{ config('invoice.company_name', config('app.name')) }}</div>
        @endif
      </td>
      <td style="text-align: right;">
        <div class="invoice-heading">INVOICE</div>
        <div class="company-name-text">{{ config('invoice.company_name', config('app.name')) }}</div>
        <div class="company-detail">
          @if(config('invoice.address_line1')) {{ config('invoice.address_line1') }}<br> @endif
          @if(config('invoice.address_line2')) {{ config('invoice.address_line2') }}<br> @endif
          @if(config('invoice.city'))          {{ config('invoice.city') }}<br> @endif
          @if(config('invoice.country'))       {{ config('invoice.country') }}<br> @endif
          @if(config('invoice.phone'))         <br>Mobile: {{ config('invoice.phone') }}<br> @endif
          @if(config('invoice.website'))       {{ config('invoice.website') }} @endif
        </div>
      </td>
    </tr>
  </table>

  <hr class="divider">

  <!-- Bill To + Invoice Meta -->
  <table class="meta-table">
    <tr>
      <td style="width: 50%;">
        <div class="bill-label">Bill To</div>
        <div class="bill-name">{{ $invoice->client->name }}</div>
        <div class="bill-detail">
          @if($invoice->client->company) {{ $invoice->client->company }}<br> @endif
          @if($invoice->client->phone)   {{ $invoice->client->phone }}<br> @endif
          @if($invoice->client->email)   {{ $invoice->client->email }}<br> @endif
          @if($invoice->client->address) {!! nl2br(e($invoice->client->address)) !!} @endif
        </div>
      </td>
      <td style="text-align: right; vertical-align: top;">
        <div class="meta-right">
          <div class="meta-row"><span class="meta-label">Invoice Number: </span>{{ $invoice->invoice_number }}</div>
          <div class="meta-row"><span class="meta-label">Invoice Date: </span>{{ $invoice->invoice_date->format('F j, Y') }}</div>
          <div class="meta-row"><span class="meta-label">Payment Due: </span>{{ $invoice->due_date->format('F j, Y') }}</div>
        </div>
        <div style="text-align: right; margin-top: 8px;">
          <div class="amount-due-box">
            <div class="amount-due-inner">
              <div class="amount-due-label">Amount Due (ZAR):</div>
              <div class="amount-due-value">R {{ number_format($invoice->balance, 2) }}</div>
            </div>
          </div>
        </div>
      </td>
    </tr>
  </table>

  <!-- Line Items -->
  <table class="items-table">
    <thead>
      <tr>
        <th style="width: 46%;">Products</th>
        <th class="text-right" style="width: 14%;">Quantity</th>
        <th class="text-right" style="width: 20%;">Price</th>
        <th class="text-right" style="width: 20%;">Amount</th>
      </tr>
    </thead>
    <tbody>
      @foreach($invoice->items as $item)
      <tr>
        <td>
          <div class="item-title">{{ $item->service?->name ?? $item->description }}</div>
          @if($item->service && $item->description !== $item->service->name)
            <div class="item-sub">{{ $item->description }}</div>
          @endif
        </td>
        <td class="text-right">{{ number_format($item->quantity, 0) }}</td>
        <td class="text-right">R{{ number_format($item->unit_price, 2) }}</td>
        <td class="text-right">R{{ number_format($item->total, 2) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <!-- Totals -->
  <div class="clearfix">
    <div class="totals-inner">

      <table class="t-row"><tr>
        <td class="t-label">Subtotal:</td>
        <td class="t-value">R{{ number_format($invoice->subtotal, 2) }}</td>
      </tr></table>

      @if($invoice->discount_amount > 0)
      <table class="t-row discount"><tr>
        <td class="t-label">Courtesy discount:</td>
        <td class="t-value">(R{{ number_format($invoice->discount_amount, 2) }})</td>
      </tr></table>
      @endif

      @if($invoice->tax_amount > 0)
      <table class="t-row"><tr>
        <td class="t-label">VAT ({{ $invoice->tax_rate }}%):</td>
        <td class="t-value">R{{ number_format($invoice->tax_amount, 2) }}</td>
      </tr></table>
      @endif

      <table class="t-row grand-total"><tr>
        <td class="t-label">Total:</td>
        <td class="t-value">R{{ number_format($invoice->total, 2) }}</td>
      </tr></table>

      @if($invoice->paid_amount > 0)
      <table class="t-row"><tr>
        <td class="t-label">Amount Paid:</td>
        <td class="t-value">R{{ number_format($invoice->paid_amount, 2) }}</td>
      </tr></table>
      @endif

      <table class="t-row amount-due"><tr>
        <td class="t-label">Amount Due (ZAR):</td>
        <td class="t-value">R{{ number_format($invoice->balance, 2) }}</td>
      </tr></table>

    </div>
  </div>

  <!-- Notes / Terms -->
  @if($invoice->notes || $invoice->terms)
  <div class="notes-section">
    <div class="notes-label">Notes / Terms</div>
    <div class="notes-text">
      @if($invoice->notes) {!! nl2br(e($invoice->notes)) !!} @endif
      @if($invoice->notes && $invoice->terms) <br> @endif
      @if($invoice->terms) {!! nl2br(e($invoice->terms)) !!} @endif
    </div>
  </div>
  @endif

  <!-- Footer -->
  <div class="footer">
    Thank you for choosing {{ config('invoice.company_name', config('app.name')) }}
  </div>

</div>
</body>
</html>