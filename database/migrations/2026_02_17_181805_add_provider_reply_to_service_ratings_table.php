<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('service_ratings', function (Blueprint $table) {
            $table->text('provider_reply')->nullable()->after('comment');
            $table->timestamp('replied_at')->nullable()->after('provider_reply');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_ratings', function (Blueprint $table) {
            $table->dropColumn(['provider_reply', 'replied_at']);
        });
    }
};