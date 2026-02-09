<?php

namespace Database\Seeders;

use App\Models\FoodOrder;
use App\Models\FoodOrderItem;
use App\Models\FoodItem;
use App\Models\MealType;
use App\Models\ServiceProvider;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FoodOrdersSeeder extends Seeder
{
    public function run()
    {
        // Clear existing data
        DB::table('food_order_items')->delete();
        DB::table('food_orders')->delete();

        // Get food service provider
        $foodProvider = ServiceProvider::where('service_type', 'FOOD')->first();

        if (!$foodProvider) {
            // Create a food provider if none exists
            $foodUser = User::factory()->create([
                'name' => 'Food Provider',
                'email' => 'food@example.com',
                'password' => bcrypt('password')
            ]);

            $foodProvider = ServiceProvider::create([
                'user_id' => $foodUser->id,
                'service_type' => 'FOOD',
                'business_name' => 'Delicious Foods',
                'contact_email' => 'food@example.com',
                'contact_phone' => '+8801712345678',
                'address' => '123 Food Street, Dhaka',
                'city' => 'Dhaka',
                'latitude' => 23.8103,
                'longitude' => 90.4125,
                'service_radius_km' => 10.00
            ]);
        }

        // Get or create customers
        $customers = User::factory()->count(5)->create();

        // Get or create meal types
        $mealTypes = MealType::all();
        if ($mealTypes->isEmpty()) {
            $mealTypes = MealType::insert([
                ['name' => 'Breakfast', 'display_order' => 1],
                ['name' => 'Lunch', 'display_order' => 2],
                ['name' => 'Dinner', 'display_order' => 3],
                ['name' => 'Snacks', 'display_order' => 4],
            ]);
            $mealTypes = MealType::all();
        }

        // Create food items
        $foodItems = [
            [
                'service_provider_id' => $foodProvider->id,
                'name' => 'Butter Chicken',
                'description' => 'Rich creamy curry with tandoori chicken',
                'meal_type_id' => $mealTypes->where('name', 'Lunch')->first()->id,
                'base_price' => 320,
                'commission_rate' => 8.00,
                'is_available' => true,
                'daily_quantity' => 50,
                'dietary_tags' => json_encode(['non-vegetarian']),
                'calories' => 450,
            ],
            // Add more food items...
        ];

        foreach ($foodItems as $item) {
            FoodItem::create($item);
        }

        $foodItems = FoodItem::where('service_provider_id', $foodProvider->id)->get();

        // Create orders
        $statuses = ['PENDING', 'ACCEPTED', 'PREPARING', 'OUT_FOR_DELIVERY', 'DELIVERED', 'CANCELLED'];
        $orderTypes = ['PAY_PER_EAT', 'SUBSCRIPTION_MEAL'];

        foreach ($customers as $customer) {
            for ($i = 1; $i <= 3; $i++) {
                $orderDate = now()->subDays(rand(1, 30));
                $status = $statuses[array_rand($statuses)];
                
                $order = FoodOrder::create([
                    'order_reference' => 'FOOD-' . date('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'user_id' => $customer->id,
                    'service_provider_id' => $foodProvider->id,
                    'order_type' => $orderTypes[array_rand($orderTypes)],
                    'meal_date' => $orderDate->format('Y-m-d'),
                    'meal_type_id' => $mealTypes->random()->id,
                    'delivery_address' => 'Customer Address ' . $i,
                    'delivery_latitude' => 23.8103 + (rand(-50, 50) / 1000),
                    'delivery_longitude' => 90.4125 + (rand(-50, 50) / 1000),
                    'distance_km' => rand(1, 10) / 2,
                    'status' => $status,
                    'estimated_delivery_time' => $orderDate->copy()->addMinutes(rand(30, 90)),
                    'actual_delivery_time' => $status == 'DELIVERED' ? 
                        $orderDate->copy()->addMinutes(rand(30, 90)) : null,
                    'base_amount' => 0, // Will update
                    'delivery_fee' => rand(30, 100),
                    'commission_amount' => 0, // Will update
                    'total_amount' => 0, // Will update
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                    // Set status timestamps based on status
                    'accepted_at' => in_array($status, ['ACCEPTED', 'PREPARING', 'OUT_FOR_DELIVERY', 'DELIVERED']) ? 
                        $orderDate->copy()->addMinutes(5) : null,
                    'preparing_at' => in_array($status, ['PREPARING', 'OUT_FOR_DELIVERY', 'DELIVERED']) ? 
                        $orderDate->copy()->addMinutes(15) : null,
                    'out_for_delivery_at' => in_array($status, ['OUT_FOR_DELIVERY', 'DELIVERED']) ? 
                        $orderDate->copy()->addMinutes(30) : null,
                    'delivered_at' => $status == 'DELIVERED' ? 
                        $orderDate->copy()->addMinutes(45) : null,
                ]);

                // Add order items
                $itemCount = rand(1, 3);
                $baseAmount = 0;
                
                for ($j = 0; $j < $itemCount; $j++) {
                    $foodItem = $foodItems->random();
                    $quantity = rand(1, 3);
                    $itemTotal = $foodItem->base_price * $quantity;
                    $baseAmount += $itemTotal;
                    
                    FoodOrderItem::create([
                        'food_order_id' => $order->id,
                        'food_item_id' => $foodItem->id,
                        'quantity' => $quantity,
                        'unit_price' => $foodItem->base_price,
                        'special_instructions' => rand(0, 1) ? 'Make it less spicy' : null,
                    ]);
                }

                // Calculate totals
                $commissionAmount = $baseAmount * ($foodItems->first()->commission_rate / 100);
                $totalAmount = $baseAmount + $order->delivery_fee + $commissionAmount;

                // Update order with calculated amounts
                $order->update([
                    'base_amount' => $baseAmount,
                    'commission_amount' => $commissionAmount,
                    'total_amount' => $totalAmount,
                ]);
            }
        }

        $this->command->info('Successfully seeded food orders!');
    }
}