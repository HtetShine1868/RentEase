<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h3 class="text-lg font-semibold text-gray-900">Your Subscriptions</h3>
            <button @click="showSubscriptionModal = true"
                    class="w-full sm:w-auto bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors flex items-center justify-center">
                <i class="fas fa-plus mr-2"></i>New Subscription
            </button>
        </div>
    </div>

    <!-- Subscriptions Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Initial subscriptions from server -->
        @if(count($subscriptions) > 0)
            @foreach($subscriptions as $subscription)
            <div class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="font-bold text-lg text-gray-900">{{ $subscription['business_name'] }}</h4>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $subscription['status'] === 'ACTIVE' ? 'bg-green-100 text-green-800' : 
                                   ($subscription['status'] === 'PAUSED' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($subscription['status'] === 'CANCELLED' ? 'bg-red-100 text-red-800' : 
                                   ($subscription['status'] === 'COMPLETED' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'))) }}">
                                {{ $subscription['status'] }}
                            </span>
                        </div>
                        
                        <div class="mt-4 space-y-2">
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-utensils w-5 text-gray-400"></i>
                                <span class="ml-2">{{ $subscription['meal_type'] }}</span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="far fa-clock w-5 text-gray-400"></i>
                                <span class="ml-2">{{ $subscription['delivery_time'] }}</span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-calendar-alt w-5 text-gray-400"></i>
                                <span class="ml-2">{{ $subscription['delivery_days_text'] }}</span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-calendar-week w-5 text-gray-400"></i>
                                <span class="ml-2">{{ $subscription['start_date_formatted'] }} - {{ $subscription['end_date_formatted'] }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-right">
                        <div class="text-2xl font-bold text-indigo-600">৳{{ $subscription['daily_price'] }}</div>
                        <div class="text-sm text-gray-500">per day</div>
                        <div class="mt-2 text-sm text-gray-600">Total: ৳{{ $subscription['total_price'] }}</div>
                        @if($subscription['discount_amount'] > 0)
                        <div class="text-sm text-green-600">Discount: ৳{{ $subscription['discount_amount'] }}</div>
                        @endif
                    </div>
                </div>
                
                <div class="mt-6 pt-6 border-t border-gray-100">
                    <div class="flex flex-wrap gap-2 justify-end">
                        <button class="px-3 py-1 text-indigo-600 hover:text-indigo-900 text-sm font-medium border border-indigo-200 rounded-md hover:bg-indigo-50">
                            <i class="fas fa-eye mr-1"></i>Details
                        </button>
                        
                        @if($subscription['status'] === 'ACTIVE')
                        <button @click="pauseSubscription({{ $subscription['id'] }})"
                                class="px-3 py-1 text-yellow-600 hover:text-yellow-900 text-sm font-medium border border-yellow-200 rounded-md hover:bg-yellow-50">
                            <i class="fas fa-pause mr-1"></i>Pause
                        </button>
                        @endif
                        
                        @if($subscription['status'] === 'PAUSED')
                        <button @click="resumeSubscription({{ $subscription['id'] }})"
                                class="px-3 py-1 text-green-600 hover:text-green-900 text-sm font-medium border border-green-200 rounded-md hover:bg-green-50">
                            <i class="fas fa-play mr-1"></i>Resume
                        </button>
                        @endif
                        
                        @if(in_array($subscription['status'], ['ACTIVE', 'PAUSED']))
                        <button @click="cancelSubscription({{ $subscription['id'] }})"
                                class="px-3 py-1 text-red-600 hover:text-red-900 text-sm font-medium border border-red-200 rounded-md hover:bg-red-50">
                            <i class="fas fa-times mr-1"></i>Cancel
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        @endif

        <!-- Dynamic subscriptions from AJAX -->
        <template x-for="subscription in subscriptions" :key="subscription.id">
            <div class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="font-bold text-lg text-gray-900" x-text="subscription.business_name"></h4>
                            <span :class="getSubscriptionStatusBadgeClass(subscription.status)" 
                                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                <span x-text="subscription.status"></span>
                            </span>
                        </div>
                        
                        <div class="mt-4 space-y-2">
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-utensils w-5 text-gray-400"></i>
                                <span class="ml-2" x-text="subscription.meal_type"></span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="far fa-clock w-5 text-gray-400"></i>
                                <span class="ml-2" x-text="subscription.delivery_time"></span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-calendar-alt w-5 text-gray-400"></i>
                                <span class="ml-2" x-text="subscription.delivery_days_text"></span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-calendar-week w-5 text-gray-400"></i>
                                <span class="ml-2" x-text="subscription.start_date_formatted + ' - ' + subscription.end_date_formatted"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-right">
                        <div class="text-2xl font-bold text-indigo-600" x-text="`৳${subscription.daily_price}`"></div>
                        <div class="text-sm text-gray-500">per day</div>
                        <div class="mt-2 text-sm text-gray-600" x-text="`Total: ৳${subscription.total_price}`"></div>
                        <div x-show="subscription.discount_amount > 0" 
                             class="text-sm text-green-600" 
                             x-text="`Discount: ৳${subscription.discount_amount}`"></div>
                    </div>
                </div>
                
                <div class="mt-6 pt-6 border-t border-gray-100">
                    <div class="flex flex-wrap gap-2 justify-end">
                        <button class="px-3 py-1 text-indigo-600 hover:text-indigo-900 text-sm font-medium border border-indigo-200 rounded-md hover:bg-indigo-50">
                            <i class="fas fa-eye mr-1"></i>Details
                        </button>
                        
                        <button x-show="subscription.status === 'ACTIVE'"
                                @click="pauseSubscription(subscription.id)"
                                class="px-3 py-1 text-yellow-600 hover:text-yellow-900 text-sm font-medium border border-yellow-200 rounded-md hover:bg-yellow-50">
                            <i class="fas fa-pause mr-1"></i>Pause
                        </button>
                        
                        <button x-show="subscription.status === 'PAUSED'"
                                @click="resumeSubscription(subscription.id)"
                                class="px-3 py-1 text-green-600 hover:text-green-900 text-sm font-medium border border-green-200 rounded-md hover:bg-green-50">
                            <i class="fas fa-play mr-1"></i>Resume
                        </button>
                        
                        <button x-show="subscription.status === 'ACTIVE' || subscription.status === 'PAUSED'"
                                @click="cancelSubscription(subscription.id)"
                                class="px-3 py-1 text-red-600 hover:text-red-900 text-sm font-medium border border-red-200 rounded-md hover:bg-red-50">
                            <i class="fas fa-times mr-1"></i>Cancel
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Empty State -->
    <div x-show="subscriptions.length === 0 && {{ count($subscriptions) }} === 0" x-cloak class="text-center py-12">
        <div class="text-gray-300 text-6xl mb-4">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900">No active subscriptions</h3>
        <p class="mt-2 text-gray-500">Subscribe to your favorite meals for regular delivery and save money!</p>
        <button @click="showSubscriptionModal = true" 
                class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            Create Subscription
        </button>
    </div>
</div>