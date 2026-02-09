<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')
                  ->constrained('complaints')
                  ->onDelete('cascade');
            $table->foreignId('changed_by')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->enum('old_status', ['OPEN', 'IN_PROGRESS', 'RESOLVED', 'CLOSED', 'PENDING'])->nullable();
            $table->enum('new_status', ['OPEN', 'IN_PROGRESS', 'RESOLVED', 'CLOSED', 'PENDING']);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['complaint_id', 'created_at']);
            $table->index(['changed_by', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_status_history');
    }
};