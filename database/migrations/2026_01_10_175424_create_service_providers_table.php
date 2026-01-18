<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->unique();
            $table->enum('service_type', ['LAUNDRY', 'FOOD']);
            $table->string('business_name', 200);
            $table->text('description')->nullable();
            
            // Contact
            $table->string('contact_email', 150);
            $table->string('contact_phone', 20);
            
            // Location
            $table->text('address');
            $table->string('city', 100);
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('service_radius_km', 5, 2)->default(5.00);
            
            // Status
            $table->enum('status', ['ACTIVE', 'SUSPENDED', 'UNDER_REVIEW'])->default('ACTIVE');
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('total_orders')->default(0);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['service_type', 'latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_providers');
    }
};