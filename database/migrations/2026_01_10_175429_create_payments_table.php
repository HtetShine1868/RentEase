<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_reference',100)->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->enum('payable_type',['BOOKING','FOOD_ORDER','LAUNDRY_ORDER','FOOD_SUBSCRIPTION']);
            $table->unsignedBigInteger('payable_id');

            // Amount Details
            $table->decimal('amount',10,2);
            $table->decimal('commission_amount',10,2);
            $table->decimal('provider_earning',10,2)->storedAs('amount - commission_amount');

            // Payment Method
            $table->enum('payment_method',['CASH','BANK_TRANSFER','MOBILE_BANKING','CARD'])->default('BANK_TRANSFER');
            $table->string('transaction_id',100)->nullable();

            // Status
            $table->enum('status',['PENDING','COMPLETED','FAILED','REFUNDED'])->default('PENDING');
            $table->dateTime('paid_at')->nullable();

            $table->timestamps();

            $table->index(['user_id','status']);
            $table->index(['payable_type','payable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
