<?php
// app/Jobs/ProcessRecurringInvoice.php

namespace App\Jobs;

use App\Models\Invoice;
use App\Models\RecurringInvoice;
use App\Models\RecurringInvoiceItem;
use App\Models\RecurringInvoiceLog;
use App\Notifications\RecurringInvoiceCreated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class ProcessRecurringInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60; // seconds between retries

    public function __construct(public RecurringInvoice $recurring) {}

    public function handle(): void
    {
        if (!$this->recurring->shouldRun()) {
            Log::info("RecurringInvoice #{$this->recurring->id} skipped — conditions not met.");
            return;
        }

        DB::transaction(function () {
            $invoice = $this->createInvoice();
            $this->advanceSchedule();
            $this->writeLog($invoice, 'created');
            $this->maybeAutoSend($invoice);
            $this->maybeNotifyAdmin($invoice);
        });
    }

    private function createInvoice(): Invoice
    {
        $invoice = $this->recurring->buildInvoice();
        $invoice->save(); // triggers invoice_number generation via boot()

        foreach ($this->recurring->items as $template) {
            $invoice->items()->create([
                'service_id'  => $template->service_id,
                'description' => $template->description,
                'quantity'    => $template->quantity,
                'unit_price'  => $template->unit_price,
                'sort_order'  => $template->sort_order,
            ]);
        }

        $invoice->load('items');
        $invoice->calculateTotals();
        $invoice->save();

        return $invoice;
    }

    private function advanceSchedule(): void
    {
        $next = $this->recurring->calculateNextRunDate();

        $this->recurring->update([
            'last_run_date'     => $this->recurring->next_run_date,
            'next_run_date'     => $next,
            'occurrences_count' => $this->recurring->occurrences_count + 1,
            // Deactivate if we've now exceeded limits
            'is_active'         => $this->recurring->shouldStillBeActiveAfterRun(),
        ]);
    }

    private function writeLog(Invoice $invoice, string $status): void
    {
        RecurringInvoiceLog::create([
            'recurring_invoice_id' => $this->recurring->id,
            'invoice_id'           => $invoice->id,
            'status'               => $status,
            'message'              => "Invoice {$invoice->invoice_number} created.",
            'ran_at'               => now(),
        ]);
    }

    private function maybeAutoSend(Invoice $invoice): void
    {
        $action = $this->recurring->action_on_create;
        $clientEmail = $this->recurring->client->email ?? null;

        $shouldSend = $action === 'send'
            || ($action === 'send_if_valid' && $clientEmail);

        if ($shouldSend && $clientEmail) {
            // Dispatch SendInvoiceEmail job (implement per your mail setup)
            // SendInvoiceEmail::dispatch($invoice);
            $invoice->markAsSent();
            Log::info("Auto-sent invoice {$invoice->invoice_number} to {$clientEmail}");
        }
    }

    private function maybeNotifyAdmin(Invoice $invoice): void
    {
        if (!$this->recurring->notify_admin) return;

        // Notify admin user(s) — adapt to your User/notification setup
        // $admin = \App\Models\User::role('admin')->first();
        // if ($admin) $admin->notify(new RecurringInvoiceCreated($invoice, $this->recurring));

        Log::info("Admin notified: recurring invoice generated {$invoice->invoice_number}");
    }

    public function failed(\Throwable $e): void
    {
        RecurringInvoiceLog::create([
            'recurring_invoice_id' => $this->recurring->id,
            'invoice_id'           => null,
            'status'               => 'failed',
            'message'              => $e->getMessage(),
            'ran_at'               => now(),
        ]);

        Log::error("RecurringInvoice #{$this->recurring->id} failed: " . $e->getMessage());
    }
}