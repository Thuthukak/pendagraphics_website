<?php
// app/Http/Resources/DeliveryNoteItemResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryNoteItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'description' => $this->description,
            'quantity'    => $this->quantity,
            'unit'        => $this->unit,
            'unit_price'  => $this->unit_price,
            'sort_order'  => $this->sort_order,

            'formatted_quantity'   => number_format($this->quantity, 2),
            'formatted_unit_price' => $this->unit_price !== null ? 'R' . number_format($this->unit_price, 2) : null,

            'service' => $this->whenLoaded('service', function () {
                return $this->service ? [
                    'id'   => $this->service->id,
                    'name' => $this->service->name,
                ] : null;
            }),

            'service_id' => $this->service_id,

            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}