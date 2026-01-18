<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laundry_service_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_provider_id')->constrained()->onDelete('cascade')->unique();
            
            // Turnaround Times (in hours)
            $table->integer('normal_turnaround_hours')->default(120);
            $table->integer('rush_turnaround_hours')->default(48);
            
            // Pickup/Delivery
            $table->time('pickup_start_time')->default('09:00:00');
            $table->time('pickup_end_time')->default('18:00:00');
            $table->boolean('provides_pickup_service')->default(true);
            $table->decimal('pickup_fee', 10, 2)->default(0.00);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laundry_service_configs');
    }
};