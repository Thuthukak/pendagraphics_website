<?php

namespace App\Mail;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Invoice $invoice,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Invoice {$this->invoice->invoice_number} from " . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.invoices.client',
            with: [
                'invoice'  => $this->invoice,
                'client'   => $this->invoice->client,
                'dueDate'  => $this->invoice->due_date->format('d M Y'),
                'total'    => 'R' . number_format($this->invoice->total, 2),
                'balance'  => 'R' . number_format($this->invoice->balance, 2),
            ],
        );
    }

    public function attachments(): array
    {
        // Generate the PDF in-memory — no temp file needed
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