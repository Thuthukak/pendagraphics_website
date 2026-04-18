<?php

namespace App\Console\Commands;

use App\Jobs\SendInvoiceJob;
use App\Models\Invoice;
use App\Models\RecurringInvoice;
use App\Models\RecurringInvoiceLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceSentAdminMail;

class ProcessRecurringInvoices extends Command
{
    protected $signature   = 'invoices:process-recurring {--dry-run : Preview what would run without creating anything}';
    protected $description = 'Generate invoices for all recurring schedules that are due today.';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $today  = now()->toDateString();

        $due = RecurringInvoice::with(['client', 'items'])
            ->dueToRun()           // scopeDueToRun: active + next_run_date <= today
            ->get();

        if ($due->isEmpty()) {
            $this->info('No recurring invoices due today.');
            return self::SUCCESS;
        }

        $this->info("Found {$due->count()} schedule(s) due. " . ($dryRun ? '[DRY RUN]' : ''));

        foreach ($due as $schedule) {
            $this->line("  → {$schedule->name} (client: {$schedule->client?->name})");

            if ($dryRun) continue;

            try {
                $this->processSchedule($schedule);
            } catch (\Throwable $e) {
                Log::error("ProcessRecurringInvoices: failed for schedule #{$schedule->id} — {$e->getMessage()}");
                $this->error("    ✗ Failed: {$e->getMessage()}");

                // Log failure so the user can see it in the UI logs drawer
                RecurringInvoiceLog::create([
                    'recurring_invoice_id' => $schedule->id,
                    'ran_at'               => now(),
                    'status'               => 'failed',
                    'notes'                => $e->getMessage(),
                ]);
            }
        }

        $this->info('Done.');
        return self::SUCCESS;
    }

    private function processSchedule(RecurringInvoice $schedule): void
    {
        if (! $schedule->shouldRun()) {
            $this->line("    ↷ Skipped (shouldRun() returned false)");
            return;
        }

        $invoice = DB::transaction(function () use ($schedule) {

            // 1. Build and save the invoice from the recurring template
            $invoice = $schedule->buildInvoice();
            $invoice->save();

            // 2. Copy line items across
            foreach ($schedule->items as $item) {
                $invoice->items()->create([
                    'service_id'  => $item->service_id,
                    'description' => $item->description,
                    'quantity'    => $item->quantity,
                    'unit_price'  => $item->unit_price,
                    'sort_order'  => $item->sort_order,
                ]);
            }

            // 3. Advance the schedule
            $schedule->increment('occurrences_count');
            $schedule->update([
                'last_run_date' => now()->toDateString(),
                'next_run_date' => $schedule->calculateNextRunDate()->toDateString(),
                'is_active'     => $schedule->shouldStillBeActiveAfterRun(),
            ]);

            // 4. Record a success log
            RecurringInvoiceLog::create([
                'recurring_invoice_id' => $schedule->id,
                'invoice_id'           => $invoice->id,
                'ran_at'               => now(),
                'status'               => 'success',
                'notes'                => "Invoice #{$invoice->invoice_number} created via {$schedule->action_on_create}.",
            ]);

            return $invoice;
        });

        // 5. Act on the invoice outside the transaction (side-effects)
        $this->dispatchAction($schedule, $invoice);

        $this->line("    ✓ Invoice #{$invoice->invoice_number} created (action: {$schedule->action_on_create})");
    }

    /**
     * Send / keep as draft based on the schedule's action_on_create setting,
     * then optionally notify the admin.
     */
    private function dispatchAction(RecurringInvoice $schedule, Invoice $invoice): void
    {
        $shouldSend = match ($schedule->action_on_create) {
            // Always queue for sending
            'send'          => true,
            // Only send if the client actually has an email address
            'send_if_valid' => filled($schedule->client?->email),
            // Leave as draft — no email to client
            'draft'         => false,
            default         => false,
        };

        if ($shouldSend) {
            // Reuse existing SendInvoiceJob — it handles the client email,
            // marks the invoice as sent, and sends the admin confirmation mail.
            SendInvoiceJob::dispatch($invoice);
        } elseif ($schedule->notify_admin) {
            // Draft mode: client gets nothing, but admin still wants to know
            // an invoice was generated so they can review and send manually.
            $this->notifyAdminDraftCreated($schedule, $invoice);
        }

        // When send_if_valid fires but the client has no email, we fall through
        // to draft behaviour. Notify the admin so nothing silently disappears.
        if ($schedule->action_on_create === 'send_if_valid'
            && ! filled($schedule->client?->email)
            && $schedule->notify_admin
        ) {
            $this->notifyAdminNoEmail($schedule, $invoice);
        }
    }

    private function notifyAdminDraftCreated(RecurringInvoice $schedule, Invoice $invoice): void
    {
        $adminEmail = config('mail.from.admin_address', config('mail.from.address'));
        Mail::to($adminEmail)->send(new \App\Mail\RecurringInvoiceDraftAdminMail($schedule, $invoice));
    }

    private function notifyAdminNoEmail(RecurringInvoice $schedule, Invoice $invoice): void
    {
        $adminEmail = config('mail.from.admin_address', config('mail.from.address'));
        Mail::to($adminEmail)->send(new \App\Mail\RecurringInvoiceNoEmailAdminMail($schedule, $invoice));
    }
}