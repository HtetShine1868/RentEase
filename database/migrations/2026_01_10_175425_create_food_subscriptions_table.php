<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('food_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_provider_id')->constrained('service_providers')->onDelete('cascade');
            $table->foreignId('meal_type_id')->constrained('meal_types')->onDelete('cascade');

            // Subscription Period
            $table->date('start_date');
            $table->date('end_date');
            $table->time('delivery_time');

            // Days of week as bitmask (1=Sun, 2=Mon, 4=Tue...)
            $table->tinyInteger('delivery_days')->default(127); // all days

            // Status
            $table->enum('status', ['ACTIVE','PAUSED','CANCELLED','COMPLETED'])->default('ACTIVE');

            // Pricing
            $table->decimal('daily_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0.00);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_subscriptions');
    }
};
