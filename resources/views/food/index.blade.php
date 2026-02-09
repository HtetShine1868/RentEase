@extends('dashboard')

@section('title', 'Food Services')

@section('content')
<div class="space-y-6">
    <!-- Header with Stats -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Food Services</h2>
                <p class="mt-2 text-gray-600">Order meals or subscribe for regular delivery</p>
            </div>
            <div class="flex space-x-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-indigo-600">{{ $totalOrders ?? 0 }}</div>
                    <div class="text-sm text-gray-500">Total Orders</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $activeSubscriptions ?? 0 }}</div>
                    <div class="text-sm text-gray-500">Active Subscriptions</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-600">{{ $pendingOrders ?? 0 }}</div>
                    <div class="text-sm text-gray-500">Pending</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button @click="activeTab = 'restaurants'; loadRestaurants();" 
                        :class="activeTab === 'restaurants' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Browse Restaurants
                </button>
                <button @click="activeTab = 'orders'; loadOrders();" 
                        :class="activeTab === 'orders' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    My Orders
                </button>
                <button @click="activeTab = 'subscriptions'; loadSubscriptions();" 
                        :class="activeTab === 'subscriptions' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    My Subscriptions
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div x-data="foodServices()" x-init="init()" class="p-6">
            <!-- Restaurants Tab -->
            <div x-show="activeTab === 'restaurants'" x-cloak>
                <!-- Search and Filters -->
                <div class="mb-6">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <div class="relative">
                                <input type="text" 
                                       x-model="searchQuery"
                                       @input.debounce.500ms="searchRestaurants"
                                       placeholder="Search restaurants or dishes..." 
                                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <select x-model="selectedMealType" @change="filterRestaurants" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                                <option value="">All Meal Types</option>
                                @foreach($mealTypes as $mealType)
                                <option value="{{ $mealType->id }}">{{ $mealType->name }}</option>
                                @endforeach
                            </select>
                            <select x-model="sortBy" @change="sortRestaurants" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                                <option value="rating">Rating</option>
                                <option value="distance">Distance</option>
                                <option value="delivery_time">Delivery Time</option>
                                <option value="total_orders">Most Orders</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Restaurant Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Initial restaurants loaded from server -->
                    @foreach($initialRestaurants as $restaurant)
                    <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="h-48 bg-gray-100 flex items-center justify-center">
                            <i class="fas fa-utensils text-gray-300 text-4xl"></i>
                        </div>
                        
                        <div class="p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-bold text-lg text-gray-900">{{ $restaurant->business_name }}</h3>
                                    <div class="flex items-center mt-1">
                                        <div class="flex text-yellow-400">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= floor($restaurant->rating ?? 0))
                                                <i class="fas fa-star"></i>
                                                @elseif($i - 0.5 <= ($restaurant->rating ?? 0))
                                                <i class="fas fa-star-half-alt"></i>
                                                @else
                                                <i class="far fa-star"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="ml-2 text-gray-600">{{ number_format($restaurant->rating ?? 0, 1) }}</span>
                                        <span class="ml-2 text-gray-400">({{ $restaurant->total_orders ?? 0 }} orders)</span>
                                    </div>
                                    <p class="mt-2 text-gray-600 text-sm">{{ Str::limit($restaurant->description ?? 'No description', 100) }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-clock mr-1"></i>
                                        <span>~45 min</span>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <span class="text-gray-900 font-semibold">5.0 km away</span>
                                        <p class="text-sm text-gray-500">{{ $restaurant->city ?? 'Dhaka' }}</p>
                                    </div>
                                    <button @click="viewRestaurant({{ $restaurant->id }})"
                                            class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                                        View Menu
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    
                    <!-- Dynamic restaurants loaded via AJAX -->
                    <template x-for="restaurant in restaurants" :key="restaurant.id">
                        <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="h-48 bg-gray-100 flex items-center justify-center">
                                <i class="fas fa-utensils text-gray-300 text-4xl"></i>
                            </div>
                            
                            <div class="p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-bold text-lg text-gray-900" x-text="restaurant.business_name"></h3>
                                        <div class="flex items-center mt-1">
                                            <div class="flex text-yellow-400">
                                                <template x-for="i in 5" :key="i">
                                                    <i :class="i <= Math.floor(restaurant.rating) ? 'fas fa-star' : (i - 0.5 <= restaurant.rating ? 'fas fa-star-half-alt' : 'far fa-star')"></i>
                                                </template>
                                            </div>
                                            <span class="ml-2 text-gray-600" x-text="restaurant.rating.toFixed(1)"></span>
                                            <span class="ml-2 text-gray-400">(<span x-text="restaurant.total_orders"></span> orders)</span>
                                        </div>
                                        <p class="mt-2 text-gray-600 text-sm" x-text="restaurant.description ? restaurant.description.substring(0, 100) + '...' : 'No description available'"></p>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-clock mr-1"></i>
                                            <span x-text="`~${restaurant.estimated_delivery_minutes || 45} min`"></span>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <span class="text-gray-900 font-semibold" x-text="`${restaurant.distance_km || 5.0} km away`"></span>
                                            <p class="text-sm text-gray-500" x-text="restaurant.city"></p>
                                        </div>
                                        <button @click="viewRestaurant(restaurant.id)"
                                                class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                                            View Menu
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Empty State -->
                <div x-show="restaurants.length === 0 && !hasInitialRestaurants" x-cloak class="text-center py-12">
                    <i class="fas fa-search text-gray-300 text-5xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900">No restaurants found</h3>
                    <p class="mt-2 text-gray-500">Try adjusting your search or filters</p>
                </div>
            </div>

            <!-- Orders Tab -->
            <div x-show="activeTab === 'orders'" x-cloak>
                <div class="mb-6">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Orders</h3>
                        <select x-model="orderStatusFilter" @change="loadOrders" class="border border-gray-300 rounded-lg px-3 py-2">
                            <option value="">All Status</option>
                            <option value="PENDING">Pending</option>
                            <option value="ACCEPTED">Accepted</option>
                            <option value="PREPARING">Preparing</option>
                            <option value="OUT_FOR_DELIVERY">Out for Delivery</option>
                            <option value="DELIVERED">Delivered</option>
                            <option value="CANCELLED">Cancelled</option>
                        </select>
                    </div>
                </div>

                <!-- Initial orders from server -->
                @if(count($recentOrders) > 0)
                @foreach($recentOrders as $order)
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 mb-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="flex items-center">
                                <span class="font-semibold text-gray-900">{{ $order->order_reference }}</span>
                                <span class="{{ 
                                    $order->status === 'PENDING' ? 'bg-yellow-100 text-yellow-800' :
                                    ($order->status === 'ACCEPTED' ? 'bg-blue-100 text-blue-800' :
                                    ($order->status === 'PREPARING' ? 'bg-purple-100 text-purple-800' :
                                    ($order->status === 'OUT_FOR_DELIVERY' ? 'bg-indigo-100 text-indigo-800' :
                                    ($order->status === 'DELIVERED' ? 'bg-green-100 text-green-800' :
                                    ($order->status === 'CANCELLED' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')))))
                                }} ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                    <span>{{ str_replace('_', ' ', $order->status) }}</span>
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">
                                <span>{{ $order->business_name }}</span> • 
                                <span>{{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}</span> • 
                                <span>{{ $order->meal_type }}</span>
                            </p>
                            <p class="text-sm text-gray-500 mt-1">{{ $order->delivery_address ?? 'Address not specified' }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-gray-900">৳{{ number_format($order->total_amount, 2) }}</div>
                            <div class="text-sm text-gray-500">{{ $order->created_at_formatted }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
                @endif

                <!-- Dynamic orders loaded via AJAX -->
                <div class="space-y-4">
                    <template x-for="order in orders" :key="order.id">
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="flex items-center">
                                        <span class="font-semibold text-gray-900" x-text="order.order_reference"></span>
                                        <span :class="getStatusBadgeClass(order.status)" 
                                              class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                            <span x-text="order.status.replace('_', ' ')"></span>
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <span x-text="order.business_name"></span> • 
                                        <span x-text="new Date(order.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })"></span> • 
                                        <span x-text="order.meal_type"></span>
                                    </p>
                                    <p class="text-sm text-gray-500 mt-1" x-text="order.delivery_address"></p>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold text-gray-900" x-text="`৳${order.total_amount}`"></div>
                                    <div class="text-sm text-gray-500" x-text="order.created_at_formatted"></div>
                                </div>
                            </div>
                            
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <template x-for="item in order.items" :key="item.id">
                                            <span class="text-sm text-gray-600">
                                                <span x-text="item.name"></span> × <span x-text="item.quantity"></span>
                                                <span class="text-gray-400 mx-2">•</span>
                                            </span>
                                        </template>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button @click="viewOrderDetails(order.id)"
                                                class="text-indigo-600 hover:text-indigo-900 px-3 py-1 text-sm font-medium">
                                            Details
                                        </button>
                                        <button x-show="order.status === 'PENDING' || order.status === 'ACCEPTED'"
                                                @click="cancelOrder(order.id)"
                                                class="text-red-600 hover:text-red-900 px-3 py-1 text-sm font-medium">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Empty State -->
                <div x-show="orders.length === 0 && {{ count($recentOrders) }} === 0" x-cloak class="text-center py-12">
                    <i class="fas fa-shopping-bag text-gray-300 text-5xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900">No orders yet</h3>
                    <p class="mt-2 text-gray-500">Your food orders will appear here</p>
                </div>
            </div>

            <!-- Subscriptions Tab -->
            <div x-show="activeTab === 'subscriptions'" x-cloak>
                <div class="mb-6">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">My Subscriptions</h3>
                        <button @click="showNewSubscriptionModal = true"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>New Subscription
                        </button>
                    </div>
                </div>

                <!-- Initial subscriptions from server -->
                @if(count($subscriptions) > 0)
                @foreach($subscriptions as $subscription)
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 mb-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="flex items-center">
                                <span class="font-semibold text-gray-900">{{ $subscription->business_name }}</span>
                                <span class="{{ 
                                    $subscription->status === 'ACTIVE' ? 'bg-green-100 text-green-800' :
                                    ($subscription->status === 'PAUSED' ? 'bg-yellow-100 text-yellow-800' :
                                    ($subscription->status === 'CANCELLED' ? 'bg-red-100 text-red-800' :
                                    ($subscription->status === 'COMPLETED' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')))
                                }} ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                    <span>{{ $subscription->status }}</span>
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">
                                <span>{{ $subscription->meal_type }}</span> • 
                                <span>{{ \Carbon\Carbon::parse($subscription->delivery_time)->format('h:i A') }}</span> • 
                                <span>{{ $subscription->delivery_days_text }}</span>
                            </p>
                            <p class="text-sm text-gray-500 mt-1">
                                <span>{{ $subscription->start_date_formatted }}</span> to <span>{{ $subscription->end_date_formatted }}</span>
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-gray-900">৳{{ number_format($subscription->daily_price, 2) }}/day</div>
                            <div class="text-sm text-gray-500">Total: ৳{{ number_format($subscription->total_price, 2) }}</div>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="flex justify-between items-center">
                            <div class="text-sm text-gray-600">
                                <span>Discount: ৳{{ number_format($subscription->discount_amount, 2) }}</span>
                            </div>
                            <div class="flex space-x-2">
                                <button class="text-indigo-600 hover:text-indigo-900 px-3 py-1 text-sm font-medium">
                                    Details
                                </button>
                                @if($subscription->status === 'ACTIVE')
                                <button class="text-yellow-600 hover:text-yellow-900 px-3 py-1 text-sm font-medium">
                                    Pause
                                </button>
                                @endif
                                @if(in_array($subscription->status, ['ACTIVE', 'PAUSED']))
                                <button class="text-red-600 hover:text-red-900 px-3 py-1 text-sm font-medium">
                                    Cancel
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                @endif

                <!-- Dynamic subscriptions loaded via AJAX -->
                <div class="space-y-4">
                    <template x-for="subscription in subscriptions" :key="subscription.id">
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="flex items-center">
                                        <span class="font-semibold text-gray-900" x-text="subscription.business_name"></span>
                                        <span :class="getSubscriptionStatusBadgeClass(subscription.status)" 
                                              class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                            <span x-text="subscription.status"></span>
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <span x-text="subscription.meal_type"></span> • 
                                        <span x-text="subscription.delivery_time"></span> • 
                                        <span x-text="subscription.delivery_days_text"></span>
                                    </p>
                                    <p class="text-sm text-gray-500 mt-1">
                                        <span x-text="subscription.start_date_formatted"></span> to <span x-text="subscription.end_date_formatted"></span>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold text-gray-900" x-text="`৳${subscription.daily_price}/day`"></div>
                                    <div class="text-sm text-gray-500" x-text="`Total: ৳${subscription.total_price}`"></div>
                                </div>
                            </div>
                            
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <div class="flex justify-between items-center">
                                    <div class="text-sm text-gray-600">
                                        <span x-text="`Discount: ৳${subscription.discount_amount}`"></span>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button class="text-indigo-600 hover:text-indigo-900 px-3 py-1 text-sm font-medium">
                                            Details
                                        </button>
                                        <button x-show="subscription.status === 'ACTIVE'"
                                                @click="pauseSubscription(subscription.id)"
                                                class="text-yellow-600 hover:text-yellow-900 px-3 py-1 text-sm font-medium">
                                            Pause
                                        </button>
                                        <button x-show="subscription.status === 'ACTIVE' || subscription.status === 'PAUSED'"
                                                @click="cancelSubscription(subscription.id)"
                                                class="text-red-600 hover:text-red-900 px-3 py-1 text-sm font-medium">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Empty State -->
                <div x-show="subscriptions.length === 0 && {{ count($subscriptions) }} === 0" x-cloak class="text-center py-12">
                    <i class="fas fa-calendar-alt text-gray-300 text-5xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900">No active subscriptions</h3>
                    <p class="mt-2 text-gray-500">Subscribe to your favorite meals for regular delivery</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Restaurant Menu Modal -->
<div x-show="showMenuModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75" @click="showMenuModal = false"></div>
        </div>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="selectedRestaurant?.business_name"></h3>
                            <button @click="showMenuModal = false" class="text-gray-400 hover:text-gray-500">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <!-- Menu Content -->
                        <div x-show="selectedRestaurant" x-cloak>
                            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-gray-600" x-text="selectedRestaurant.description"></p>
                                        <p class="text-sm text-gray-500 mt-1">
                                            <i class="fas fa-clock mr-1"></i>
                                            <span x-text="`${selectedRestaurant.opening_time || '08:00'} - ${selectedRestaurant.closing_time || '22:00'}`"></span>
                                            <span class="mx-2">•</span>
                                            <i class="fas fa-map-marker-alt mr-1"></i>
                                            <span x-text="selectedRestaurant.address"></span>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-bold text-indigo-600" x-text="`৳${cartTotal}`"></div>
                                        <p class="text-sm text-gray-500">Cart Total</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Meal Type Tabs -->
                            <div class="border-b border-gray-200 mb-6">
                                <nav class="-mb-px flex space-x-8" aria-label="Meal Types">
                                    @foreach($mealTypes as $mealType)
                                    <button @click="selectMealType({{ $mealType->id }})"
                                            :class="selectedMealTypeId === {{ $mealType->id }} ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                                        {{ $mealType->name }}
                                    </button>
                                    @endforeach
                                </nav>
                            </div>
                            
                            <!-- Food Items Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 max-h-96 overflow-y-auto">
                                <template x-for="item in filteredMenuItems" :key="item.id">
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-gray-900" x-text="item.name"></h4>
                                                <p class="text-sm text-gray-600 mt-1" x-text="item.description || 'No description available'"></p>
                                                <div class="mt-2">
                                                    <template x-for="tag in item.dietary_tags" :key="tag">
                                                        <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded mr-1" x-text="tag"></span>
                                                    </template>
                                                </div>
                                            </div>
                                            <div class="text-right ml-4">
                                                <div class="text-lg font-bold text-gray-900" x-text="`৳${item.total_price}`"></div>
                                                <div class="text-sm text-gray-500 line-through" x-text="`৳${item.base_price}`"></div>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-4 pt-4 border-t border-gray-100">
                                            <div class="flex justify-between items-center">
                                                <div class="text-sm text-gray-500">
                                                    <span x-text="item.calories ? `${item.calories} cal` : ''"></span>
                                                    <span x-show="item.daily_quantity" class="ml-2">
                                                        <span x-text="`${item.daily_quantity - item.sold_today} left`"></span>
                                                    </span>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <button @click="decreaseQuantity(item.id)"
                                                            class="w-8 h-8 rounded-full border border-gray-300 text-gray-600 hover:bg-gray-100"
                                                            :disabled="getCartQuantity(item.id) <= 0">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <span class="w-8 text-center font-medium" x-text="getCartQuantity(item.id)"></span>
                                                    <button @click="increaseQuantity(item.id)"
                                                            class="w-8 h-8 rounded-full border border-gray-300 text-gray-600 hover:bg-gray-100"
                                                            :disabled="item.daily_quantity && getCartQuantity(item.id) >= (item.daily_quantity - item.sold_today)">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            
                            <!-- Order Actions -->
                            <div class="border-t border-gray-200 pt-6">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">Delivery Options</h4>
                                        <div class="mt-2 space-y-2">
                                            <label class="flex items-center">
                                                <input type="radio" x-model="orderType" value="PAY_PER_EAT" class="mr-2" checked>
                                                <span>Pay Per Meal</span>
                                            </label>
                                            <label class="flex items-center" x-show="selectedRestaurant.supports_subscription">
                                                <input type="radio" x-model="orderType" value="SUBSCRIPTION" class="mr-2">
                                                <span>Subscribe for Regular Delivery</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="space-x-3">
                                        <button @click="showMenuModal = false"
                                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                            Cancel
                                        </button>
                                        <button @click="proceedToCheckout"
                                                :disabled="cartTotal === 0"
                                                :class="cartTotal === 0 ? 'bg-gray-300 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700'"
                                                class="px-6 py-2 rounded-lg text-white">
                                            <span x-text="orderType === 'SUBSCRIPTION' ? 'Subscribe' : 'Order Now'"></span>
                                            <span x-text="` (৳${cartTotal})`"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function foodServices() {
    return {
        activeTab: 'restaurants',
        searchQuery: '',
        selectedMealType: '',
        sortBy: 'rating',
        orderStatusFilter: '',
        
        // Data
        restaurants: [],
        orders: [],
        subscriptions: [],
        mealTypes: @json($mealTypes ?? []),
        
        // Flags
        hasInitialRestaurants: {{ count($initialRestaurants) > 0 ? 'true' : 'false' }},
        isLoading: false,
        
        // Cart
        cart: {},
        cartTotal: 0,
        
        // Modals
        showMenuModal: false,
        showCheckoutModal: false,
        showNewSubscriptionModal: false,
        
        // Selected items
        selectedRestaurant: null,
        selectedMealTypeId: {{ $mealTypes->first()->id ?? 'null' }},
        orderType: 'PAY_PER_EAT',
        
        // Methods
        async init() {
            // Initial data is already loaded from server
            console.log('Food services initialized');
        },
        
        async loadRestaurants() {
            this.isLoading = true;
            try {
                const params = new URLSearchParams({
                    search: this.searchQuery,
                    meal_type: this.selectedMealType,
                    sort: this.sortBy
                });
                
                const response = await fetch(`/food/api/restaurants?${params}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                // Check if response is HTML (error page)
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.indexOf("text/html") !== -1) {
                    throw new Error('Server returned HTML instead of JSON. Check your route.');
                }
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                if (data.success) {
                    this.restaurants = data.restaurants || [];
                } else {
                    console.error('Error loading restaurants:', data.message);
                    this.showError(data.message || 'Failed to load restaurants');
                }
            } catch (error) {
                console.error('Error loading restaurants:', error);
                this.showError('Failed to load restaurants. Please check your connection.');
            } finally {
                this.isLoading = false;
            }
        },
        
        async loadOrders() {
            this.isLoading = true;
            try {
                const params = new URLSearchParams();
                if (this.orderStatusFilter) {
                    params.append('status', this.orderStatusFilter);
                }
                
                const response = await fetch(`/food/api/orders?${params}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                // Check if response is HTML (error page)
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.indexOf("text/html") !== -1) {
                    throw new Error('Server returned HTML instead of JSON. Check your route.');
                }
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                if (data.success) {
                    this.orders = data.orders || [];
                } else {
                    console.error('Error loading orders:', data.message);
                    this.showError(data.message || 'Failed to load orders');
                }
            } catch (error) {
                console.error('Error loading orders:', error);
                this.showError('Failed to load orders. Please check your connection.');
            } finally {
                this.isLoading = false;
            }
        },
        
        async loadSubscriptions() {
            this.isLoading = true;
            try {
                const response = await fetch('/food/api/subscriptions', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                // Check if response is HTML (error page)
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.indexOf("text/html") !== -1) {
                    throw new Error('Server returned HTML instead of JSON. Check your route.');
                }
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                if (data.success) {
                    this.subscriptions = data.subscriptions || [];
                } else {
                    console.error('Error loading subscriptions:', data.message);
                    this.showError(data.message || 'Failed to load subscriptions');
                }
            } catch (error) {
                console.error('Error loading subscriptions:', error);
                this.showError('Failed to load subscriptions. Please check your connection.');
            } finally {
                this.isLoading = false;
            }
        },
        
        searchRestaurants() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.loadRestaurants();
            }, 500);
        },
        
        filterRestaurants() {
            this.loadRestaurants();
        },
        
        sortRestaurants() {
            this.loadRestaurants();
        },
        
        async viewRestaurant(restaurantId) {
            try {
                const response = await fetch(`/food/api/restaurant/${restaurantId}/menu`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                // Check if response is HTML (error page)
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.indexOf("text/html") !== -1) {
                    throw new Error('Server returned HTML instead of JSON. Check your route.');
                }
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                if (data.success) {
                    this.selectedRestaurant = data.restaurant;
                    this.selectedRestaurant.menu_items = data.menu_items || [];
                    this.selectedMealTypeId = this.mealTypes[0]?.id;
                    this.showMenuModal = true;
                    this.cart = {};
                    this.cartTotal = 0;
                    this.orderType = 'PAY_PER_EAT'; // Reset order type
                } else {
                    this.showError(data.message || 'Failed to load restaurant menu');
                }
            } catch (error) {
                console.error('Error loading restaurant:', error);
                this.showError('Failed to load restaurant menu');
            }
        },
        
        get filteredMenuItems() {
            if (!this.selectedRestaurant?.menu_items) return [];
            return this.selectedRestaurant.menu_items.filter(item => 
                item.meal_type_id == this.selectedMealTypeId
            );
        },
        
        selectMealType(mealTypeId) {
            this.selectedMealTypeId = mealTypeId;
        },
        
        increaseQuantity(itemId) {
            const item = this.filteredMenuItems.find(i => i.id == itemId);
            if (!item) return;
            
            // Check quantity limit
            if (item.daily_quantity && this.getCartQuantity(itemId) >= (item.daily_quantity - item.sold_today)) {
                this.showError(`Only ${item.daily_quantity - item.sold_today} items available`);
                return;
            }
            
            if (!this.cart[itemId]) {
                this.cart[itemId] = 0;
            }
            this.cart[itemId]++;
            this.calculateCartTotal();
        },
        
        decreaseQuantity(itemId) {
            if (this.cart[itemId] && this.cart[itemId] > 0) {
                this.cart[itemId]--;
                if (this.cart[itemId] === 0) {
                    delete this.cart[itemId];
                }
                this.calculateCartTotal();
            }
        },
        
        getCartQuantity(itemId) {
            return this.cart[itemId] || 0;
        },
        
        calculateCartTotal() {
            let total = 0;
            for (const [itemId, quantity] of Object.entries(this.cart)) {
                const item = this.selectedRestaurant?.menu_items?.find(i => i.id == itemId);
                if (item) {
                    total += parseFloat(item.total_price) * quantity;
                }
            }
            this.cartTotal = total.toFixed(2);
        },
        
        async proceedToCheckout() {
            if (this.cartTotal === 0) {
                this.showError('Please add items to cart');
                return;
            }
            
            if (this.orderType === 'SUBSCRIPTION') {
                this.showNewSubscriptionModal = true;
                return;
            }
            
            // Prepare order items
            const orderItems = [];
            for (const [itemId, quantity] of Object.entries(this.cart)) {
                const item = this.selectedRestaurant.menu_items.find(i => i.id == itemId);
                if (item) {
                    orderItems.push({
                        food_item_id: parseInt(itemId),
                        quantity: quantity
                    });
                }
            }
            
            // Prepare order data
            const today = new Date().toISOString().split('T')[0];
            
            const orderData = {
                service_provider_id: this.selectedRestaurant.id,
                meal_type_id: this.selectedMealTypeId,
                meal_date: today,
                delivery_address: 'Your delivery address',
                delivery_latitude: 23.8103,
                delivery_longitude: 90.4125,
                items: orderItems
            };
            
            try {
                const response = await fetch('/food/api/order', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(orderData)
                });
                
                // Check if response is HTML (error page)
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.indexOf("text/html") !== -1) {
                    throw new Error('Server returned HTML instead of JSON. Check your route.');
                }
                
                const result = await response.json();
                
                if (result.success) {
                    this.showMenuModal = false;
                    this.showSuccess('Order placed successfully!');
                    this.cart = {};
                    this.cartTotal = 0;
                    await this.loadOrders();
                } else {
                    this.showError(result.message || 'Failed to place order');
                }
            } catch (error) {
                console.error('Error placing order:', error);
                this.showError('Failed to place order');
            }
        },
        
        async cancelOrder(orderId) {
            if (!confirm('Are you sure you want to cancel this order?')) return;
            
            try {
                const response = await fetch(`/food/api/order/${orderId}/cancel`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                // Check if response is HTML (error page)
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.indexOf("text/html") !== -1) {
                    throw new Error('Server returned HTML instead of JSON. Check your route.');
                }
                
                const result = await response.json();
                
                if (result.success) {
                    this.showSuccess('Order cancelled successfully');
                    await this.loadOrders();
                } else {
                    this.showError(result.message || 'Failed to cancel order');
                }
            } catch (error) {
                console.error('Error cancelling order:', error);
                this.showError('Failed to cancel order');
            }
        },
        
        async cancelSubscription(subscriptionId) {
            if (!confirm('Are you sure you want to cancel this subscription?')) return;
            
            try {
                const response = await fetch(`/food/api/subscription/${subscriptionId}/cancel`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                // Check if response is HTML (error page)
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.indexOf("text/html") !== -1) {
                    throw new Error('Server returned HTML instead of JSON. Check your route.');
                }
                
                const result = await response.json();
                
                if (result.success) {
                    this.showSuccess('Subscription cancelled successfully');
                    await this.loadSubscriptions();
                } else {
                    this.showError(result.message || 'Failed to cancel subscription');
                }
            } catch (error) {
                console.error('Error cancelling subscription:', error);
                this.showError('Failed to cancel subscription');
            }
        },
        
        getStatusBadgeClass(status) {
            const classes = {
                'PENDING': 'bg-yellow-100 text-yellow-800',
                'ACCEPTED': 'bg-blue-100 text-blue-800',
                'PREPARING': 'bg-purple-100 text-purple-800',
                'OUT_FOR_DELIVERY': 'bg-indigo-100 text-indigo-800',
                'DELIVERED': 'bg-green-100 text-green-800',
                'CANCELLED': 'bg-red-100 text-red-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },
        
        getSubscriptionStatusBadgeClass(status) {
            const classes = {
                'ACTIVE': 'bg-green-100 text-green-800',
                'PAUSED': 'bg-yellow-100 text-yellow-800',
                'CANCELLED': 'bg-red-100 text-red-800',
                'COMPLETED': 'bg-blue-100 text-blue-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },
        
        showSuccess(message) {
            // Use SweetAlert or Toastr for better notifications
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: message,
                    timer: 3000
                });
            } else {
                alert('Success: ' + message);
            }
        },
        
        showError(message) {
            // Use SweetAlert or Toastr for better notifications
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message
                });
            } else {
                alert('Error: ' + message);
            }
        }
    };
}
</script>

<style>
[x-cloak] {
    display: none !important;
}

/* Loading spinner */
.loading-spinner {
    border: 3px solid #f3f3f3;
    border-top: 3px solid #3498db;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    animation: spin 1s linear infinite;
    margin: 0 auto;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

@endsection