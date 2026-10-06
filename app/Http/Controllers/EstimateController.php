<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEstimateRequest;
use App\Http\Requests\UpdateEstimateRequest;
use App\Http\Resources\EstimateResource;
use App\Jobs\SendInvoiceJob;
use App\Mail\EstimateRequest as EstimateRequestMail;
use App\Models\Client;
use App\Models\Estimate;
use App\Models\EstimateService;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Exception;

class EstimateController extends Controller
{
    /**
     * List quotations with filtering, sorting and pagination (mirrors InvoiceController::index).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Estimate::with(['services.service', 'invoice']);

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('date_from') || $request->filled('date_to')) {
            $query->byDateRange($request->date_from, $request->date_to);
        }

        if ($request->boolean('expired_only')) {
            $query->expired();
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSorts = ['quote_number', 'name', 'email', 'total_amount', 'status', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $perPage = $request->get('per_page', 15);
        $estimates = $query->paginate($perPage);

        return EstimateResource::collection($estimates);
    }

    public function show(Estimate $estimate): JsonResponse
    {
        $estimate->load(['services.service', 'invoice']);

        return response()->json([
            'estimate' => new EstimateResource($estimate),
        ]);
    }

    public function store(StoreEstimateRequest $request): JsonResponse
    {
        try {
            $estimate = Estimate::create([
                'name' => $request->name,
                'email' => $request->email,
                'total_amount' => $request->totalEstimate,
                'expiry_date' => $request->expiry_date,
                'notes' => $request->notes ?? $request->additionalDetails,
                'terms' => $request->terms,
                'status' => Estimate::STATUS_PENDING,
            ]);

            foreach ($request->selectedServices as $service) {
                EstimateService::create([
                    'estimate_id' => $estimate->id,
                    'service_id' => $service['id'],
                    'price' => $service['price'],
                ]);
            }

            $emailSent = false;

            try {
                Mail::to($request->email)
                    ->cc(config('mail.admin_address'))
                    ->bcc(config('mail.from.address'))
                    ->send(new EstimateRequestMail($estimate));

                $emailSent = true;
                $estimate->update(['status' => Estimate::STATUS_EMAILED]);
            } catch (Exception $mailException) {
                Log::error('Failed to send estimate email', [
                    'estimate_id' => $estimate->id,
                    'email' => $request->email,
                    'error' => $mailException->getMessage(),
                ]);
                $estimate->update(['status' => Estimate::STATUS_EMAIL_FAILED]);
            }

            $response = [
                'message' => 'Estimate request received successfully',
                'estimate' => new EstimateResource($estimate->load(['services.service'])),
                'email_sent' => $emailSent,
            ];

            if (!$emailSent) {
                $response['email_warning'] = 'Your request was saved, but we encountered an issue sending the confirmation email. We will contact you directly within 24 hours.';
            }

            return response()->json($response, 201);
        } catch (Exception $e) {
            Log::error('Failed to create estimate', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'message' => 'An error occurred while processing your request. Please try again or contact us directly.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    public function update(UpdateEstimateRequest $request, Estimate $estimate): JsonResponse
    {
        if (!$estimate->is_editable) {
            return response()->json([
                'message' => 'This quotation can no longer be edited (' . $estimate->status . ').',
            ], 422);
        }

        try {
            $estimate->update($request->only(['name', 'email', 'expiry_date', 'notes', 'terms']));

            if ($request->has('selectedServices')) {
                $estimate->services()->delete();

                foreach ($request->selectedServices as $service) {
                    EstimateService::create([
                        'estimate_id' => $estimate->id,
                        'service_id' => $service['id'],
                        'price' => $service['price'],
                    ]);
                }
            }

            if ($request->filled('totalEstimate')) {
                $estimate->update(['total_amount' => $request->totalEstimate]);
            }

            return response()->json([
                'message' => 'Quotation updated successfully',
                'estimate' => new EstimateResource($estimate->load(['services.service'])),
            ]);
        } catch (Exception $e) {
            Log::error('Failed to update estimate', ['estimate_id' => $estimate->id, 'error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Failed to update quotation',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Update single estimate status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,emailed,email_failed,completed,cancelled',
        ]);

        try {
            $estimate = Estimate::findOrFail($id);

            if ($estimate->status === Estimate::STATUS_CONVERTED) {
                return response()->json([
                    'message' => 'This quotation has already been converted to an invoice and cannot change status.',
                ], 422);
            }

            $estimate->update([
                'status' => $request->status,
                'updated_at' => now(),
            ]);

            return response()->json([
                'message' => 'Status updated successfully',
                'estimate' => new EstimateResource($estimate),
            ]);
        } catch (Exception $e) {
            Log::error('Failed to update estimate status', [
                'estimate_id' => $id,
                'status' => $request->status,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to update status',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Bulk update estimate statuses
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:estimates,id',
            'status' => 'required|in:pending,emailed,email_failed,completed,cancelled',
        ]);

        try {
            $updated = Estimate::whereIn('id', $request->ids)
                ->where('status', '!=', Estimate::STATUS_CONVERTED)
                ->update([
                    'status' => $request->status,
                    'updated_at' => now(),
                ]);

            return response()->json([
                'message' => "{$updated} estimates updated successfully",
                'updated_count' => $updated,
            ]);
        } catch (Exception $e) {
            Log::error('Failed to bulk update estimate statuses', [
                'ids' => $request->ids,
                'status' => $request->status,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to bulk update statuses',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Resend email for a specific estimate
     */
    public function resendEmail(Request $request, $id)
    {
        try {
            $estimate = Estimate::with(['services.service'])->findOrFail($id);

            Mail::to($estimate->email)
                ->cc(config('mail.admin_address'))
                ->bcc(config('mail.from.address'))
                ->send(new EstimateRequestMail($estimate));

            $estimate->update([
                'status' => Estimate::STATUS_EMAILED,
                'updated_at' => now(),
            ]);

            return response()->json([
                'message' => 'Email sent successfully',
                'estimate' => new EstimateResource($estimate),
            ]);
        } catch (Exception $e) {
            Log::error('Failed to resend estimate email', ['estimate_id' => $id, 'error' => $e->getMessage()]);

            $estimate = Estimate::find($id);
            if ($estimate) {
                $estimate->update(['status' => Estimate::STATUS_EMAIL_FAILED, 'updated_at' => now()]);
            }

            return response()->json([
                'message' => 'Failed to send email',
                'error' => config('app.debug') ? $e->getMessage() : 'Email delivery failed',
            ], 500);
        }
    }

