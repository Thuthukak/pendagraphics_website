<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRecurringInvoiceRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'               => 'sometimes|required|string|max:255',
            'client_id'          => 'sometimes|required|exists:clients,id',
            'frequency'          => 'sometimes|required|in:weekly,monthly,quarterly,annually,custom',
            'interval'           => 'sometimes|required|integer|min:1',
            'interval_unit'      => 'required_if:frequency,custom|nullable|in:day,week,month,year',
            'start_date'         => 'sometimes|required|date',
            'end_date'           => 'nullable|date|after:start_date',
            'occurrences_limit'  => 'nullable|integer|min:1',
            'days_until_due'     => 'sometimes|required|integer|min:0',
            'notes'              => 'nullable|string|max:1000',
            'terms'              => 'nullable|string|max:1000',
            'tax_rate'           => 'nullable|numeric|min:0|max:100',
            'discount_rate'      => 'nullable|numeric|min:0|max:100',
            'action_on_create'   => 'sometimes|required|in:draft,send,send_if_valid',
            'notify_admin'       => 'nullable|boolean',
            'items'              => 'sometimes|required|array|min:1',
            'items.*.service_id' => 'nullable|exists:services,id',
            'items.*.description'=> 'required_with:items|string|max:255',
            'items.*.quantity'   => 'required_with:items|numeric|min:0.01',
            'items.*.unit_price' => 'required_with:items|numeric|min:0',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'tax_rate'      => $this->tax_rate ?? 0,
            'discount_rate' => $this->discount_rate ?? 0,
            'notify_admin'  => $this->notify_admin ?? true,
            'interval'      => $this->interval ?? 1,
        ]);
    }
}