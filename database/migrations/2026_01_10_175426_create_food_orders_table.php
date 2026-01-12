<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('food_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_reference',50)->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_provider_id')->constrained('service_providers')->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained('food_subscriptions')->nullOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->foreignId('meal_type_id')->constrained('meal_types')->onDelete('cascade');

            $table->enum('order_type',['PAY_PER_EAT','SUBSCRIPTION_MEAL']);
            $table->date('meal_date');
            $table->text('delivery_address');
            $table->decimal('delivery_latitude',10,7);
            $table->decimal('delivery_longitude',10,7);
            $table->decimal('distance_km',5,2);
            $table->text('delivery_instructions')->nullable();

            // Status & Timing
            $table->enum('status',['PENDING','ACCEPTED','PREPARING','OUT_FOR_DELIVERY','DELIVERED','CANCELLED'])->default('PENDING');
            $table->dateTime('estimated_delivery_time');
            $table->dateTime('actual_delivery_time')->nullable();

            // Pricing
            $table->decimal('base_amount',10,2);
            $table->decimal('delivery_fee',10,2)->default(0.00);
            $table->decimal('commission_amount',10,2);
            $table->decimal('total_amount',10,2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_orders');
    }
};
