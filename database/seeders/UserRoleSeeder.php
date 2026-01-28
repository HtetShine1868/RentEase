<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Insert users
        DB::table('users')->insert([
            [
                'name' => 'Regular User',
                'email' => 'user@example.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Owner User',
                'email' => 'owner@example.com',
                'password' => Hash::make('password123'),
                 'email_verified_at' => now(), 
            ],
            [
                'name' => 'Food Provider',
                'email' => 'food@example.com',
                'password' => Hash::make('password123'),
                 'email_verified_at' => now(), 
            ],
            [
                'name' => 'Laundry Provider',
                'email' => 'laundry@example.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ],
        ]);

        // Assign roles
        DB::table('user_roles')->insert([
            [
                    'user_id' => DB::table('users')->where('email', 'user@example.com')->value('id'),
                    'role_id' => DB::table('roles')->where('name', 'USER')->value('id'),
            ],
            [
                'user_id' => DB::table('users')->where('email', 'owner@example.com')->value('id'),
                'role_id' => DB::table('roles')->where('name', 'OWNER')->value('id'),
            ],
            [
                'user_id' => DB::table('users')->where('email', 'food@example.com')->value('id'),
                'role_id' => DB::table('roles')->where('name', 'FOOD')->value('id'),
            ],
            [
                'user_id' => DB::table('users')->where('email', 'laundry@example.com')->value('id'),
                'role_id' => DB::table('roles')->where('name', 'LAUNDRY')->value('id'),
            ],
            [
                'user_id' => DB::table('users')->where('email', 'admin@example.com')->value('id'),
                'role_id' => DB::table('roles')->where('name', 'SUPERADMIN')->value('id'),
            ],
        ]);
    }
}
