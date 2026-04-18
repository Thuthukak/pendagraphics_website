<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecurringInvoiceRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'               => 'required|string|max:255',
            'client_id'          => 'required|exists:clients,id',
            'frequency'          => 'required|in:weekly,monthly,quarterly,annually,custom',
            'interval'           => 'required|integer|min:1',
            'interval_unit'      => 'required_if:frequency,custom|nullable|in:day,week,month,year',
            'start_date'         => 'required|date|after_or_equal:today',
            'end_date'           => 'nullable|date|after:start_date',
            'occurrences_limit'  => 'nullable|integer|min:1',
            'days_until_due'     => 'required|integer|min:0',
            'notes'              => 'nullable|string|max:1000',
            'terms'              => 'nullable|string|max:1000',
            'tax_rate'           => 'nullable|numeric|min:0|max:100',
            'discount_rate'      => 'nullable|numeric|min:0|max:100',
            'action_on_create'   => 'required|in:draft,send,send_if_valid',
            'notify_admin'       => 'nullable|boolean',
            'items'              => 'required|array|min:1',
            'items.*.service_id' => 'nullable|exists:services,id',
            'items.*.description'=> 'required|string|max:255',
            'items.*.quantity'   => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
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