<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_provider_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('order_id');
            $table->enum('order_type', ['FOOD', 'LAUNDRY']);
            
            // Ratings (1-5)
            $table->integer('quality_rating')->nullable();
            $table->integer('delivery_rating')->nullable();
            $table->integer('value_rating')->nullable();
            $table->decimal('overall_rating', 3, 2);
            $table->text('comment')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_ratings');
    }
};