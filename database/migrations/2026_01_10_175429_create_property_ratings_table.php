<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->constrained()->onDelete('cascade')->unique();
            
            // Ratings (1-5)
            $table->integer('cleanliness_rating')->nullable();
            $table->integer('location_rating')->nullable();
            $table->integer('value_rating')->nullable();
            $table->integer('service_rating')->nullable();
            $table->decimal('overall_rating', 3, 2);
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(true);
            
            $table->timestamps();
            
            // Check constraints will be added via validation
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_ratings');
    }
};