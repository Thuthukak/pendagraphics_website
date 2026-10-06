<?php
// app/Models/DeliveryNote.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_number',
        'client_id',
        'invoice_id',
        'estimate_id',
        'delivery_date',
        'status',
        'delivery_address',
        'notes',
        'received_by',
        'received_date',
        'dispatched_at',
        'delivered_at',
    ];

    protected $casts = [
        'delivery_date'  => 'date',
        'received_date'  => 'date',
        'dispatched_at'  => 'datetime',
        'delivered_at'   => 'datetime',
    ];

    // Relationships
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function estimate(): BelongsTo
    {
        return $this->belongsTo(Estimate::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(DeliveryNoteItem::class)->orderBy('sort_order');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('delivery_date', [$startDate, $endDate]);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'pending']);
    }

    // Accessors
    public function getIsDeliveredAttribute(): bool
    {
        return $this->status === 'delivered';
    }

    // Methods
    public function generateDeliveryNumber(): string
    {
        $year = now()->year;
        $last = static::whereYear('created_at', $year)
                        ->orderBy('id', 'desc')
                        ->first();

        $number = $last
            ? intval(substr($last->delivery_number, -4)) + 1
            : 1;

        return 'DN-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function markAsDispatched(): void
    {
        $this->update([
            'status'        => 'pending',
            'dispatched_at' => now(),
        ]);
    }

    public function markAsDelivered(?string $receivedBy = null, ?string $receivedDate = null): void
    {
        $this->update([
            'status'        => 'delivered',
            'delivered_at'  => now(),
            'received_by'   => $receivedBy ?? $this->received_by,
            'received_date' => $receivedDate ?? now()->toDateString(),
        ]);
    }

    /**
     * Build a fresh, unsaved DeliveryNote (with items) from an Invoice.
     * Used by the controller when converting an invoice into a delivery note.
     * This is a one-time copy — the delivery note has no live link back to
     * the invoice's line items or quantities after creation.
     */
    public static function fromInvoice(Invoice $invoice): array
    {
        $invoice->loadMissing('items');

        return [
            'client_id'  => $invoice->client_id,
            'invoice_id' => $invoice->id,
            'items'      => $invoice->items->map(fn ($item) => [
                'service_id' => $item->service_id,
                'description' => $item->description,
                'quantity'    => $item->quantity,
                'unit_price'  => $item->unit_price,
            ])->values()->all(),
        ];
    }

    public static function fromEstimate(Estimate $estimate): array
    {
        $estimate->loadMissing('items');

        return [
            'client_id'    => $estimate->client_id,
            'estimate_id' => $estimate->id,
            'items'        => $estimate->items->map(fn ($item) => [
                'service_id' => $item->service_id,
                'description' => $item->description,
                'quantity'    => $item->quantity,
                'unit_price'  => $item->unit_price,
            ])->values()->all(),
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($deliveryNote) {
            if (empty($deliveryNote->delivery_number)) {
                $deliveryNote->delivery_number = $deliveryNote->generateDeliveryNumber();
            }
        });
    }
}