<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_configs', function (Blueprint $table) {
            $table->id();
            $table->enum('service_type', ['HOSTEL', 'APARTMENT', 'FOOD', 'LAUNDRY'])->unique();
            $table->decimal('rate', 5, 2)->default(0.00);
            $table->timestamps();
        });

        // Insert default commission rates
        DB::table('commission_configs')->insert([
            ['service_type' => 'HOSTEL', 'rate' => 5.00, 'created_at' => now(), 'updated_at' => now()],
            ['service_type' => 'APARTMENT', 'rate' => 3.00, 'created_at' => now(), 'updated_at' => now()],
            ['service_type' => 'FOOD', 'rate' => 8.00, 'created_at' => now(), 'updated_at' => now()],
            ['service_type' => 'LAUNDRY', 'rate' => 10.00, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_configs');
    }
};