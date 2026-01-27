<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('verification_code')->nullable()->after('email');
            $table->timestamp('verification_code_sent_at')->nullable()->after('verification_code');
            $table->timestamp('email_verified_at')->nullable()->after('verification_code_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['verification_code', 'verification_code_sent_at', 'email_verified_at']);
        });
    }
};