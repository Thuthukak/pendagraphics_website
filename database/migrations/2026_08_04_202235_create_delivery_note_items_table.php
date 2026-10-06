<?php
// database/migrations/2026_08_03_000001_create_delivery_note_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_note_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_note_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();

            $table->string('description');
            $table->decimal('quantity', 10, 2)->default(1);
            $table->string('unit', 30)->nullable(); // e.g. pcs, boxes, kg — purely descriptive

            // Kept for reference only (e.g. printing a value alongside the delivery note).
            // Delivery notes are not billing documents, so this is optional and never
            // summed into a total the way invoice items are.
            $table->decimal('unit_price', 10, 2)->nullable();

            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_note_items');
    }
};