<?php
// app/Http/Resources/DeliveryNoteResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryNoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'delivery_number'  => $this->delivery_number,
            'status'           => $this->status,
            'delivery_date'    => $this->delivery_date->format('Y-m-d'),
            'delivery_address' => $this->delivery_address,
            'notes'            => $this->notes,

            'received_by'   => $this->received_by,
            'received_date' => $this->received_date?->format('Y-m-d'),

            'is_delivered'  => $this->is_delivered,
            'dispatched_at' => $this->dispatched_at?->format('Y-m-d H:i:s'),
            'delivered_at'  => $this->delivered_at?->format('Y-m-d H:i:s'),

            // Source document (if converted from one) — display-only reference,
            // not a live link.
            'invoice_id'   => $this->invoice_id,
            'estimate_id' => $this->estimate_id,
            'source' => $this->when($this->invoice_id || $this->estimate_id, function () {
                if ($this->invoice_id) {
                    return ['type' => 'invoice', 'id' => $this->invoice_id, 'number' => $this->invoice?->invoice_number];
                }
                return ['type' => 'estimate', 'id' => $this->estimate_id, 'number' => $this->estimate?->estimate_number];
            }),

            'client' => $this->whenLoaded('client', function () {
                return [
                    'id'      => $this->client->id,
                    'name'    => $this->client->name,
                    'email'   => $this->client->email,
                    'phone'   => $this->client->phone ?? null,
                    'address' => $this->client->address ?? null,
                ];
            }),

            'items'       => DeliveryNoteItemResource::collection($this->whenLoaded('items')),
            'items_count' => $this->whenLoaded('items', fn () => $this->items->count()),

            'status_badge' => [
                'color' => $this->getStatusColor(),
                'text'  => ucfirst($this->status),
            ],

            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    private function getStatusColor(): string
    {
        return match ($this->status) {
            'draft'     => 'gray',
            'pending'   => 'amber',
            'delivered' => 'green',
            'cancelled' => 'red',
            default     => 'gray',
        };
    }
}