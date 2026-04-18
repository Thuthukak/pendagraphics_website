<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecurringInvoiceRequest;
use App\Http\Requests\UpdateRecurringInvoiceRequest;
use App\Models\Client;
use App\Models\RecurringInvoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecurringInvoiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = RecurringInvoice::with(['client', 'items'])
            ->withCount('logs');

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }
        if ($request->filled('frequency')) {
            $query->where('frequency', $request->frequency);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                ->orWhereHas('client', fn($cq) => $cq->where('name', 'like', "%{$request->search}%"));
            });
        }

        $perPage = $request->get('per_page', 15);
        return response()->json($query->orderBy('next_run_date')->paginate($perPage));
    }

    public function store(StoreRecurringInvoiceRequest $request): JsonResponse
    {
        $recurring = DB::transaction(function () use ($request) {
            $recurring = RecurringInvoice::create(
                array_merge($request->validated(), ['next_run_date' => $request->start_date])
            );

            foreach ($request->items ?? [] as $i => $item) {
                $recurring->items()->create([
                    'service_id'  => $item['service_id'] ?? null,
                    'description' => $item['description'],
                    'quantity'    => $item['quantity'] ?? 1,
                    'unit_price'  => $item['unit_price'],
                    'sort_order'  => $i,
                ]);
            }

            return $recurring->load(['client', 'items']);
        });

        return response()->json([
            'message'   => 'Recurring invoice schedule created.',
            'recurring' => $recurring,
        ], 201);
    }

    public function show(RecurringInvoice $recurringInvoice): JsonResponse
    {
        return response()->json(
            $recurringInvoice->load(['client', 'items', 'logs.invoice'])
        );
    }

    public function update(UpdateRecurringInvoiceRequest $request, RecurringInvoice $recurringInvoice): JsonResponse
    {
        DB::transaction(function () use ($request, $recurringInvoice) {
            $recurringInvoice->update($request->validated());

            if ($request->has('items')) {
                $recurringInvoice->items()->delete();
                foreach ($request->items as $i => $item) {
                    $recurringInvoice->items()->create([
                        'service_id'  => $item['service_id'] ?? null,
                        'description' => $item['description'],
                        'quantity'    => $item['quantity'] ?? 1,
                        'unit_price'  => $item['unit_price'],
                        'sort_order'  => $i,
                    ]);
                }
            }
        });

        return response()->json([
            'message'   => 'Recurring invoice updated.',
            'recurring' => $recurringInvoice->fresh(['client', 'items']),
        ]);
    }

    public function destroy(RecurringInvoice $recurringInvoice): JsonResponse
    {
        $recurringInvoice->delete(); // soft delete
        return response()->json(['message' => 'Recurring invoice deleted.']);
    }

    public function toggleActive(RecurringInvoice $recurringInvoice): JsonResponse
    {
        $recurringInvoice->update(['is_active' => !$recurringInvoice->is_active]);
        $status = $recurringInvoice->is_active ? 'activated' : 'paused';
        return response()->json([
            'message'   => "Recurring invoice {$status}.",
            'is_active' => $recurringInvoice->is_active,
        ]);
    }

    public function logs(RecurringInvoice $recurringInvoice): JsonResponse
    {
        $logs = $recurringInvoice->logs()->with('invoice')->paginate(20);
        return response()->json($logs);
    }

    /** Preview the next N scheduled dates without running anything */
    public function previewDates(RecurringInvoice $recurringInvoice, int $count = 5): JsonResponse
    {
        $dates = [];
        $next = $recurringInvoice->next_run_date->copy();

        for ($i = 0; $i < min($count, 12); $i++) {
            if ($recurringInvoice->end_date && $next->gt($recurringInvoice->end_date)) break;
            if ($recurringInvoice->occurrences_limit
                && ($recurringInvoice->occurrences_count + $i) >= $recurringInvoice->occurrences_limit) break;

            $dates[] = $next->toDateString();
            $next = $recurringInvoice->calculateNextRunDate($next);
        }

        return response()->json(['dates' => $dates]);
    }
}