<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->enum('name', ['USER', 'OWNER', 'LAUNDRY', 'FOOD', 'SUPERADMIN'])->unique();
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        // Insert default roles
        DB::table('roles')->insert([
            ['name' => 'USER', 'description' => 'Regular user who can book properties and order services'],
            ['name' => 'OWNER', 'description' => 'Property owner who can list and manage properties'],
            ['name' => 'LAUNDRY', 'description' => 'Laundry service provider'],
            ['name' => 'FOOD', 'description' => 'Food service provider'],
            ['name' => 'SUPERADMIN', 'description' => 'System administrator with full access'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};