<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EstimateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quote_number' => $this->quote_number,
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status,
            'notes' => $this->notes,
            'terms' => $this->terms,

            'total_amount' => $this->total_amount,
            'formatted_total_amount' => $this->formatted_total_amount,

            'expiry_date' => $this->expiry_date?->format('Y-m-d'),
            'is_expired' => $this->is_expired,
            'is_editable' => $this->is_editable,

            // Conversion info
            'invoice_id' => $this->invoice_id,
            'converted_at' => $this->converted_at?->format('Y-m-d H:i:s'),
            'invoice' => $this->whenLoaded('invoice', function () {
                return $this->invoice ? [
                    'id' => $this->invoice->id,
                    'invoice_number' => $this->invoice->invoice_number,
                    'status' => $this->invoice->status,
                    'total' => $this->invoice->total,
                ] : null;
            }),

            'services' => $this->whenLoaded('services', function () {
                return $this->services->map(function ($estimateService) {
                    return [
                        'id' => $estimateService->service_id,
                        'name' => $estimateService->service->name ?? 'Unknown Service',
                        'price' => $estimateService->price,
                    ];
                });
            }),

            'status_badge' => [
                'color' => $this->getStatusColor(),
                'text' => ucfirst(str_replace('_', ' ', $this->status)),
            ],

            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    private function getStatusColor(): string
    {
        return match ($this->status) {
            'pending' => 'amber',
            'emailed' => 'blue',
            'email_failed' => 'red',
            'completed' => 'green',
            'converted' => 'teal',
            'cancelled' => 'gray',
            default => 'gray',
        };
    }
}