<?php

namespace App\Http\Controllers;

use App\Models\DeliveryNote;
use App\Models\Invoice;
use App\Models\Estimate;
use App\Http\Requests\StoreDeliveryNoteRequest;
use App\Http\Requests\UpdateDeliveryNoteRequest;
use App\Http\Resources\DeliveryNoteResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class DeliveryNoteController extends Controller
{
    /**
     * Display a listing of delivery notes with filtering and pagination
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = DeliveryNote::with(['client', 'items.service']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('date_from')) {
            $query->where('delivery_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('delivery_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('delivery_number', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->boolean('pending_only')) {
            $query->pending();
        }

        $sortBy    = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSorts = ['delivery_number', 'delivery_date', 'status', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $perPage = $request->get('per_page', 15);
        $deliveryNotes = $query->paginate($perPage);

        return DeliveryNoteResource::collection($deliveryNotes);
    }

    /**
     * Store a newly created delivery note.
     * Works both standalone and when the front-end has pre-filled the
     * payload from an Invoice/Estimate (invoice_id / estimate_id present).
     */
    public function store(StoreDeliveryNoteRequest $request): JsonResponse
    {
        try {
            $deliveryNote = DB::transaction(function () use ($request) {
                $deliveryNote = DeliveryNote::create($request->safe()->except('items'));

                foreach ($request->items as $index => $itemData) {
                    $deliveryNote->items()->create([
                        'service_id'  => $itemData['service_id'] ?? null,
                        'description' => $itemData['description'],
                        'quantity'    => $itemData['quantity'],
                        'unit'        => $itemData['unit'] ?? null,
                        'unit_price'  => $itemData['unit_price'] ?? null,
                        'sort_order'  => $index,
                    ]);
                }

                return $deliveryNote;
            });

            return response()->json([
                'message'       => 'Delivery note created successfully',
                'delivery_note' => new DeliveryNoteResource($deliveryNote->load(['client', 'items.service'])),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating delivery note',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function show(DeliveryNote $deliveryNote): JsonResponse
    {
        $deliveryNote->load(['client', 'items.service']);

        return response()->json([
            'delivery_note' => new DeliveryNoteResource($deliveryNote),
        ]);
    }

    public function update(UpdateDeliveryNoteRequest $request, DeliveryNote $deliveryNote): JsonResponse
    {
        try {
            if ($deliveryNote->status === 'delivered' && !$request->boolean('force_update')) {
                return response()->json([
                    'message' => 'Cannot update a delivery note that has already been delivered',
                ], 422);
            }

            DB::transaction(function () use ($request, $deliveryNote) {
                $deliveryNote->update($request->safe()->except('items'));

                if ($request->has('items') && is_array($request->items)) {
                    $deliveryNote->items()->delete();

                    foreach ($request->items as $index => $itemData) {
                        $deliveryNote->items()->create([
                            'service_id'  => $itemData['service_id'] ?? null,
                            'description' => $itemData['description'],
                            'quantity'    => $itemData['quantity'],
                            'unit'        => $itemData['unit'] ?? null,
                            'unit_price'  => $itemData['unit_price'] ?? null,
                            'sort_order'  => $index,
                        ]);
                    }
                }
            });

            return response()->json([
                'message'       => 'Delivery note updated successfully',
                'delivery_note' => new DeliveryNoteResource($deliveryNote->load(['client', 'items.service'])),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating delivery note',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(DeliveryNote $deliveryNote): JsonResponse
    {
        try {
            if ($deliveryNote->status === 'delivered') {
                return response()->json([
                    'message' => 'Cannot delete a delivery note that has already been delivered',
                ], 422);
            }

            $deliveryNote->delete();

            return response()->json([
                'message' => 'Delivery note deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting delivery note',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark as dispatched (draft -> pending)
     */
    public function markAsDispatched(DeliveryNote $deliveryNote): JsonResponse
    {
        if ($deliveryNote->status !== 'draft') {
            return response()->json([
                'message' => 'Only draft delivery notes can be dispatched',
            ], 422);
        }

        $deliveryNote->markAsDispatched();

        return response()->json([
            'message'       => 'Delivery note marked as dispatched',
            'delivery_note' => new DeliveryNoteResource($deliveryNote->load(['client', 'items.service'])),
        ]);
    }

    /**
     * Mark as delivered, optionally recording who received it and when.
     */
    public function markAsDelivered(Request $request, DeliveryNote $deliveryNote): JsonResponse
    {
        $request->validate([
            'received_by'   => 'nullable|string|max:255',
            'received_date' => 'nullable|date',
        ]);

        if ($deliveryNote->status === 'cancelled') {
            return response()->json([
                'message' => 'Cannot mark a cancelled delivery note as delivered',
            ], 422);
        }

        $deliveryNote->markAsDelivered($request->received_by, $request->received_date);

        return response()->json([
            'message'       => 'Delivery note marked as delivered',
            'delivery_note' => new DeliveryNoteResource($deliveryNote->load(['client', 'items.service'])),
        ]);
    }

    public function cancel(DeliveryNote $deliveryNote): JsonResponse
    {
        if ($deliveryNote->status === 'delivered') {
            return response()->json([
                'message' => 'Cannot cancel a delivery note that has already been delivered',
            ], 422);
        }

        $deliveryNote->update(['status' => 'cancelled']);

        return response()->json([
            'message'       => 'Delivery note cancelled',
            'delivery_note' => new DeliveryNoteResource($deliveryNote->load(['client', 'items.service'])),
        ]);
    }

    /**
     * Pre-fill payload (client + items) for creating a delivery note from an
     * existing invoice. This is a one-off copy — the front-end takes this
     * response, lets the user adjust it, then POSTs to store() as normal.
     */
    public function fromInvoice(Invoice $invoice): JsonResponse
    {
        return response()->json([
            'prefill' => DeliveryNote::fromInvoice($invoice),
        ]);
    }

    public function fromEstimate(Estimate $estimate): JsonResponse
    {
        return response()->json([
            'prefill' => DeliveryNote::fromEstimate($estimate),
        ]);
    }

    /**
     * Export delivery note as PDF
     */
    public function export(DeliveryNote $deliveryNote)
    {
        $deliveryNote->load(['client', 'items.service']);

        $pdf = Pdf::loadView('delivery-notes.pdf', ['deliveryNote' => $deliveryNote])
            ->setPaper('a4', 'portrait');

        $filename = 'delivery-note-' . $deliveryNote->delivery_number . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Get delivery note statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $query = DeliveryNote::query();

        if ($request->filled('date_from')) {
            $query->where('delivery_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('delivery_date', '<=', $request->date_to);
        }

        $stats = [
            'total_delivery_notes'     => (clone $query)->count(),
            'draft_delivery_notes'     => (clone $query)->where('status', 'draft')->count(),
            'pending_delivery_notes'   => (clone $query)->where('status', 'pending')->count(),
            'delivered_delivery_notes' => (clone $query)->where('status', 'delivered')->count(),
            'cancelled_delivery_notes' => (clone $query)->where('status', 'cancelled')->count(),
        ];

        $recent = (clone $query)
            ->with(['client'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'statistics'          => $stats,
            'recent_delivery_notes' => DeliveryNoteResource::collection($recent),
        ]);
    }
}