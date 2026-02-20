<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find or create the SUPERADMIN role
        $adminRole = Role::firstOrCreate(
            ['name' => 'SUPERADMIN'],
            ['description' => 'System administrator with full access']
        );

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@rentease.com'],
            [
                'name' => 'Super Admin',
                'phone' => '1234567890',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'status' => 'ACTIVE',
                'gender' => 'MALE',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Assign SUPERADMIN role to the admin user
        $admin->roles()->syncWithoutDetaching([$adminRole->id]);

        // Create a second admin (optional)
        $admin2 = User::firstOrCreate(
            ['email' => 'admin2@rentease.com'],
            [
                'name' => 'Secondary Admin',
                'phone' => '0987654321',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'status' => 'ACTIVE',
                'gender' => 'FEMALE',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $admin2->roles()->syncWithoutDetaching([$adminRole->id]);

        $this->command->info('Admin users seeded successfully!');
        $this->command->info('Email: admin@rentease.com');
        $this->command->info('Password: password123');
    }
}