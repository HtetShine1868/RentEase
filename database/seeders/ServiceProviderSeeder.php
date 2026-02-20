<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ServiceProviderSeeder extends Seeder
{
    public function run(): void
    {
        // Get laundry user ID
        $laundryUserId = DB::table('users')->where('email', 'laundry@example.com')->value('id');

        if (!$laundryUserId) {
            $this->command->error('Laundry user not found! Please run UserRoleSeeder first.');
            return;
        }

        // Insert into service_providers table (ONLY LAUNDRY)
        DB::table('service_providers')->insert([
            [
                'user_id' => $laundryUserId,
                'service_type' => 'LAUNDRY',
                'business_name' => 'Fresh & Clean Laundry',
                'description' => 'Professional laundry service with quick turnaround. We offer both normal and rush services.',
                'contact_email' => 'laundry@example.com',
                'contact_phone' => '9876543210',
                'address' => '456 Laundry Avenue, Dhaka',
                'city' => 'Dhaka',
                'latitude' => 23.8041,
                'longitude' => 90.4152,
                'service_radius_km' => 8.00,
                'status' => 'ACTIVE',
                'rating' => 4.8,
                'total_orders' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Insert into laundry_service_configs
        DB::table('laundry_service_configs')->insert([
            [
                'service_provider_id' => DB::table('service_providers')
                    ->where('user_id', $laundryUserId)
                    ->value('id'),
                'normal_turnaround_hours' => 120, // 5 days
                'rush_turnaround_hours' => 48,     // 2 days
                'pickup_start_time' => '09:00:00',
                'pickup_end_time' => '18:00:00',
                'provides_pickup_service' => true,
                'pickup_fee' => 50.00,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        $this->command->info('✅ Laundry service provider created successfully!');
    }
}