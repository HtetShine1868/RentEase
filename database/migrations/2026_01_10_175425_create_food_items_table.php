<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_provider_id')->constrained()->onDelete('cascade');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->foreignId('meal_type_id')->constrained();
            
            // Pricing
            $table->decimal('base_price', 10, 2);
            $table->decimal('commission_rate', 5, 2)->default(8.00);
            $table->decimal('total_price', 10, 2)->virtualAs('base_price + (base_price * commission_rate / 100)');
            
            // Availability
            $table->boolean('is_available')->default(true);
            $table->integer('daily_quantity')->nullable();
            $table->integer('sold_today')->default(0);
            
            // Dietary Info
            $table->json('dietary_tags')->nullable();
            $table->integer('calories')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['service_provider_id', 'meal_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_items');
    }
};