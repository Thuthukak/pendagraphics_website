<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_notes', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_number')->unique();

            $table->foreignId('client_id')->constrained()->cascadeOnDelete();

            // Optional source document — a delivery note can be created standalone
            // OR converted from an existing Invoice / Estimate (client + items copied
            // across at creation time; no live link back to the source afterwards).
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('estimate_id')->nullable()->constrained()->nullOnDelete();

            $table->date('delivery_date');
            $table->string('status')->default('draft'); // draft, pending, delivered, cancelled

            $table->text('delivery_address')->nullable();
            $table->text('notes')->nullable();

            // Lightweight proof-of-delivery (no signature capture)
            $table->string('received_by')->nullable();
            $table->date('received_date')->nullable();

            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            $table->timestamps();

            $table->index(['status']);
            $table->index(['client_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_notes');
    }
};