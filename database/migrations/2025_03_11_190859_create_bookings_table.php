<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->string('reference');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('barber_id')->constrained()->onDelete('cascade');
            $table->string('bookingSlot')>nullable();
            $table->enum('status', ['queued', 'in-progress', 'completed', 'skipped', 're-booked', 'no-show',])->default('queued');
            $table->timestamp('start_time')->nullable();
            $table->integer('skipCount')->default(0); // number of times skipped
            $table->softDeletes(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
