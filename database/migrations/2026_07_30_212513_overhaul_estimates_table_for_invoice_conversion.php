<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Brings the `estimates` table up to parity with `invoices` so quotations
     * can be managed the same way (numbering, expiry, terms) and can be
     * converted into a real Invoice while keeping a link back to it.
     */
    public function up(): void
    {
        Schema::table('estimates', function (Blueprint $table) {
            // Human-friendly reference, e.g. QUO-2026-0001 (mirrors invoice_number)
            if (!Schema::hasColumn('estimates', 'quote_number')) {
                $table->string('quote_number')->nullable()->unique()->after('id');
            }

            // Quotes should expire — gives the table the same "date pressure" invoices have
            if (!Schema::hasColumn('estimates', 'expiry_date')) {
                $table->date('expiry_date')->nullable()->after('total_amount');
            }

            // Free-text terms, same field invoices already expose
            if (!Schema::hasColumn('estimates', 'terms')) {
                $table->text('terms')->nullable()->after('notes');
            }

            // Link to the invoice this quote was converted into, if any
            if (!Schema::hasColumn('estimates', 'invoice_id')) {
                $table->foreignId('invoice_id')
                    ->nullable()
                    ->after('status')
                    ->constrained('invoices')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('estimates', 'converted_at')) {
                $table->timestamp('converted_at')->nullable()->after('invoice_id');
            }
        });

        // Backfill quote numbers for existing rows so the unique index has no collisions.
        $rows = \Illuminate\Support\Facades\DB::table('estimates')
            ->whereNull('quote_number')
            ->orderBy('id')
            ->get(['id', 'created_at']);

        foreach ($rows as $row) {
            $year = $row->created_at ? date('Y', strtotime($row->created_at)) : date('Y');
            $number = 'QUO-' . $year . '-' . str_pad((string) $row->id, 4, '0', STR_PAD_LEFT);

            \Illuminate\Support\Facades\DB::table('estimates')
                ->where('id', $row->id)
                ->update(['quote_number' => $number]);
        }
    }

    public function down(): void
    {
        Schema::table('estimates', function (Blueprint $table) {
            if (Schema::hasColumn('estimates', 'invoice_id')) {
                $table->dropConstrainedForeignId('invoice_id');
            }
            $table->dropColumn(array_filter([
                Schema::hasColumn('estimates', 'quote_number') ? 'quote_number' : null,
                Schema::hasColumn('estimates', 'expiry_date') ? 'expiry_date' : null,
                Schema::hasColumn('estimates', 'terms') ? 'terms' : null,
                Schema::hasColumn('estimates', 'converted_at') ? 'converted_at' : null,
            ]));
        });
    }
};