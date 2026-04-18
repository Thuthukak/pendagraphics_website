<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Jobs\SendInvoiceJob;
use Illuminate\Support\Facades\DB;


class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices with filtering and pagination
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Invoice::with(['client', 'items.service']);

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Client filter
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->where('invoice_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('invoice_date', '<=', $request->date_to);
        }

        // Search by invoice number or client name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                    $clientQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Overdue filter
        if ($request->boolean('overdue_only')) {
            $query->overdue();
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSorts = ['invoice_number', 'invoice_date', 'due_date', 'status', 'total', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $invoices = $query->paginate($perPage);

        return InvoiceResource::collection($invoices);
    }

    /**
     * Store a newly created invoice
     */
    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        try {
            $invoice = Invoice::create($request->validated());

            // Add invoice items if provided
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $index => $itemData) {
                    $invoice->items()->create([
                        'service_id' => $itemData['service_id'] ?? null,
                        'description' => $itemData['description'],
                        'quantity' => $itemData['quantity'] ?? 1,
                        'unit_price' => $itemData['unit_price'],
                        'sort_order' => $index,
                    ]);
                }
            }

            // Recalculate totals
            $invoice->calculateTotals();
            $invoice->save();

            return response()->json([
                'message' => 'Invoice created successfully',
                'invoice' => new InvoiceResource($invoice->load(['client', 'items.service']))
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified invoice
     */
    public function show(Invoice $invoice): JsonResponse
    {
        $invoice->load(['client', 'items.service']);
        
        return response()->json([
            'invoice' => new InvoiceResource($invoice)
        ]);
    }

    /**
     * Update the specified invoice
     */
    public function update(UpdateInvoiceRequest $request, Invoice $invoice): JsonResponse
    {
        try {
            // Prevent updating paid invoices
            if ($invoice->status === 'paid' && !$request->boolean('force_update')) {
                return response()->json([
                    'message' => 'Cannot update paid invoice'
                ], 422);
            }

            // Update invoice basic info
            $invoice->update($request->validated());

            // Update items if provided
            if ($request->has('items') && is_array($request->items)) {
                // Delete existing items
                $invoice->items()->delete();
                
                // Add new items
                foreach ($request->items as $index => $itemData) {
                    $invoice->items()->create([
                        'service_id' => $itemData['service_id'] ?? null,
                        'description' => $itemData['description'],
                        'quantity' => $itemData['quantity'] ?? 1,
                        'unit_price' => $itemData['unit_price'],
                        'sort_order' => $index,
                    ]);
                }
            }

            // Recalculate totals
            $invoice->calculateTotals();
            $invoice->save();

            return response()->json([
                'message' => 'Invoice updated successfully',
                'invoice' => new InvoiceResource($invoice->load(['client', 'items.service']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified invoice
     */
    public function destroy(Invoice $invoice): JsonResponse
    {
        try {
            // Prevent deleting paid invoices
            if ($invoice->status === 'paid') {
                return response()->json([
                    'message' => 'Cannot delete paid invoice'
                ], 422);
            }

            $invoice->delete();

            return response()->json([
                'message' => 'Invoice deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send invoice to client via email
     */
    public function send(Invoice $invoice): JsonResponse
    {
        // Guard: nothing to send if already paid
        if ($invoice->status === 'paid') {
            return response()->json([
                'message' => 'Invoice is already paid and cannot be re-sent.',
            ], 422);
        }
    
        // Guard: don't queue twice if a job is already in flight.
        // "sent" invoices CAN be re-sent (e.g. client lost the email),
        // so we only block draft-less situations you specifically want to lock.
        // Remove this block if re-sending is always allowed.
        if ($invoice->status === 'sent') {
            return response()->json(['message' => 'Invoice has already been sent.'], 422);
        }
    
        try {
            SendInvoiceJob::dispatch($invoice);
    
            return response()->json([
                'message' => 'Invoice is being sent. The client will receive it shortly.',
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to queue invoice for sending.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * export invoice as PDF
     */
    public function export(Invoice $invoice)
    {
        $invoice->load(['client', 'items.service']);

        $pdf = Pdf::loadView('invoices.pdf', ['invoice' => $invoice])
            ->setPaper('a4', 'portrait');

        $filename = 'invoice-' . $invoice->invoice_number . '.pdf';

        return $pdf->download($filename);
    }
    
    /**
     * Mark invoice as sent
     */
    public function markAsSent(Invoice $invoice): JsonResponse
    {
        try {
            if ($invoice->status === 'paid') {
                return response()->json([
                    'message' => 'Invoice is already paid'
                ], 422);
            }

            $invoice->markAsSent();

            return response()->json([
                'message' => 'Invoice marked as sent',
                'invoice' => new InvoiceResource($invoice->load(['client', 'items.service']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating invoice status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Record payment for invoice
     */
    public function recordPayment(Request $request, Invoice $invoice): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $invoice->balance,
            'payment_method' => 'nullable|string|max:255',
            'payment_notes' => 'nullable|string',
        ]);

        try {
            $invoice->markAsPaid(
                $request->amount,
                $request->payment_method,
                $request->payment_notes
            );

            return response()->json([
                'message' => 'Payment recorded successfully',
                'invoice' => new InvoiceResource($invoice->load(['client', 'items.service']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error recording payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Convert a once-off invoice into a recurring schedule.
     * The front-end sends a small config payload; we copy the invoice's
     * line items into a new RecurringInvoice and its items.
     */
    public function makeRecurring(Request $request, Invoice $invoice): JsonResponse
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'frequency'         => 'required|in:weekly,monthly,quarterly,annually,custom',
            'interval'          => 'required|integer|min:1',
            'interval_unit'     => 'required_if:frequency,custom|nullable|in:day,week,month,year',
            'start_date'        => 'required|date',
            'end_date'          => 'nullable|date|after:start_date',
            'occurrences_limit' => 'nullable|integer|min:1',
            'action_on_create'  => 'required|in:draft,send,send_if_valid',
            'notify_admin'      => 'nullable|boolean',
        ]);
    
        $invoice->load(['items']);
    
        $recurring = DB::transaction(function () use ($request, $invoice) {
    
            $recurring = \App\Models\RecurringInvoice::create([
                // Schedule identity
                'name'              => $request->name,
                'client_id'         => $invoice->client_id,
    
                // Schedule timing
                'frequency'         => $request->frequency,
                'interval'          => $request->interval,
                'interval_unit'     => $request->interval_unit ?? 'month',
                'start_date'        => $request->start_date,
                'next_run_date'     => $request->start_date,   // first run = start date
                'end_date'          => $request->end_date,
                'occurrences_limit' => $request->occurrences_limit,
    
                // Carry invoice financial settings across
                'days_until_due'    => $invoice->due_date && $invoice->invoice_date
                                        ? (int) \Carbon\Carbon::parse($invoice->invoice_date)
                                                            ->diffInDays($invoice->due_date)
                                        : 30,
                'notes'             => $invoice->notes,
                'terms'             => $invoice->terms,
                'tax_rate'          => $invoice->tax_rate    ?? 0,
                'discount_rate'     => $invoice->discount_rate ?? 0,
    
                // Automation
                'action_on_create'  => $request->action_on_create,
                'notify_admin'      => $request->boolean('notify_admin', true),
                'is_active'         => true,
            ]);
    
            // Copy line items from the source invoice
            foreach ($invoice->items as $index => $item) {
                $recurring->items()->create([
                    'service_id'  => $item->service_id,
                    'description' => $item->description,
                    'quantity'    => $item->quantity,
                    'unit_price'  => $item->unit_price,
                    'sort_order'  => $index,
                ]);
            }
    
            return $recurring->load(['client', 'items']);
        });
    
        return response()->json([
            'message'   => 'Recurring schedule created from invoice.',
            'recurring' => $recurring,
        ], 201);
    }

    /**
     * Duplicate an existing invoice
     */
    public function duplicate(Invoice $invoice): JsonResponse
    {
        try {
            $newInvoice = $invoice->replicate();
            $newInvoice->invoice_number = null; // Will be auto-generated
            $newInvoice->status = 'draft';
            $newInvoice->sent_at = null;
            $newInvoice->paid_date = null;
            $newInvoice->paid_amount = 0;
            $newInvoice->balance = $newInvoice->total;
            $newInvoice->invoice_date = now()->toDateString();
            $newInvoice->due_date = now()->addDays(30)->toDateString();
            $newInvoice->save();

            // Duplicate items
            foreach ($invoice->items as $item) {
                $newInvoice->items()->create([
                    'service_id' => $item->service_id,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'sort_order' => $item->sort_order,
                ]);
            }

            return response()->json([
                'message' => 'Invoice duplicated successfully',
                'invoice' => new InvoiceResource($newInvoice->load(['client', 'items.service']))
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error duplicating invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get invoice statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $query = Invoice::query();

        // Date range filter for statistics
        if ($request->filled('date_from')) {
            $query->where('invoice_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('invoice_date', '<=', $request->date_to);
        }

        $stats = [
            'total_invoices' => (clone $query)->count(),
            'draft_invoices' => (clone $query)->where('status', 'draft')->count(),
            'sent_invoices' => (clone $query)->where('status', 'sent')->count(),
            'paid_invoices' => (clone $query)->where('status', 'paid')->count(),
            'overdue_invoices' => (clone $query)->overdue()->count(),
            'total_revenue' => (clone $query)->where('status', 'paid')->sum('total'),
            'pending_revenue' => (clone $query)->whereIn('status', ['sent', 'overdue'])->sum('balance'),
            'average_invoice_value' => (clone $query)->avg('total'),
        ];

        // Recent invoices
        $recentInvoices = (clone $query)
            ->with(['client'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'statistics' => $stats,
            'recent_invoices' => InvoiceResource::collection($recentInvoices)
        ]);
    }

    /**
     * Get clients for invoice creation
     */
    public function getClients(Request $request): JsonResponse
    {
        $clients = Client::select('id', 'name', 'email')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            })
            ->orderBy('name')
            ->get();

        return response()->json($clients);
    }
}