<!-- resources/views/livewire/dashboard/quick-actions.blade.php -->
<div class="bg-white shadow-sm sm:rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
    </div>
    
    <div class="p-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Find Property -->
            <a href="#" class="group relative flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="flex-shrink-0 h-10 w-10 bg-indigo-600 rounded-lg flex items-center justify-center group-hover:bg-indigo-700 transition-colors">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <span class="mt-2 text-sm font-medium text-gray-900">Find Property</span>
                <span class="text-xs text-gray-500">Search & Book</span>
            </a>
            
            <!-- Order Food -->
            <a href="#" class="group relative flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="flex-shrink-0 h-10 w-10 bg-green-600 rounded-lg flex items-center justify-center group-hover:bg-green-700 transition-colors">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="mt-2 text-sm font-medium text-gray-900">Order Food</span>
                <span class="text-xs text-gray-500">Subscribe or One-time</span>
            </a>
            
            <!-- Request Laundry -->
            <a href="#" class="group relative flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="flex-shrink-0 h-10 w-10 bg-blue-600 rounded-lg flex items-center justify-center group-hover:bg-blue-700 transition-colors">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <span class="mt-2 text-sm font-medium text-gray-900">Laundry</span>
                <span class="text-xs text-gray-500">Normal or Rush</span>
            </a>
            
            <!-- Apply for Role -->
            <a href="#" class="group relative flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="flex-shrink-0 h-10 w-10 bg-purple-600 rounded-lg flex items-center justify-center group-hover:bg-purple-700 transition-colors">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="mt-2 text-sm font-medium text-gray-900">Become Provider</span>
                <span class="text-xs text-gray-500">Apply for Role</span>
            </a>
        </div>
        
        <!-- Recent Activity -->
        <div class="mt-8">
            <h4 class="text-sm font-medium text-gray-900 mb-4">Recent Activity</h4>
            <div class="space-y-3">
                <!-- Sample activity items -->
                <div class="flex items-center text-sm">
                    <div class="flex-shrink-0 h-8 w-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-gray-900">Booking confirmed for "Sunrise Hostel"</p>
                        <p class="text-gray-500">2 hours ago</p>
                    </div>
                </div>
                
                <div class="flex items-center text-sm">
                    <div class="flex-shrink-0 h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-gray-900">Food order is out for delivery</p>
                        <p class="text-gray-500">Yesterday, 6:30 PM</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>