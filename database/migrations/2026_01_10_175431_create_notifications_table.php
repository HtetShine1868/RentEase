<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->enum('type',['BOOKING','ORDER','PAYMENT','COMPLAINT','SYSTEM','MARKETING']);
            $table->string('title',200);
            $table->text('message');

            $table->string('related_entity_type',50)->nullable();
            $table->unsignedBigInteger('related_entity_id')->nullable();

            $table->enum('channel',['IN_APP','EMAIL','SMS','PUSH'])->default('IN_APP');
            $table->boolean('is_read')->default(false);
            $table->boolean('is_sent')->default(false);
            $table->dateTime('read_at')->nullable();
            $table->dateTime('sent_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
