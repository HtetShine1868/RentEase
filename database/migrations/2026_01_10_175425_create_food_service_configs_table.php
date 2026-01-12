<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('food_service_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_provider_id')->unique()->constrained('service_providers')->onDelete('cascade');

            // Service Types
            $table->boolean('supports_subscription')->default(false);
            $table->boolean('supports_pay_per_eat')->default(true);

            // Timing
            $table->time('opening_time')->default('08:00:00');
            $table->time('closing_time')->default('22:00:00');
            $table->integer('avg_preparation_minutes')->default(30);
            $table->integer('delivery_buffer_minutes')->default(15);

            // Subscription Settings
            $table->decimal('subscription_discount_percent', 5, 2)->default(10.00);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_service_configs');
    }
};
