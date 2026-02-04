<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_service_meal_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_provider_id')->constrained('service_providers')->onDelete('cascade');
            $table->foreignId('meal_type_id')->constrained('meal_types')->onDelete('cascade');
            $table->timestamps();
            
            // Unique constraint to prevent duplicate entries
            $table->unique(['service_provider_id', 'meal_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_service_meal_types');
    }
};