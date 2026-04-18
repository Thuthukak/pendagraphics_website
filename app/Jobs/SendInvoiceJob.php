<?php

namespace App\Jobs;

use App\Mail\InvoiceMail;
use App\Mail\InvoiceSentAdminMail;
use App\Mail\InvoiceFailedAdminMail;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Retry up to 5 times before failing.
     */
    public int $tries = 5;

    /**
     * Wait 180s (3 mins) between retries (exponential back-off via retryAfter).
     */
    public int $backoff = 180;

    public function __construct(
        public readonly Invoice $invoice,
    ) {}

    public function handle(): void
    {
        // Eager-load everything the mailables and PDF view need
        $this->invoice->load(['client', 'items.service']);

        // 1. Send invoice PDF to client
        Mail::to($this->invoice->client->email)
            ->send(new InvoiceMail($this->invoice));

        // 2. Send confirmation to admin
        $adminEmail = config('mail.from.admin_address', config('mail.from.address'));

        Mail::to($adminEmail)
            ->send(new InvoiceSentAdminMail($this->invoice));

        // 3. Mark invoice as sent (only after both mails succeed)
        $this->invoice->markAsSent();

        Log::info("Invoice {$this->invoice->invoice_number} sent to {$this->invoice->client->email}");
    }

    /**
     * Handle a job failure — log it; you could also fire an event or
     * notify the admin here instead of silently swallowing the error.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Failed to send invoice {$this->invoice->invoice_number}: {$exception->getMessage()}");
        $adminEmail = config('mail.from.admin_address', config('mail.from.address'));
        Mail::to($adminEmail)->send(new InvoiceFailedAdminMail($this->invoice, $exception->getMessage()));
    }
}