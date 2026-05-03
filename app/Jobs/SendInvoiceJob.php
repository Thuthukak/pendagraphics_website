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
use App\Mail\InvoiceReminderMail;

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
        public readonly string $type = 'send', // 'send' | 'reminder'
    ) {}

    public function handle(): void
    {
        $this->invoice->load(['client', 'items.service']);

        if ($this->type === 'reminder') {
            $this->sendReminder();
        } else {
            $this->sendInitial();
        }
    }

    private function sendInitial(): void
    {
        Mail::to($this->invoice->client->email)
            ->send(new InvoiceMail($this->invoice));

        $adminEmail = config('mail.from.admin_address', config('mail.from.address'));
        Mail::to($adminEmail)->send(new InvoiceSentAdminMail($this->invoice));

        $this->invoice->markAsSent();
        Log::info("Invoice {$this->invoice->invoice_number} sent to {$this->invoice->client->email}");
    }

    private function sendReminder(): void
    {
        Mail::to($this->invoice->client->email)
            ->send(new InvoiceReminderMail($this->invoice));

        Log::info("Reminder sent for invoice {$this->invoice->invoice_number} to {$this->invoice->client->email}");
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