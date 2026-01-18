<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->string('room_number', 50);
            $table->enum('room_type', ['SINGLE', 'DOUBLE', 'TRIPLE', 'QUAD', 'DORM'])->default('SINGLE');
            $table->integer('floor_number')->nullable();
            $table->integer('capacity')->default(1);
            
            // Pricing
            $table->decimal('base_price', 10, 2);
            $table->decimal('commission_rate', 5, 2)->default(5.00);
            $table->decimal('total_price', 10, 2)->virtualAs('base_price + (base_price * commission_rate / 100)');
            
            // Status
            $table->enum('status', ['AVAILABLE', 'OCCUPIED', 'MAINTENANCE', 'RESERVED'])->default('AVAILABLE');
            
            $table->timestamps();
            
            $table->unique(['property_id', 'room_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};