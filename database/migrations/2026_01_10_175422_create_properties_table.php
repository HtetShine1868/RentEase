<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['HOSTEL','APARTMENT']);
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->text('address');
            $table->string('city', 100);
            $table->string('area', 100);
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            // Property Details
            $table->enum('status', ['DRAFT','PENDING','ACTIVE','INACTIVE'])->default('DRAFT');
            $table->enum('gender_policy', ['MALE_ONLY','FEMALE_ONLY','MIXED'])->default('MIXED');
            $table->integer('unit_size')->nullable();
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->enum('furnishing_status', ['FURNISHED','SEMI_FURNISHED','UNFURNISHED'])->nullable();
            $table->integer('min_stay_months')->default(1);
            $table->integer('deposit_months')->default(1);

            // Pricing
            $table->decimal('base_price', 10, 2);
            $table->decimal('commission_rate', 5, 2)->default(5.00);
            $table->decimal('total_price', 10, 2)->storedAs('(base_price + (base_price * commission_rate / 100))');

            $table->timestamps();

            // Indexes
            $table->index(['latitude','longitude'], 'idx_location');
            $table->index('status', 'idx_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
