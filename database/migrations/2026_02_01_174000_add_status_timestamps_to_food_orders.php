<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('food_orders', function (Blueprint $table) {
            // Add status timestamps if they don't exist
            if (!Schema::hasColumn('food_orders', 'accepted_at')) {
                $table->timestamp('accepted_at')->nullable()->after('status');
            }
            
            if (!Schema::hasColumn('food_orders', 'preparing_at')) {
                $table->timestamp('preparing_at')->nullable()->after('accepted_at');
            }
            
            if (!Schema::hasColumn('food_orders', 'out_for_delivery_at')) {
                $table->timestamp('out_for_delivery_at')->nullable()->after('preparing_at');
            }
            
            if (!Schema::hasColumn('food_orders', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable()->after('out_for_delivery_at');
            }
            
            if (!Schema::hasColumn('food_orders', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('delivered_at');
            }
        });

        // Add indexes for better performance
        Schema::table('food_orders', function (Blueprint $table) {
            $table->index('status');
            $table->index(['service_provider_id', 'status']);
            $table->index(['meal_date', 'status']);
        });
    }

    public function down()
    {
        Schema::table('food_orders', function (Blueprint $table) {
            $table->dropColumn(['accepted_at', 'preparing_at', 'out_for_delivery_at', 'delivered_at', 'cancelled_at']);
            $table->dropIndex(['status']);
            $table->dropIndex(['service_provider_id', 'status']);
            $table->dropIndex(['meal_date', 'status']);
        });
    }
};