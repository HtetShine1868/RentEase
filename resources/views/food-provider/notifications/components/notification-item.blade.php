<div class="notification-item p-4 hover:bg-gray-50 
            @if(!$isRead) notification-unread @else notification-read @endif
            @if($isImportant) notification-important @endif"
     data-type="{{ $type ?? 'order' }}"
     data-id="{{ $id ?? 1 }}">
    <div class="flex items-start">
        <!-- Notification Icon -->
        <div class="flex-shrink-0 mr-3">
            <div class="h-10 w-10 rounded-full flex items-center justify-center 
                @if($type === 'order') bg-green-100 text-green-600
                @elseif($type === 'review') bg-yellow-100 text-yellow-600
                @elseif($type === 'subscription') bg-blue-100 text-blue-600
                @elseif($type === 'payment') bg-purple-100 text-purple-600
                @else bg-gray-100 text-gray-600
                @endif">
                @if($type === 'order')
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                @elseif($type === 'review')
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
                @elseif($type === 'subscription')
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                @elseif($type === 'payment')
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                @else
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                @endif
            </div>
        </div>
        
        <!-- Notification Content -->
        <div class="flex-1 min-w-0">
            <div class="flex justify-between items-start">
                <div>
                    <h4 class="notification-title text-sm font-medium text-gray-900">
                        {{ $title ?? 'Notification Title' }}
                    </h4>
                    <p class="notification-message text-sm text-gray-600 mt-1">
                        {{ $message ?? 'Notification message goes here' }}
                    </p>
                    
                    <!-- Additional Details -->
                    <div class="mt-2">
                        @if(isset($orderId) && $type === 'order')
                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                Order: {{ $orderId }}
                            </span>
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $orderType ?? 'pay-per-eat' }}
                            </span>
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                {{ $mealType ?? 'Lunch' }}
                            </span>
                            @if(isset($amount))
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                ${{ number_format($amount, 2) }}
                            </span>
                            @endif
                        </div>
                        @endif
                        
                        @if(isset($reviewId) && $type === 'review')
                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                Review #{{ $reviewId }}
                            </span>
                            @if(isset($rating))
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-3 h-3 {{ $i <= $rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @endfor
                            </div>
                            @endif
                        </div>
                        @endif
                        
                        @if(isset($systemType) && $type === 'system')
                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                {{ ucfirst($systemType) }}
                            </span>
                            @if(isset($maintenanceTime))
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800">
                                {{ $maintenanceTime }}
                            </span>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Time and Actions -->
                <div class="flex flex-col items-end">
                    <span class="text-xs text-gray-500">{{ $time ?? 'Just now' }}</span>
                    
                    <!-- Unread Indicator -->
                    @if(!$isRead)
                    <div class="unread-indicator mt-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                            New
                        </span>
                    </div>
                    @else
                    <div class="unread-indicator hidden"></div>
                    @endif
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="mt-3 flex items-center space-x-4">
                @if(!$isRead)
                <button onclick="markAsRead('{{ $id ?? 1 }}')" 
                        class="text-sm text-blue-600 hover:text-blue-800">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Mark as read
                </button>
                @endif
                
                @if(isset($orderId) && $type === 'order')
                <a href="{{ route('food-provider.orders.show', $orderId ?? 1) }}" 
                   class="text-sm text-green-600 hover:text-green-800">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    View Order
                </a>
                @endif
                
                @if(isset($reviewId) && $type === 'review')
                <a href="{{ route('food-provider.reviews.index') }}?review={{ $reviewId ?? 1 }}" 
                   class="text-sm text-yellow-600 hover:text-yellow-800">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                    View Review
                </a>
                @endif
                
                <button onclick="deleteNotification('{{ $id ?? 1 }}')" 
                        class="text-sm text-red-600 hover:text-red-800">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function markAsRead(notificationId) {
        const notification = document.querySelector(`[data-id="${notificationId}"]`);
        
        if (notification) {
            notification.classList.remove('notification-unread');
            notification.classList.add('notification-read');
            notification.querySelector('.unread-indicator').classList.add('hidden');
            
            // Update action button
            const markAsReadButton = notification.querySelector('button[onclick^="markAsRead"]');
            if (markAsReadButton) {
                markAsReadButton.remove();
            }
            
            // In production: Send API request
            console.log('Marked notification as read:', notificationId);
            
            // Update unread count
            updateUnreadCount();
        }
    }
    
    function deleteNotification(notificationId) {
        if (confirm('Delete this notification?')) {
            const notification = document.querySelector(`[data-id="${notificationId}"]`);
            
            if (notification) {
                // Remove from DOM with animation
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(-20px)';
                notification.style.transition = 'all 0.3s ease';
                
                setTimeout(() => {
                    notification.remove();
                    
                    // Check if list is empty
                    const remainingNotifications = document.querySelectorAll('.notification-item').length;
                    if (remainingNotifications === 0) {
                        document.getElementById('notifications-list').classList.add('hidden');
                        document.getElementById('empty-state').classList.remove('hidden');
                    }
                    
                    // In production: Send API request
                    console.log('Deleted notification:', notificationId);
                }, 300);
            }
        }
    }
    
    // Helper function to update unread count (defined in main page)
    function updateUnreadCount() {
        // This function should be defined in the main notifications page
        if (typeof window.updateUnreadCount === 'function') {
            window.updateUnreadCount();
        }
    }
</script>