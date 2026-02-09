<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')
                  ->constrained('complaints')
                  ->onDelete('cascade');
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->text('message');
            $table->enum('type', ['REPLY', 'STATUS_UPDATE', 'ASSIGNMENT', 'NOTE'])->default('REPLY');
            $table->json('attachments')->nullable();
            $table->timestamps();
            $table->boolean('is_read')->default(false);
            $table->index(['complaint_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_conversations');
    }
};