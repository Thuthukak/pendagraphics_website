<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\RecurringInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecurringInvoiceDraftAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly RecurringInvoice $schedule,
        public readonly Invoice          $invoice,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Draft Invoice Created — {$this->invoice->invoice_number} ({$this->schedule->client?->name})",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.recurring.draft-admin');
    }
}