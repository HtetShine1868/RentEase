<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_conversation_id')
                  ->constrained('complaint_conversations')
                  ->onDelete('cascade');
            $table->foreignId('complaint_id')
                  ->constrained('complaints')
                  ->onDelete('cascade'); // Direct reference for easier querying
            $table->string('original_name');
            $table->string('path');
            $table->string('mime_type');
            $table->unsignedInteger('size')->default(0);
            $table->string('disk')->default('public');
            $table->timestamps();

            $table->index(['complaint_conversation_id']);
            $table->index(['complaint_id']);
            $table->index(['mime_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_attachments');
    }
};