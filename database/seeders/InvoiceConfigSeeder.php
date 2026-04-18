<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds static, rarely-changing configuration values used across the invoicing module.
 * Add new config keys here as the accounting suite grows.
 */
class InvoiceConfigSeeder extends Seeder
{
    public function run(): void
    {
        $configs = [
            // Payment methods available when recording a payment
            ['group' => 'payment_methods', 'key' => 'bank_transfer',   'label' => 'Bank Transfer',   'sort_order' => 1],
            ['group' => 'payment_methods', 'key' => 'cash',            'label' => 'Cash',             'sort_order' => 2],
            ['group' => 'payment_methods', 'key' => 'credit_card',     'label' => 'Credit Card',      'sort_order' => 3],
            ['group' => 'payment_methods', 'key' => 'debit_card',      'label' => 'Debit Card',       'sort_order' => 4],
            ['group' => 'payment_methods', 'key' => 'eft',             'label' => 'EFT',              'sort_order' => 5],
            ['group' => 'payment_methods', 'key' => 'paypal',          'label' => 'PayPal',           'sort_order' => 6],
            ['group' => 'payment_methods', 'key' => 'cheque',          'label' => 'Cheque',           'sort_order' => 7],
            ['group' => 'payment_methods', 'key' => 'other',           'label' => 'Other',            'sort_order' => 8],

            // Recurring invoice frequency options
            ['group' => 'recurrence_frequencies', 'key' => 'weekly',    'label' => 'Weekly',    'sort_order' => 1],
            ['group' => 'recurrence_frequencies', 'key' => 'monthly',   'label' => 'Monthly',   'sort_order' => 2],
            ['group' => 'recurrence_frequencies', 'key' => 'quarterly', 'label' => 'Quarterly', 'sort_order' => 3],
            ['group' => 'recurrence_frequencies', 'key' => 'annually',  'label' => 'Annually',  'sort_order' => 4],
            ['group' => 'recurrence_frequencies', 'key' => 'custom',    'label' => 'Custom',    'sort_order' => 5],

            // Interval units for custom frequency
            ['group' => 'interval_units', 'key' => 'day',   'label' => 'Day(s)',   'sort_order' => 1],
            ['group' => 'interval_units', 'key' => 'week',  'label' => 'Week(s)',  'sort_order' => 2],
            ['group' => 'interval_units', 'key' => 'month', 'label' => 'Month(s)', 'sort_order' => 3],
            ['group' => 'interval_units', 'key' => 'year',  'label' => 'Year(s)',  'sort_order' => 4],

            // Actions the system takes when a recurring invoice generates
            ['group' => 'recurrence_actions', 'key' => 'draft',         'label' => 'Create as Draft (manual review)', 'sort_order' => 1],
            ['group' => 'recurrence_actions', 'key' => 'send',          'label' => 'Auto-send to Client',             'sort_order' => 2],
            ['group' => 'recurrence_actions', 'key' => 'send_if_valid', 'label' => 'Auto-send if client has email',   'sort_order' => 3],

            // Default net terms (days until due) — add more as needed
            ['group' => 'net_terms', 'key' => '0',  'label' => 'Due on Receipt', 'sort_order' => 1],
            ['group' => 'net_terms', 'key' => '7',  'label' => 'Net 7',          'sort_order' => 2],
            ['group' => 'net_terms', 'key' => '14', 'label' => 'Net 14',         'sort_order' => 3],
            ['group' => 'net_terms', 'key' => '30', 'label' => 'Net 30',         'sort_order' => 4],
            ['group' => 'net_terms', 'key' => '60', 'label' => 'Net 60',         'sort_order' => 5],
            ['group' => 'net_terms', 'key' => '90', 'label' => 'Net 90',         'sort_order' => 6],
        ];

        // Create the table if it doesn't exist yet; avoids needing a dedicated migration
        if (!DB::getSchemaBuilder()->hasTable('invoice_configs')) {
            DB::getSchemaBuilder()->create('invoice_configs', function ($table) {
                $table->id();
                $table->string('group');
                $table->string('key');
                $table->string('label');
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->unique(['group', 'key']);
                $table->index(['group', 'is_active', 'sort_order']);
            });
        }

        foreach ($configs as $config) {
            DB::table('invoice_configs')->updateOrInsert(
                ['group' => $config['group'], 'key' => $config['key']],
                array_merge($config, [
                    'is_active'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}