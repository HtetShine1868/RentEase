<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laundry_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_reference', 50)->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_provider_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            
            // Order Type
            $table->enum('service_mode', ['NORMAL', 'RUSH'])->default('NORMAL');
            
            // Pickup/Delivery
            $table->text('pickup_address');
            $table->decimal('pickup_latitude', 10, 7);
            $table->decimal('pickup_longitude', 10, 7);
            $table->decimal('distance_km', 5, 2);
            $table->dateTime('pickup_time');
            $table->text('pickup_instructions')->nullable();
            $table->date('expected_return_date');
            $table->date('actual_return_date')->nullable();
            
            // Status
            $table->enum('status', ['PENDING', 'PICKUP_SCHEDULED', 'PICKED_UP', 'IN_PROGRESS', 'READY', 'OUT_FOR_DELIVERY', 'DELIVERED', 'CANCELLED'])->default('PENDING');
            
            // Pricing
            $table->decimal('base_amount', 10, 2);
            $table->decimal('rush_surcharge', 10, 2)->default(0.00);
            $table->decimal('pickup_fee', 10, 2)->default(0.00);
            $table->decimal('commission_amount', 10, 2);
            $table->decimal('total_amount', 10, 2);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laundry_orders');
    }
};