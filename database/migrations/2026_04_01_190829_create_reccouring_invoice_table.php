<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. "Monthly Hosting - Acme Corp"
            $table->foreignId('client_id')->constrained()->onDelete('cascade');

            // Scheduling
            $table->string('frequency'); // weekly|monthly|quarterly|annually|custom
            $table->unsignedSmallInteger('interval')->default(1); // e.g. every 2 months
            $table->string('interval_unit')->nullable(); // for custom: day|week|month|year
            $table->date('start_date');
            $table->date('end_date')->nullable(); // null = runs indefinitely
            $table->date('next_run_date');
            $table->date('last_run_date')->nullable();
            $table->unsignedInteger('occurrences_limit')->nullable(); // null = unlimited
            $table->unsignedInteger('occurrences_count')->default(0);

            // Invoice template fields
            $table->unsignedSmallInteger('days_until_due')->default(30);
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('discount_rate', 5, 2)->default(0);

            // Automation behaviour
            $table->string('action_on_create')->default('draft');
            // draft         = create invoice as draft (manual review before sending)
            // send          = auto-send email to client immediately
            // send_if_valid = send if client has email, else draft

            $table->boolean('notify_admin')->default(true); // email admin when invoice is created
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'next_run_date']);
            $table->index('client_id');
        });

        Schema::create('recurring_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recurring_invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained()->onDelete('set null');
            $table->string('description');
            $table->decimal('quantity', 10, 2)->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['recurring_invoice_id', 'sort_order']);
        });

        // Audit log for every generated invoice from a recurring schedule
        Schema::create('recurring_invoice_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recurring_invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('set null');
            $table->string('status'); // created|sent|failed
            $table->text('message')->nullable();
            $table->timestamp('ran_at');
            $table->timestamps();

            $table->index('recurring_invoice_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_invoice_logs');
        Schema::dropIfExists('recurring_invoice_items');
        Schema::dropIfExists('recurring_invoices');
    }
};