    /**
     * Send bulk emails
     */
    public function bulkSendEmails(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:estimates,id',
        ]);

        try {
            $estimates = Estimate::with(['services.service'])->whereIn('id', $request->ids)->get();

            $sent = 0;
            $failed = 0;
            $failedIds = [];

            foreach ($estimates as $estimate) {
                try {
                    Mail::to($estimate->email)
                        ->cc(config('mail.admin_address'))
                        ->bcc(config('mail.from.address'))
                        ->send(new EstimateRequestMail($estimate));

                    $estimate->update(['status' => Estimate::STATUS_EMAILED, 'updated_at' => now()]);
                    $sent++;
                } catch (Exception $e) {
                    Log::error('Failed to send bulk email', [
                        'estimate_id' => $estimate->id,
                        'email' => $estimate->email,
                        'error' => $e->getMessage(),
                    ]);

                    $estimate->update(['status' => Estimate::STATUS_EMAIL_FAILED, 'updated_at' => now()]);
                    $failed++;
                    $failedIds[] = $estimate->id;
                }
            }

            return response()->json([
                'message' => 'Bulk email operation completed',
                'sent' => $sent,
                'failed' => $failed,
                'failed_ids' => $failedIds,
            ]);
        } catch (Exception $e) {
            Log::error('Failed to process bulk emails', ['ids' => $request->ids, 'error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Failed to process bulk emails',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    public function downloadPDF(Request $request, $id)
    {
        try {
            $estimate = Estimate::with(['services.service'])->findOrFail($id);
            $pdf = Pdf::loadView('estimates.pdf', compact('estimate'));

            return $pdf->download("{$estimate->quote_number}.pdf");
        } catch (Exception $e) {
            Log::error('Failed to generate PDF', ['estimate_id' => $id, 'error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Failed to generate PDF',
                'error' => config('app.debug') ? $e->getMessage() : 'PDF generation failed',
            ], 500);
        }
    }

    /**
     * Duplicate an existing quotation (new draft-style pending quote, own number).
     */
    public function duplicate(Estimate $estimate): JsonResponse
    {
        try {
            $new = $estimate->replicate(['quote_number', 'status', 'invoice_id', 'converted_at']);
            $new->status = Estimate::STATUS_PENDING;
            $new->invoice_id = null;
            $new->converted_at = null;
            $new->quote_number = null; // regenerated on create via model boot
            $new->save();

            foreach ($estimate->services as $service) {
                EstimateService::create([
                    'estimate_id' => $new->id,
                    'service_id' => $service->service_id,
                    'price' => $service->price,
                ]);
            }

            return response()->json([
                'message' => 'Quotation duplicated successfully',
                'estimate' => new EstimateResource($new->load(['services.service'])),
            ], 201);
        } catch (Exception $e) {
            Log::error('Failed to duplicate estimate', ['estimate_id' => $estimate->id, 'error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Error duplicating quotation',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Convert a quotation into a real Invoice.
     *
     * - Finds or creates a Client from the quote's name/email.
     * - Copies each selected service into an InvoiceItem.
     * - Locks the quotation with status "converted" and links invoice_id.
     */
    public function convertToInvoice(Request $request, Estimate $estimate): JsonResponse
    {
        if ($estimate->status === Estimate::STATUS_CONVERTED) {
            return response()->json([
                'message' => 'This quotation has already been converted to invoice ' .
                    ($estimate->invoice->invoice_number ?? '#' . $estimate->invoice_id) . '.',
            ], 422);
        }

        if ($estimate->status === Estimate::STATUS_CANCELLED) {
            return response()->json([
                'message' => 'Cancelled quotations cannot be converted to an invoice.',
            ], 422);
        }

        $request->validate([
            'invoice_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
            'action' => 'required|in:draft,send',
        ]);

        $estimate->load('services.service');

        if ($estimate->services->isEmpty()) {
            return response()->json([
                'message' => 'This quotation has no services to convert.',
            ], 422);
        }

        try {
            $invoice = DB::transaction(function () use ($request, $estimate) {
                $client = Client::firstOrCreate(
                    ['email' => $estimate->email],
                    ['name' => $estimate->name]
                );

                $invoiceDate = $request->input('invoice_date', now()->toDateString());
                $dueDate = $request->input('due_date', now()->addDays(30)->toDateString());

                $invoice = Invoice::create([
                    'client_id' => $client->id,
                    'invoice_date' => $invoiceDate,
                    'due_date' => $dueDate,
                    'status' => 'draft',
                    'notes' => $request->input('notes', $estimate->notes),
                    'terms' => $estimate->terms,
                    'tax_rate' => $request->input('tax_rate', 0),
                    'discount_rate' => $request->input('discount_rate', 0),
                ]);

                foreach ($estimate->services as $index => $estimateService) {
                    $invoice->items()->create([
                        'service_id' => $estimateService->service_id,
                        'description' => $estimateService->service->name ?? 'Service',
                        'quantity' => 1,
                        'unit_price' => $estimateService->price,
                        'sort_order' => $index,
                    ]);
                }

                $invoice->load('items');
                $invoice->calculateTotals();
                $invoice->save();

                if ($request->action === 'send') {
                    SendInvoiceJob::dispatch($invoice);
                }

                $estimate->markAsConverted($invoice);

                return $invoice;
            });

            return response()->json([
                'message' => $request->action === 'send'
                    ? 'Quotation converted — the invoice is being sent to the client.'
                    : 'Quotation converted to a draft invoice.',
                'invoice' => new \App\Http\Resources\InvoiceResource($invoice->load(['client', 'items.service'])),
                'estimate' => new EstimateResource($estimate->fresh()->load(['services.service', 'invoice'])),
            ], 201);
        } catch (Exception $e) {
            Log::error('Failed to convert estimate to invoice', [
                'estimate_id' => $estimate->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to convert quotation to invoice',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Quotation statistics for the dashboard stat cards.
     */
    public function statistics(Request $request): JsonResponse
    {
        $query = Estimate::query();

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        $stats = [
            'total_estimates' => (clone $query)->count(),
            'pending' => (clone $query)->where('status', Estimate::STATUS_PENDING)->count(),
            'email_failed' => (clone $query)->where('status', Estimate::STATUS_EMAIL_FAILED)->count(),
            'completed' => (clone $query)->where('status', Estimate::STATUS_COMPLETED)->count(),
            'converted' => (clone $query)->where('status', Estimate::STATUS_CONVERTED)->count(),
            'cancelled' => (clone $query)->where('status', Estimate::STATUS_CANCELLED)->count(),
            'total_value' => (clone $query)->sum('total_amount'),
            'converted_value' => (clone $query)->where('status', Estimate::STATUS_CONVERTED)->sum('total_amount'),
        ];

        return response()->json(['statistics' => $stats]);
    }

    /**
     * Delete an estimate
     */
    public function destroy($id)
    {
        try {
            $estimate = Estimate::findOrFail($id);

            if ($estimate->status === Estimate::STATUS_CONVERTED) {
                return response()->json([
                    'message' => 'This quotation has been converted to an invoice and cannot be deleted.',
                ], 422);
            }

            DB::beginTransaction();

            EstimateService::where('estimate_id', $id)->delete();
            $estimate->delete();

            DB::commit();

            return response()->json(['message' => 'Estimate deleted successfully']);
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Failed to delete estimate', ['estimate_id' => $id, 'error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Failed to delete estimate',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }
}