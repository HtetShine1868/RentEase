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
        Schema::table('laundry_items', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('laundry_items', 'description')) {
                $table->text('description')->nullable()->after('item_type');
            }
            
            if (!Schema::hasColumn('laundry_items', 'rush_surcharge_percent')) {
                $table->decimal('rush_surcharge_percent', 5, 2)->default(30.00)->after('base_price');
            }
            
            if (!Schema::hasColumn('laundry_items', 'commission_rate')) {
                $table->decimal('commission_rate', 5, 2)->default(10.00)->after('rush_surcharge_percent');
            }
            
            if (!Schema::hasColumn('laundry_items', 'total_price')) {
                $table->decimal('total_price', 10, 2)->nullable()->after('commission_rate');
            }
            
            if (!Schema::hasColumn('laundry_items', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('total_price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laundry_items', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'rush_surcharge_percent',
                'commission_rate',
                'total_price',
                'is_active'
            ]);
        });
    }
};