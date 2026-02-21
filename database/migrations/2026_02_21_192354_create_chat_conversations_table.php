<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id')->nullable();
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->timestamps();
            
            $table->foreign('property_id')
                  ->references('id')
                  ->on('properties')
                  ->onDelete('set null');
                  
            $table->foreign('booking_id')
                  ->references('id')
                  ->on('bookings')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_conversations');
    }
};