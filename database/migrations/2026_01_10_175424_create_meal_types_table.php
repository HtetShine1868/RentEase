<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });

        // Insert default meal types
        DB::table('meal_types')->insert([
            ['name' => 'Breakfast', 'display_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lunch', 'display_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dinner', 'display_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Snacks', 'display_order' => 4, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_types');
    }
};