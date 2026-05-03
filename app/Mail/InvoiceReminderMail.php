<?php

namespace App\Mail;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Invoice $invoice,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->invoice->status === 'overdue'
            ? "Overdue: Invoice {$this->invoice->invoice_number} — Action Required"
            : "Friendly Reminder: Invoice {$this->invoice->invoice_number} Due " . $this->invoice->due_date->format('d M Y');

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.invoices.reminder',
            with: [
                'invoice' => $this->invoice,
                'client'  => $this->invoice->client,
                'dueDate' => $this->invoice->due_date->format('d M Y'),
                'total'   => 'R' . number_format($this->invoice->total, 2),
                'balance' => 'R' . number_format($this->invoice->balance, 2),
                'isOverdue' => $this->invoice->status === 'overdue',
            ],
        );
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('invoices.pdf', ['invoice' => $this->invoice])
            ->setPaper('a4', 'portrait');

        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                "invoice-{$this->invoice->invoice_number}.pdf"
            )->withMime('application/pdf'),
        ];
    }
}