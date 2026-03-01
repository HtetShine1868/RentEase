<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\ServiceProvider;
use App\Models\FoodServiceConfig;
use App\Models\LaundryServiceConfig;
use App\Models\MealType;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserWithRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure roles exist
        $this->ensureRoles();

        // Create users with different roles
        $this->createLaundryProviders();
        $this->createFoodProviders();
        $this->createOwner();
        $this->createSuperAdmin();
        $this->createRegularUsers();
        
        $this->command->info('Users with roles seeded successfully!');
    }

    /**
     * Ensure all required roles exist
     */
    private function ensureRoles()
    {
        $roles = [
            ['name' => 'USER', 'description' => 'Regular user'],
            ['name' => 'OWNER', 'description' => 'Property owner'],
            ['name' => 'LAUNDRY', 'description' => 'Laundry service provider'],
            ['name' => 'FOOD', 'description' => 'Food service provider'],
            ['name' => 'SUPERADMIN', 'description' => 'Super administrator'],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['name' => $roleData['name']],
                ['description' => $roleData['description']]
            );
        }
    }

    /**
     * Create laundry provider users
     */
    private function createLaundryProviders()
    {
        $laundryRole = Role::where('name', 'LAUNDRY')->first();
        $userRole = Role::where('name', 'USER')->first();

        $laundryProviders = [
            [
                'name' => 'Clean & Fresh Laundry',
                'email' => 'cleanfresh@example.com',
                'phone' => '09123456789',
                'business_name' => 'Clean & Fresh Laundry Services',
                'contact_person' => 'Mg Aung',
                'contact_email' => 'contact@cleanfresh.com',
                'contact_phone' => '09234567890',
                'address' => 'No. 123, Hlaing Township, Yangon',
                'city' => 'Yangon',
                'latitude' => 16.8661,
                'longitude' => 96.1951,
                'service_radius_km' => 8.5,
                'description' => 'Professional laundry service with pickup and delivery',
            ],
            [
                'name' => 'Speed Wash Laundry',
                'email' => 'speedwash@example.com',
                'phone' => '09345678901',
                'business_name' => 'Speed Wash Laundry',
                'contact_person' => 'Daw Su Su',
                'contact_email' => 'info@speedwash.com',
                'contact_phone' => '09456789012',
                'address' => 'No. 45, Bahan Township, Yangon',
                'city' => 'Yangon',
                'latitude' => 16.8229,
                'longitude' => 96.1587,
                'service_radius_km' => 10.0,
                'description' => 'Express laundry service with 24-hour turnaround',
            ],
            [
                'name' => 'Mandalay Laundry Center',
                'email' => 'mandalaylaundry@example.com',
                'phone' => '09567890123',
                'business_name' => 'Mandalay Laundry Center',
                'contact_person' => 'U Myint',
                'contact_email' => 'mandalay@laundry.com',
                'contact_phone' => '09678901234',
                'address' => 'No. 78, Chanayethazan, Mandalay',
                'city' => 'Mandalay',
                'latitude' => 21.9588,
                'longitude' => 96.0891,
                'service_radius_km' => 7.0,
                'description' => 'Full-service laundry and dry cleaning',
            ],
        ];

        foreach ($laundryProviders as $index => $provider) {
            // Create user
            $user = User::firstOrCreate(
                ['email' => $provider['email']],
                [
                    'name' => $provider['name'],
                    'password' => Hash::make('password123'),
                    'phone' => $provider['phone'],
                    'status' => 'ACTIVE',
                    'email_verified_at' => now(),
                ]
            );

            // Assign roles
            $user->roles()->syncWithoutDetaching([$laundryRole->id, $userRole->id]);

            // Create service provider record
            $serviceProvider = ServiceProvider::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'service_type' => 'LAUNDRY',
                    'business_name' => $provider['business_name'],
                    'description' => $provider['description'],
                    'contact_email' => $provider['contact_email'],
                    'contact_phone' => $provider['contact_phone'],
                    'address' => $provider['address'],
                    'city' => $provider['city'],
                    'latitude' => $provider['latitude'],
                    'longitude' => $provider['longitude'],
                    'service_radius_km' => $provider['service_radius_km'],
                    'status' => 'ACTIVE',
                    'rating' => rand(35, 50) / 10, // 3.5 to 5.0
                    'total_orders' => rand(50, 500),
                ]
            );

            // Create laundry service config
            LaundryServiceConfig::updateOrCreate(
                ['service_provider_id' => $serviceProvider->id],
                [
                    'normal_turnaround_hours' => 48,
                    'rush_turnaround_hours' => 24,
                    'pickup_start_time' => '09:00:00',
                    'pickup_end_time' => '18:00:00',
                    'provides_pickup_service' => true,
                    'pickup_fee' => 0,
                ]
            );

            $this->command->info("Created laundry provider: {$provider['business_name']}");
        }
    }

    /**
     * Create food provider users
     */
    private function createFoodProviders()
    {
        $foodRole = Role::where('name', 'FOOD')->first();
        $userRole = Role::where('name', 'USER')->first();

        // Get meal types
        $mealTypes = MealType::all();
        if ($mealTypes->isEmpty()) {
            $this->call(MealTypeSeeder::class);
            $mealTypes = MealType::all();
        }

        $foodProviders = [
            [
                'name' => 'Golden Myanmar Restaurant',
                'email' => 'goldenmyanmar@example.com',
                'phone' => '09789012345',
                'business_name' => 'Golden Myanmar Restaurant',
                'contact_person' => 'U Kyaw',
                'contact_email' => 'info@goldenmyanmar.com',
                'contact_phone' => '09890123456',
                'address' => 'No. 56, Shwe Gon Daing, Bahan, Yangon',
                'city' => 'Yangon',
                'latitude' => 16.8231,
                'longitude' => 96.1583,
                'service_radius_km' => 5.0,
                'description' => 'Authentic Myanmar cuisine',
               
                'opening_time' => '08:00:00',
                'closing_time' => '22:00:00',
                'avg_preparation_minutes' => 25,
                'delivery_buffer_minutes' => 10,
                'meal_types' => ['Breakfast', 'Lunch', 'Dinner'],
            ],
            [
                'name' => 'Shwe Pizza House',
                'email' => 'shwepizza@example.com',
                'phone' => '09901234567',
                'business_name' => 'Shwe Pizza House',
                'contact_person' => 'Ko Zaw',
                'contact_email' => 'orders@shwepizza.com',
                'contact_phone' => '09112345678',
                'address' => 'No. 23, Pyay Road, Hlaing, Yangon',
                'city' => 'Yangon',
                'latitude' => 16.8567,
                'longitude' => 96.1234,
                'service_radius_km' => 6.5,
                'description' => 'Delicious pizza and Italian food',
                
                'opening_time' => '10:00:00',
                'closing_time' => '23:00:00',
                'avg_preparation_minutes' => 20,
                'delivery_buffer_minutes' => 15,
                'meal_types' => ['Lunch', 'Dinner', 'Snacks'],
            ],
            [
                'name' => 'Mandalay Food Corner',
                'email' => 'mandalayfood@example.com',
                'phone' => '09223456789',
                'business_name' => 'Mandalay Food Corner',
                'contact_person' => 'Daw Hla',
                'contact_email' => 'mandalay@foodcorner.com',
                'contact_phone' => '09334567890',
                'address' => 'No. 89, 26th Street, Chanayethazan, Mandalay',
                'city' => 'Mandalay',
                'latitude' => 21.9750,
                'longitude' => 96.0833,
                'service_radius_km' => 4.5,
                'description' => 'Traditional Mandalay dishes',
                
                'opening_time' => '07:00:00',
                'closing_time' => '21:00:00',
                'avg_preparation_minutes' => 20,
                'delivery_buffer_minutes' => 12,
                'meal_types' => ['Breakfast', 'Lunch', 'Dinner'],
            ],
            [
                'name' => 'Green Leaf Healthy Food',
                'email' => 'greenleaf@example.com',
                'phone' => '09445678901',
                'business_name' => 'Green Leaf Healthy Food',
                'contact_person' => 'Ma Thida',
                'contact_email' => 'health@greenleaf.com',
                'contact_phone' => '09556789012',
                'address' => 'No. 12, University Avenue, Kamayut, Yangon',
                'city' => 'Yangon',
                'latitude' => 16.8345,
                'longitude' => 96.1357,
                'service_radius_km' => 5.0,
                'description' => 'Healthy, vegetarian and vegan options',
                'cuisine_type' => 'Healthy, Vegetarian',
                'opening_time' => '09:00:00',
                'closing_time' => '20:00:00',
                'avg_preparation_minutes' => 15,
                'delivery_buffer_minutes' => 10,
                'meal_types' => ['Breakfast', 'Lunch', 'Snacks'],
            ],
        ];

        foreach ($foodProviders as $index => $provider) {
            // Create user
            $user = User::firstOrCreate(
                ['email' => $provider['email']],
                [
                    'name' => $provider['name'],
                    'password' => Hash::make('password123'),
                    'phone' => $provider['phone'],
                    'status' => 'ACTIVE',
                    'email_verified_at' => now(),
                ]
            );

            // Assign roles
            $user->roles()->syncWithoutDetaching([$foodRole->id, $userRole->id]);

            // Create service provider record
            $serviceProvider = ServiceProvider::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'service_type' => 'FOOD',
                    'business_name' => $provider['business_name'],
                    'description' => $provider['description'],
                    'contact_email' => $provider['contact_email'],
                    'contact_phone' => $provider['contact_phone'],
                    'address' => $provider['address'],
                    'city' => $provider['city'],
                    'latitude' => $provider['latitude'],
                    'longitude' => $provider['longitude'],
                    'service_radius_km' => $provider['service_radius_km'],
                    'status' => 'ACTIVE',
                    'rating' => rand(40, 50) / 10, // 4.0 to 5.0
                    'total_orders' => rand(100, 1000),
                ]
            );

            // Create food service config
            $foodConfig = FoodServiceConfig::updateOrCreate(
                ['service_provider_id' => $serviceProvider->id],
                [
                    'supports_subscription' => rand(0, 1),
                    'supports_pay_per_eat' => true,
                    'opening_time' => $provider['opening_time'],
                    'closing_time' => $provider['closing_time'],
                    'avg_preparation_minutes' => $provider['avg_preparation_minutes'],
                    'delivery_buffer_minutes' => $provider['delivery_buffer_minutes'],
                    'subscription_discount_percent' => 10,
                 
                ]
            );

            // Attach meal types
            $mealTypeIds = [];
            foreach ($provider['meal_types'] as $mealTypeName) {
                $mealType = MealType::where('name', $mealTypeName)->first();
                if ($mealType) {
                    $mealTypeIds[] = $mealType->id;
                }
            }
            
            if (!empty($mealTypeIds)) {
                $foodConfig->mealTypes()->sync($mealTypeIds);
            }

            $this->command->info("Created food provider: {$provider['business_name']}");
        }
    }

    /**
     * Create property owner users
     */
    private function createOwner()
    {
        $ownerRole = Role::where('name', 'OWNER')->first();
        $userRole = Role::where('name', 'USER')->first();

        $owners = [
            [
                'name' => 'Property Owner 1',
                'email' => 'owner1@example.com',
                'phone' => '09111222333',
                'business_name' => 'Golden Properties',
                'status' => 'ACTIVE',
            ],
            [
                'name' => 'Property Owner 2',
                'email' => 'owner2@example.com',
                'phone' => '09222333444',
                'business_name' => 'City Real Estate',
                'status' => 'ACTIVE',
            ],
        ];

        foreach ($owners as $ownerData) {
            $user = User::firstOrCreate(
                ['email' => $ownerData['email']],
                [
                    'name' => $ownerData['name'],
                    'password' => Hash::make('password123'),
                    'phone' => $ownerData['phone'],
                    'status' => $ownerData['status'],
                    'email_verified_at' => now(),
                ]
            );

            $user->roles()->syncWithoutDetaching([$ownerRole->id, $userRole->id]);
            
            $this->command->info("Created owner: {$ownerData['name']}");
        }
    }

    /**
     * Create super admin user
     */
    private function createSuperAdmin()
    {
        $superAdminRole = Role::where('name', 'SUPERADMIN')->first();
        $userRole = Role::where('name', 'USER')->first();

        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin123'),
                'phone' => '09000000000',
                'status' => 'ACTIVE',
                'email_verified_at' => now(),
            ]
        );

        $superAdmin->roles()->syncWithoutDetaching([$superAdminRole->id, $userRole->id]);
        
        $this->command->info('Created super admin: admin@example.com / admin123');
    }

    /**
     * Create regular users
     */
    private function createRegularUsers()
    {
        $userRole = Role::where('name', 'USER')->first();

        $regularUsers = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '09444555666',
                'gender' => 'MALE',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'phone' => '09555666777',
                'gender' => 'FEMALE',
            ],
            [
                'name' => 'Bob Johnson',
                'email' => 'bob@example.com',
                'phone' => '09666777888',
                'gender' => 'MALE',
            ],
            [
                'name' => 'Alice Brown',
                'email' => 'alice@example.com',
                'phone' => '09777888999',
                'gender' => 'FEMALE',
            ],
        ];

        foreach ($regularUsers as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password123'),
                    'phone' => $userData['phone'],
                    'gender' => $userData['gender'],
                    'status' => 'ACTIVE',
                    'email_verified_at' => now(),
                ]
            );

            $user->roles()->syncWithoutDetaching([$userRole->id]);
            
            $this->command->info("Created regular user: {$userData['name']}");
        }
    }
}