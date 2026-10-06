<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Return every settings group the front-end needs in one call.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'company'   => Setting::get('company', 'profile', $this->companyDefaults()),
            'invoice'   => Setting::get('invoice', 'preferences', $this->invoiceDefaults()),
            'quotation' => Setting::get('quotation', 'preferences', $this->quotationDefaults()),
            'banking'   => Setting::get('banking', 'accounts', []),
        ]);
    }

    /**
     * Persist one or more settings groups. Each group is sent (and stored)
     * as a single JSON blob, so a partial payload only updates the groups
     * that are present.
     */
    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'company'                    => 'sometimes|array',
            'company.name'               => 'nullable|string|max:255',
            'company.tax_number'         => 'nullable|string|max:100',
            'company.address_line1'      => 'nullable|string|max:255',
            'company.address_line2'      => 'nullable|string|max:255',
            'company.city'               => 'nullable|string|max:255',
            'company.state'              => 'nullable|string|max:255',
            'company.postal_code'        => 'nullable|string|max:50',
            'company.country'            => 'nullable|string|max:255',
            'company.phone'              => 'nullable|string|max:50',
            'company.email'              => 'nullable|email|max:255',
            'company.website'            => 'nullable|string|max:255',

            'invoice'                          => 'sometimes|array',
            'invoice.prefix'                   => 'nullable|string|max:20',
            'invoice.next_number'              => 'nullable|integer|min:1',
            'invoice.default_tax_rate'         => 'nullable|numeric|min:0|max:100',
            'invoice.default_discount_rate'    => 'nullable|numeric|min:0|max:100',
            'invoice.default_net_terms_days'   => 'nullable|integer|min:0',
            'invoice.default_notes'            => 'nullable|string',
            'invoice.default_terms'            => 'nullable|string',

            'quotation'                        => 'sometimes|array',
            'quotation.prefix'                 => 'nullable|string|max:20',
            'quotation.next_number'            => 'nullable|integer|min:1',
            'quotation.default_tax_rate'       => 'nullable|numeric|min:0|max:100',
            'quotation.valid_days'             => 'nullable|integer|min:0',
            'quotation.default_notes'          => 'nullable|string',
            'quotation.default_terms'          => 'nullable|string',

            'banking'                     => 'sometimes|array',
            'banking.*.name'              => 'required_with:banking|string|max:255',
            'banking.*.bank'              => 'required_with:banking|string|max:255',
            'banking.*.account_number'    => 'required_with:banking|string|max:100',
            'banking.*.branch'            => 'nullable|string|max:255',
            'banking.*.phone'             => 'nullable|string|max:50',
        ]);

        if (array_key_exists('company', $data)) {
            Setting::set('company', 'profile', $data['company']);
        }
        if (array_key_exists('invoice', $data)) {
            Setting::set('invoice', 'preferences', $data['invoice']);
        }
        if (array_key_exists('quotation', $data)) {
            Setting::set('quotation', 'preferences', $data['quotation']);
        }
        if (array_key_exists('banking', $data)) {
            // Re-index and stamp each account with a stable id for front-end keys
            $accounts = collect($data['banking'])->values()->map(function ($account, $index) {
                $account['id'] = $account['id'] ?? (string) \Illuminate\Support\Str::uuid();
                return $account;
            })->all();

            Setting::set('banking', 'accounts', $accounts);
        }

        return response()->json(['message' => 'Settings saved successfully.']);
    }

    /**
     * Lightweight endpoint used by the invoice/quotation form modals to
     * populate the "insert bank details" picker without loading everything.
     */
    public function bankAccounts(): JsonResponse
    {
        return response()->json(Setting::get('banking', 'accounts', []));
    }

    private function companyDefaults(): array
    {
        return [
            'name'          => config('invoice.company_name', config('app.name')),
            'tax_number'    => '',
            'address_line1' => config('invoice.address_line1', ''),
            'address_line2' => config('invoice.address_line2', ''),
            'city'          => config('invoice.city', ''),
            'state'         => '',
            'postal_code'   => '',
            'country'       => config('invoice.country', ''),
            'phone'         => config('invoice.phone', ''),
            'email'         => '',
            'website'       => config('invoice.website', ''),
        ];
    }

    private function invoiceDefaults(): array
    {
        return [
            'prefix'                 => 'INV-',
            'next_number'            => 1,
            'default_tax_rate'       => 15,
            'default_discount_rate'  => 0,
            'default_net_terms_days' => 30,
            'default_notes'          => '',
            'default_terms'          => '',
        ];
    }

    private function quotationDefaults(): array
    {
        return [
            'prefix'            => 'QUO-',
            'next_number'       => 1,
            'default_tax_rate'  => 15,
            'valid_days'        => 30,
            'default_notes'     => '',
            'default_terms'     => '',
        ];
    }
}