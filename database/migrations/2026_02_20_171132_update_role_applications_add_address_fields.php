<?php
// database/migrations/xxxx_xx_xx_xxxxxx_update_role_applications_add_address_fields.php

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
        Schema::table('role_applications', function (Blueprint $table) {
            // First, make latitude/longitude nullable since we're replacing them
            $table->decimal('latitude', 10, 7)->nullable()->change();
            $table->decimal('longitude', 10, 7)->nullable()->change();
            
            // Add new address fields
            $table->string('city')->nullable()->after('business_address');
            $table->string('state')->nullable()->after('city');
            $table->string('postal_code', 20)->nullable()->after('state');
            $table->string('country')->default('Bangladesh')->after('postal_code');
            $table->string('area')->nullable()->after('country');
            $table->string('map_link')->nullable()->after('area');
            
            // Add laundry-specific fields
            $table->json('service_types')->nullable()->after('map_link'); // Store selected service types
            $table->json('item_types')->nullable()->after('service_types'); // Store accepted item types
            $table->time('opening_time')->nullable()->after('item_types');
            $table->time('closing_time')->nullable()->after('opening_time');
            $table->integer('daily_capacity')->nullable()->after('closing_time'); // in kg
            $table->integer('turnaround_hours')->nullable()->after('daily_capacity');
            $table->boolean('rush_service_available')->default(false)->after('turnaround_hours');
            $table->integer('rush_turnaround_hours')->nullable()->after('rush_service_available');
            $table->boolean('provides_pickup')->default(true)->after('rush_turnaround_hours');
            $table->decimal('pickup_fee', 10, 2)->nullable()->after('provides_pickup');
            $table->string('alt_phone', 20)->nullable()->after('contact_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('role_applications', function (Blueprint $table) {
            $table->dropColumn([
                'city',
                'state',
                'postal_code',
                'country',
                'area',
                'map_link',
                'service_types',
                'item_types',
                'opening_time',
                'closing_time',
                'daily_capacity',
                'turnaround_hours',
                'rush_service_available',
                'rush_turnaround_hours',
                'provides_pickup',
                'pickup_fee',
                'alt_phone'
            ]);
            
            // Make latitude/longitude required again if needed
            $table->decimal('latitude', 10, 7)->nullable(false)->change();
            $table->decimal('longitude', 10, 7)->nullable(false)->change();
        });
    }
};