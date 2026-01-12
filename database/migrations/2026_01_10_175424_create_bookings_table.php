<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_reference', 50)->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->nullable()->constrained()->onDelete('cascade');

            $table->date('check_in');
            $table->date('check_out');
            $table->integer('duration_days')->storedAs('DATEDIFF(check_out, check_in)');

            // Pricing
            $table->decimal('room_price_per_day', 10, 2);
            $table->decimal('total_room_price', 10, 2)->storedAs('room_price_per_day * DATEDIFF(check_out, check_in)');
            $table->decimal('commission_amount', 10, 2);
            $table->decimal('total_amount', 10, 2);

            // Status
            $table->enum('status', ['PENDING','CONFIRMED','CHECKED_IN','CHECKED_OUT','CANCELLED'])->default('PENDING');
            $table->text('cancellation_reason')->nullable();

            $table->timestamps();

            $table->index(['room_id', 'check_in', 'check_out'], 'idx_dates');
            $table->index(['user_id','status'], 'idx_user_bookings');

            // Prevent invalid dates
            //$table->check('check_out > check_in');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
