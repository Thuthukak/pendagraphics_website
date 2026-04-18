<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceSentAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Invoice $invoice,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[Sent] Invoice {$this->invoice->invoice_number} — {$this->invoice->client->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.invoices.admin-confirmation',
            with: [
                'invoice' => $this->invoice,
                'client'  => $this->invoice->client,
                'sentAt'  => now()->format('d M Y H:i'),
                'total'   => 'R' . number_format($this->invoice->total, 2),
            ],
        );
    }
}