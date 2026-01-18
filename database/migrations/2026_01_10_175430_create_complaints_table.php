<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('complaint_reference', 50)->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // What is being complained about
            $table->enum('complaint_type', ['PROPERTY', 'FOOD_SERVICE', 'LAUNDRY_SERVICE', 'USER', 'SYSTEM']);
            $table->unsignedBigInteger('related_id');
            $table->enum('related_type', ['PROPERTY', 'SERVICE_PROVIDER', 'USER']);
            
            // Complaint Details
            $table->string('title', 200);
            $table->text('description');
            $table->enum('priority', ['LOW', 'MEDIUM', 'HIGH', 'URGENT'])->default('MEDIUM');
            $table->enum('status', ['OPEN', 'IN_PROGRESS', 'RESOLVED', 'CLOSED'])->default('OPEN');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('resolution')->nullable();
            $table->timestamp('resolved_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['status', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};