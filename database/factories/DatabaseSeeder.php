<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Property;
use App\Models\ServiceProvider;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $roles = [
            ['name' => 'USER', 'description' => 'Regular user'],
            ['name' => 'OWNER', 'description' => 'Property owner'],
            ['name' => 'FOOD', 'description' => 'Food provider'],
            ['name' => 'LAUNDRY', 'description' => 'Laundry provider'],
            ['name' => 'SUPERADMIN', 'description' => 'System admin'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        // Create superadmin
        $superadmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@rms.com',
            'phone' => '+8801700000000',
        ]);
        $superadmin->assignRole('SUPERADMIN');

        // Create 10 regular users
        $users = User::factory(10)->create();
        foreach ($users as $user) {
            $user->assignRole('USER');
        }

        // Create 5 property owners
        $owners = User::factory(5)->create();
        foreach ($owners as $owner) {
            $owner->assignRole('OWNER');
            
            // Create properties for each owner
            Property::factory(rand(1, 3))->create([
                'owner_id' => $owner->id,
            ]);
        }

        // Create 3 food providers
        $foodProviders = User::factory(3)->create();
        foreach ($foodProviders as $provider) {
            $provider->assignRole('FOOD');
            
            ServiceProvider::factory()->create([
                'user_id' => $provider->id,
                'service_type' => 'FOOD',
                'business_name' => $this->faker->company() . ' Restaurant',
            ]);
        }

        // Create 2 laundry providers
        $laundryProviders = User::factory(2)->create();
        foreach ($laundryProviders as $provider) {
            $provider->assignRole('LAUNDRY');
            
            ServiceProvider::factory()->create([
                'user_id' => $provider->id,
                'service_type' => 'LAUNDRY',
                'business_name' => $this->faker->company() . ' Laundry',
            ]);
        }

        $this->call(CommissionConfigSeeder::class);
        $this->call(MealTypeSeeder::class);
    }
}