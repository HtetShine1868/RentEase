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
            // Add is_approved column if it doesn't exist
            if (!Schema::hasColumn('service_ratings', 'is_approved')) {
                $table->boolean('is_approved')->default(true)->after('comment');
            }
            
            // Add helpful_count column if it doesn't exist
            if (!Schema::hasColumn('service_ratings', 'helpful_count')) {
                $table->integer('helpful_count')->default(0)->after('is_approved');
            }
            
            // Add response columns if they don't exist
            if (!Schema::hasColumn('service_ratings', 'admin_response')) {
                $table->text('admin_response')->nullable()->after('helpful_count');
            }
            
            if (!Schema::hasColumn('service_ratings', 'responded_at')) {
                $table->timestamp('responded_at')->nullable()->after('admin_response');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_ratings', function (Blueprint $table) {
            $table->dropColumn([
                'is_approved', 
                'helpful_count', 
                'admin_response', 
                'responded_at'
            ]);
        });
    }
};