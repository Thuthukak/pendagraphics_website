<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceFailedAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Invoice $invoice,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[Failed] Invoice {$this->invoice->invoice_number} — {$this->invoice->client->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.invoices.admin-failed',
            with: [
                'invoice' => $this->invoice,
                'client'  => $this->invoice->client,
                'sentAt'  => now()->format('d M Y H:i'),
                'total'   => '$' . number_format($this->invoice->total, 2),
            ],
        );
    }
}