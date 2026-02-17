<div>
    <!-- Filters and Actions -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h3 class="text-lg font-semibold text-gray-900">Your Orders</h3>
            <div class="flex gap-2 w-full sm:w-auto">
                <select x-model="orderStatusFilter" @change="loadOrders" class="flex-1 sm:flex-none border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Status</option>
                    <option value="PENDING">Pending</option>
                    <option value="ACCEPTED">Accepted</option>
                    <option value="PREPARING">Preparing</option>
                    <option value="OUT_FOR_DELIVERY">Out for Delivery</option>
                    <option value="DELIVERED">Delivered</option>
                    <option value="CANCELLED">Cancelled</option>
                </select>
                <button @click="loadOrders" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Orders List -->
    <div class="space-y-4">
        <!-- Initial orders from server -->
        @if(isset($recentOrders) && count($recentOrders) > 0)
            @foreach($recentOrders as $order)
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                    <div class="flex-1">
                        <div class="flex items-center flex-wrap gap-2">
                            <span class="font-semibold text-gray-900">{{ $order['order_reference'] }}</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $order['status'] === 'PENDING' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($order['status'] === 'ACCEPTED' ? 'bg-blue-100 text-blue-800' : 
                                   ($order['status'] === 'PREPARING' ? 'bg-purple-100 text-purple-800' : 
                                   ($order['status'] === 'OUT_FOR_DELIVERY' ? 'bg-indigo-100 text-indigo-800' : 
                                   ($order['status'] === 'DELIVERED' ? 'bg-green-100 text-green-800' : 
                                   ($order['status'] === 'CANCELLED' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))))) }}">
                                {{ str_replace('_', ' ', $order['status']) }}
                            </span>
                            
                            <!-- Rating Badge for Delivered Orders -->
                            @if($order['status'] === 'DELIVERED')
                                @if(isset($order['is_rated']) && $order['is_rated'])
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-star mr-1 text-yellow-500"></i> Rated
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-star mr-1"></i> Pending Review
                                    </span>
                                @endif
                            @endif
                        </div>
                        
                        <div class="mt-2 space-y-1">
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">{{ $order['business_name'] }}</span> • 
                                <span>{{ $order['meal_type'] }}</span>
                            </p>
                            <p class="text-sm text-gray-500">
                                <i class="far fa-clock mr-1"></i>{{ $order['created_at_formatted'] }}
                            </p>
                            <p class="text-sm text-gray-500">
                                <i class="fas fa-map-marker-alt mr-1"></i>{{ $order['delivery_address'] }}
                            </p>
                        </div>
                        
                        <!-- Order Items Preview -->
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach($order['items'] as $item)
                            <span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-xs text-gray-700">
                                {{ $item['name'] }} × {{ $item['quantity'] }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="text-right w-full sm:w-auto">
                        <div class="text-lg font-bold text-gray-900">৳{{ number_format($order['total_amount'], 2) }}</div>
                        <div class="flex flex-wrap gap-2 mt-2 justify-end">
                            <button @click="viewOrderDetails({{ $order['id'] }})" 
                                    class="text-indigo-600 hover:text-indigo-900 px-3 py-1 text-sm font-medium border border-indigo-200 rounded-md hover:bg-indigo-50">
                                <i class="fas fa-eye mr-1"></i>Details
                            </button>
                            
                            @if(in_array($order['status'], ['PENDING', 'ACCEPTED']))
                            <button @click="cancelOrder({{ $order['id'] }})"
                                    class="text-red-600 hover:text-red-900 px-3 py-1 text-sm font-medium border border-red-200 rounded-md hover:bg-red-50">
                                <i class="fas fa-times mr-1"></i>Cancel
                            </button>
                            @endif
                            
                            <!-- Rate Order Button - Only for delivered orders that aren't rated -->
                            @if($order['status'] === 'DELIVERED' && (!isset($order['is_rated']) || !$order['is_rated']))
                            <a href="{{ url('/food/orders/rate/' . $order['id']) }}" 
                                    class="text-green-600 hover:text-green-900 px-3 py-1 text-sm font-medium border border-green-200 rounded-md hover:bg-green-50">
                                <i class="fas fa-star mr-1"></i>Rate Order
                            </a>
                            @endif
                            
                            <!-- Reorder Button -->
                            @if(in_array($order['status'], ['DELIVERED', 'CANCELLED']))
                            <button @click="reorderItems({{ $order['id'] }})"
                                    class="text-blue-600 hover:text-blue-900 px-3 py-1 text-sm font-medium border border-blue-200 rounded-md hover:bg-blue-50">
                                <i class="fas fa-redo mr-1"></i>Reorder
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- User's Rating Display (if already rated) -->
                @if(isset($order['user_rating']) && $order['user_rating'])
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-gray-700 mr-3">Your Rating:</span>
                            <div class="flex text-yellow-400">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $order['user_rating']['overall_rating'])
                                        <i class="fas fa-star"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="ml-2 text-sm text-gray-600">{{ $order['user_rating']['overall_rating'] }}/5</span>
                        </div>
                        <a href="{{ url('/food/orders/rate/' . $order['id'] . '/edit') }}" 
                           class="text-xs text-indigo-600 hover:text-indigo-800">
                            <i class="fas fa-edit mr-1"></i>Edit Review
                        </a>
                    </div>
                    @if(isset($order['user_rating']['comment']) && $order['user_rating']['comment'])
                    <p class="mt-2 text-sm text-gray-600 italic">"{{ $order['user_rating']['comment'] }}"</p>
                    @endif
                </div>
                @endif
            </div>
            @endforeach
        @endif

        <!-- Dynamic orders from AJAX -->
        <template x-for="order in orders" :key="order.id">
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                    <div class="flex-1">
                        <div class="flex items-center flex-wrap gap-2">
                            <span class="font-semibold text-gray-900" x-text="order.order_reference"></span>
                            <span :class="getStatusBadgeClass(order.status)" 
                                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                <span x-text="order.status.replace('_', ' ')"></span>
                            </span>
                            
                            <!-- Rating Badge for Delivered Orders -->
                            <template x-if="order.status === 'DELIVERED'">
                                <span x-show="order.is_rated" 
                                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-star mr-1 text-yellow-500"></i> Rated
                                </span>
                                <span x-show="!order.is_rated" 
                                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-star mr-1"></i> Pending Review
                                </span>
                            </template>
                        </div>
                        
                        <div class="mt-2 space-y-1">
                            <p class="text-sm text-gray-600">
                                <span class="font-medium" x-text="order.business_name"></span> • 
                                <span x-text="order.meal_type"></span>
                            </p>
                            <p class="text-sm text-gray-500">
                                <i class="far fa-clock mr-1"></i><span x-text="order.created_at_formatted"></span>
                            </p>
                            <p class="text-sm text-gray-500">
                                <i class="fas fa-map-marker-alt mr-1"></i><span x-text="order.delivery_address"></span>
                            </p>
                        </div>
                        
                        <!-- Order Items Preview -->
                        <div class="mt-3 flex flex-wrap gap-2">
                            <template x-for="item in order.items" :key="item.id">
                                <span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-xs text-gray-700">
                                    <span x-text="item.name"></span> × <span x-text="item.quantity"></span>
                                </span>
                            </template>
                        </div>
                    </div>
                    
                    <div class="text-right w-full sm:w-auto">
                        <div class="text-lg font-bold text-gray-900" x-text="`৳${order.total_amount}`"></div>
                        <div class="flex flex-wrap gap-2 mt-2 justify-end">
                            <button @click="viewOrderDetails(order.id)" 
                                    class="text-indigo-600 hover:text-indigo-900 px-3 py-1 text-sm font-medium border border-indigo-200 rounded-md hover:bg-indigo-50">
                                <i class="fas fa-eye mr-1"></i>Details
                            </button>
                            
                            <button x-show="order.status === 'PENDING' || order.status === 'ACCEPTED'"
                                    @click="cancelOrder(order.id)"
                                    class="text-red-600 hover:text-red-900 px-3 py-1 text-sm font-medium border border-red-200 rounded-md hover:bg-red-50">
                                <i class="fas fa-times mr-1"></i>Cancel
                            </button>
                            
                            <!-- Rate Order Button -->
                            <template x-if="order.status === 'DELIVERED' && !order.is_rated">
                                <a :href="`/food/orders/rate/${order.id}`" 
                                        class="text-green-600 hover:text-green-900 px-3 py-1 text-sm font-medium border border-green-200 rounded-md hover:bg-green-50">
                                    <i class="fas fa-star mr-1"></i>Rate Order
                                </a>
                            </template>
                            
                            <!-- Reorder Button -->
                            <button x-show="order.status === 'DELIVERED' || order.status === 'CANCELLED'"
                                    @click="reorderItems(order.id)"
                                    class="text-blue-600 hover:text-blue-900 px-3 py-1 text-sm font-medium border border-blue-200 rounded-md hover:bg-blue-50">
                                <i class="fas fa-redo mr-1"></i>Reorder
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- User's Rating Display (if already rated) - FIXED with null checks -->
                <div x-show="order.status === 'DELIVERED' && order.user_rating" 
                     class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-gray-700 mr-3">Your Rating:</span>
                            <div class="flex text-yellow-400">
                                <template x-for="i in 5" :key="i">
                                    <!-- FIXED: Added optional chaining and null check -->
                                    <i :class="order.user_rating && i <= order.user_rating.overall_rating ? 'fas fa-star' : 'far fa-star'"></i>
                                </template>
                            </div>
                            <!-- FIXED: Added conditional rendering with null check -->
                            <span class="ml-2 text-sm text-gray-600" x-text="order.user_rating ? order.user_rating.overall_rating + '/5' : ''"></span>
                        </div>
                        <a :href="`/food/orders/rate/${order.id}/edit`" 
                           class="text-xs text-indigo-600 hover:text-indigo-800">
                            <i class="fas fa-edit mr-1"></i>Edit Review
                        </a>
                    </div>
                    <!-- FIXED: Added conditional rendering with null check -->
                    <p x-show="order.user_rating && order.user_rating.comment" 
                       class="mt-2 text-sm text-gray-600 italic" 
                       x-text="`"${order.user_rating.comment}"`"></p>
                </div>
            </div>
        </template>
    </div>

    <!-- Empty State -->
    <div x-show="orders.length === 0 && {{ isset($recentOrders) ? count($recentOrders) : 0 }} === 0" x-cloak class="text-center py-12">
        <div class="text-gray-300 text-6xl mb-4">
            <i class="fas fa-shopping-bag"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900">No orders yet</h3>
        <p class="mt-2 text-gray-500">Your food orders will appear here</p>
        <button @click="activeTab = 'restaurants'" 
                class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            Browse Restaurants
        </button>
    </div>
</div>