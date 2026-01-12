<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('food_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('food_order_id')->constrained('food_orders')->onDelete('cascade');
            $table->foreignId('food_item_id')->constrained('food_items')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price',10,2);
            $table->decimal('total_price',10,2)->storedAs('quantity * unit_price');
            $table->text('special_instructions')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_order_items');
    }
};
