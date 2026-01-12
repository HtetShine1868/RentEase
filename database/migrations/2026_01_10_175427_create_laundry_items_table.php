<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('laundry_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_provider_id')->constrained('service_providers')->onDelete('cascade');
            $table->string('item_name',100);
            $table->enum('item_type',['CLOTHING','BEDDING','CURTAIN','OTHER'])->default('CLOTHING');

            // Pricing
            $table->decimal('base_price',10,2);
            $table->decimal('rush_surcharge_percent',5,2)->default(30.00);
            $table->decimal('commission_rate',5,2)->default(10.00);
            $table->decimal('total_price',10,2)->storedAs('base_price + (base_price * commission_rate / 100)');

            $table->timestamps();

            $table->unique(['service_provider_id','item_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laundry_items');
    }
};
