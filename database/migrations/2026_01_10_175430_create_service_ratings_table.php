<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
Schema::create('service_ratings', function (Blueprint $table) {
    $table->id();

    // Polymorphic keys
    $table->morphs('rateable');
    /*
        This creates:
        - rateable_id BIGINT
        - rateable_type VARCHAR
    */

    $table->foreignId('user_id')
          ->constrained()
          ->cascadeOnDelete();

    $table->tinyInteger('rating')->checkBetween(1, 5);
    $table->text('comment')->nullable();

    $table->timestamps();
});

    }

    public function down(): void
    {
        Schema::dropIfExists('service_ratings');
    }
};
