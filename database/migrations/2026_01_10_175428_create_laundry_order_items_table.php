<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('laundry_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laundry_order_id')->constrained('laundry_orders')->onDelete('cascade');
            $table->foreignId('laundry_item_id')->constrained('laundry_items')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price',10,2);
            $table->decimal('total_price',10,2)->storedAs('quantity * unit_price');
            $table->text('special_instructions')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laundry_order_items');
    }
};
