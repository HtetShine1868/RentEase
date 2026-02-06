@extends('layouts.food-provider')

@section('title', 'Notifications - Food Provider Dashboard')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
                <p class="text-gray-600 mt-2">Stay updated with orders, reviews, and system alerts</p>
            </div>
            <div class="flex items-center space-x-4">
                <button onclick="markAllAsRead()" 
                        class="px-4 py-2 text-sm font-medium text-blue-600 hover:text-blue-800">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Mark all as read
                </button>
                <button onclick="clearAllNotifications()" 
                        class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-800">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Clear all
                </button>
            </div>
        </div>
    </div>

    <!-- Notification Tabs -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button onclick="switchTab('all')" 
                        id="tab-all"
                        class="py-4 px-6 text-sm font-medium border-b-2 border-blue-500 text-blue-600">
                    All Notifications
                    <span class="ml-2 px-2 py-1 text-xs bg-blue-100 text-blue-600 rounded-full">24</span>
                </button>
                <button onclick="switchTab('unread')" 
                        id="tab-unread"
                        class="py-4 px-6 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Unread
                    <span class="ml-2 px-2 py-1 text-xs bg-red-100 text-red-600 rounded-full">5</span>
                </button>
                <button onclick="switchTab('orders')" 
                        id="tab-orders"
                        class="py-4 px-6 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Orders
                    <span class="ml-2 px-2 py-1 text-xs bg-green-100 text-green-600 rounded-full">12</span>
                </button>
                <button onclick="switchTab('reviews')" 
                        id="tab-reviews"
                        class="py-4 px-6 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Reviews
                    <span class="ml-2 px-2 py-1 text-xs bg-yellow-100 text-yellow-600 rounded-full">6</span>
                </button>
                <button onclick="switchTab('system')" 
                        id="tab-system"
                        class="py-4 px-6 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    System
                    <span class="ml-2 px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-full">3</span>
                </button>
            </nav>
        </div>

        <!-- Filters -->
        <div class="p-4 border-b bg-gray-50">
            <div class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-4">
                <div class="flex-1">
                    <input type="text" 
                           placeholder="Search notifications..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           onkeyup="searchNotifications(this.value)">
                </div>
                <div class="flex space-x-4">
                    <select class="border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            onchange="filterByDate(this.value)">
                        <option value="">All Time</option>
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                    </select>
                    <label class="inline-flex items-center">
                        <input type="checkbox" 
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                               onchange="toggleImportantOnly(this.checked)">
                        <span class="ml-2 text-sm text-gray-600">Important Only</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div id="notifications-list" class="divide-y divide-gray-200">
            <!-- Today's Notifications -->
            <div class="notification-section">
                <div class="px-6 py-3 bg-gray-50">
                    <h3 class="text-sm font-medium text-gray-900">Today</h3>
                </div>
                
                <!-- Today's Notification Items -->
                @include('food-provider.notifications.components.notification-item', [
                    'id' => 1,
                    'type' => 'order',
                    'title' => 'New Order Received',
                    'message' => 'John Doe placed a new pay-per-eat order for Lunch',
                    'time' => '2 hours ago',
                    'isRead' => false,
                    'isImportant' => true,
                    'orderId' => 'ORD-001234',
                    'customerName' => 'John Doe',
                    'orderType' => 'pay-per-eat',
                    'mealType' => 'Lunch',
                    'amount' => 28.50
                ])
                
                @include('food-provider.notifications.components.notification-item', [
                    'id' => 2,
                    'type' => 'review',
                    'title' => 'New Review Received',
                    'message' => 'Sarah Williams rated your service 5 stars',
                    'time' => '4 hours ago',
                    'isRead' => false,
                    'isImportant' => false,
                    'rating' => 5,
                    'customerName' => 'Sarah Williams',
                    'reviewId' => 'REV-001'
                ])
                
                @include('food-provider.notifications.components.notification-item', [
                    'id' => 3,
                    'type' => 'subscription',
                    'title' => 'Subscription Renewed',
                    'message' => 'Michael Brown renewed his monthly dinner subscription',
                    'time' => '6 hours ago',
                    'isRead' => true,
                    'isImportant' => true,
                    'customerName' => 'Michael Brown',
                    'subscriptionId' => 'SUB-001',
                    'mealType' => 'Dinner',
                    'amount' => 90.00
                ])
            </div>

            <!-- Yesterday's Notifications -->
            <div class="notification-section">
                <div class="px-6 py-3 bg-gray-50">
                    <h3 class="text-sm font-medium text-gray-900">Yesterday</h3>
                </div>
                
                @include('food-provider.notifications.components.notification-item', [
                    'id' => 4,
                    'type' => 'system',
                    'title' => 'System Maintenance',
                    'message' => 'Scheduled maintenance will occur tomorrow at 2 AM',
                    'time' => '1 day ago',
                    'isRead' => true,
                    'isImportant' => true,
                    'systemType' => 'maintenance',
                    'maintenanceTime' => 'Tomorrow, 2:00 AM - 4:00 AM'
                ])
                
                @include('food-provider.notifications.components.notification-item', [
                    'id' => 5,
                    'type' => 'order',
                    'title' => 'Order Cancelled',
                    'message' => 'Robert Johnson cancelled his lunch order',
                    'time' => '1 day ago',
                    'isRead' => true,
                    'isImportant' => false,
                    'orderId' => 'ORD-001233',
                    'customerName' => 'Robert Johnson',
                    'orderType' => 'pay-per-eat',
                    'status' => 'cancelled',
                    'cancellationReason' => 'Change of plans'
                ])
            </div>

            <!-- This Week -->
            <div class="notification-section">
                <div class="px-6 py-3 bg-gray-50">
                    <h3 class="text-sm font-medium text-gray-900">This Week</h3>
                </div>
                
                @include('food-provider.notifications.components.notification-item', [
                    'id' => 6,
                    'type' => 'payment',
                    'title' => 'Payment Received',
                    'message' => 'Payment of $120.00 received from Emily Davis',
                    'time' => '3 days ago',
                    'isRead' => true,
                    'isImportant' => true,
                    'customerName' => 'Emily Davis',
                    'amount' => 120.00,
                    'paymentMethod' => 'Credit Card'
                ])
                
                @include('food-provider.notifications.components.notification-item', [
                    'id' => 7,
                    'type' => 'review',
                    'title' => 'Review Response',
                    'message' => 'David Wilson replied to your response',
                    'time' => '4 days ago',
                    'isRead' => true,
                    'isImportant' => false,
                    'customerName' => 'David Wilson',
                    'reviewId' => 'REV-002'
                ])
            </div>
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="hidden">
            @include('food-provider.components.empty-state', [
                'title' => 'No notifications',
                'message' => 'You\'re all caught up! New notifications will appear here.',
                'icon' => 'bell',
                'show' => true
            ])
        </div>

        <!-- Load More Button -->
        <div class="px-6 py-4 border-t bg-gray-50 text-center">
            <button onclick="loadMoreNotifications()" 
                    class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                Load More Notifications
            </button>
        </div>
    </div>

    <!-- Notification Settings -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold text-gray-900">Notification Settings</h2>
            <p class="text-sm text-gray-600 mt-1">Customize how you receive notifications</p>
        </div>
        
        <div class="p-6">
            <div class="space-y-6">
                <!-- Email Notifications -->
                <div>
                    <h3 class="text-sm font-medium text-gray-900 mb-4">Email Notifications</h3>
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" checked>
                            <span class="ml-3 text-sm text-gray-700">New orders</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" checked>
                            <span class="ml-3 text-sm text-gray-700">Customer reviews</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-3 text-sm text-gray-700">Subscription updates</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" checked>
                            <span class="ml-3 text-sm text-gray-700">System announcements</span>
                        </label>
                    </div>
                </div>
                
                <!-- In-App Notifications -->
                <div>
                    <h3 class="text-sm font-medium text-gray-900 mb-4">In-App Notifications</h3>
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" checked>
                            <span class="ml-3 text-sm text-gray-700">Desktop notifications</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" checked>
                            <span class="ml-3 text-sm text-gray-700">Sound alerts</span>
                        </label>
                        <div class="ml-6">
                            <select class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option>Default sound</option>
                                <option>Soft chime</option>
                                <option>Gentle bell</option>
                                <option>No sound</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Quiet Hours -->
                <div>
                    <h3 class="text-sm font-medium text-gray-900 mb-4">Quiet Hours</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Start Time</label>
                            <input type="time" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" value="22:00">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">End Time</label>
                            <input type="time" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" value="07:00">
                        </div>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">Notifications will be muted during these hours</p>
                </div>
                
                <!-- Save Button -->
                <div class="pt-4 border-t">
                    <button onclick="saveNotificationSettings()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Save Settings
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .notification-item:hover {
        background-color: #f9fafb;
    }
    
    .notification-unread {
        background-color: #eff6ff;
        border-left: 4px solid #3b82f6;
    }
    
    .notification-important {
        background-color: #fef3c7;
    }
    
    .notification-section {
        margin-bottom: 1rem;
    }
</style>
@endpush

@push('scripts')
<script>
    let currentTab = 'all';
    let currentFilter = '';
    let importantOnly = false;
    
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Notifications page loaded');
        updateUnreadCount();
    });
    
    function switchTab(tab) {
        currentTab = tab;
        
        // Update active tab styling
        document.querySelectorAll('[id^="tab-"]').forEach(tabElement => {
            tabElement.classList.remove('border-blue-500', 'text-blue-600');
            tabElement.classList.add('border-transparent', 'text-gray-500');
        });
        
        document.getElementById(`tab-${tab}`).classList.add('border-blue-500', 'text-blue-600');
        document.getElementById(`tab-${tab}`).classList.remove('border-transparent', 'text-gray-500');
        
        // Filter notifications based on tab
        filterNotifications();
    }
    
    function filterNotifications() {
        const notifications = document.querySelectorAll('.notification-item');
        let visibleCount = 0;
        
        notifications.forEach(notification => {
            const type = notification.dataset.type;
            const isRead = notification.classList.contains('notification-read');
            const isImportant = notification.classList.contains('notification-important');
            
            let shouldShow = true;
            
            // Apply tab filter
            if (currentTab === 'unread' && isRead) {
                shouldShow = false;
            } else if (currentTab === 'orders' && type !== 'order' && type !== 'subscription' && type !== 'payment') {
                shouldShow = false;
            } else if (currentTab === 'reviews' && type !== 'review') {
                shouldShow = false;
            } else if (currentTab === 'system' && type !== 'system') {
                shouldShow = false;
            }
            
            // Apply important filter
            if (importantOnly && !isImportant) {
                shouldShow = false;
            }
            
            // Apply search filter
            if (currentFilter) {
                const title = notification.querySelector('.notification-title').textContent.toLowerCase();
                const message = notification.querySelector('.notification-message').textContent.toLowerCase();
                const searchTerm = currentFilter.toLowerCase();
                
                if (!title.includes(searchTerm) && !message.includes(searchTerm)) {
                    shouldShow = false;
                }
            }
            
            // Show/hide notification
            if (shouldShow) {
                notification.style.display = '';
                visibleCount++;
            } else {
                notification.style.display = 'none';
            }
        });
        
        // Show/hide empty state
        const emptyState = document.getElementById('empty-state');
        const notificationsList = document.getElementById('notifications-list');
        
        if (visibleCount === 0) {
            emptyState.classList.remove('hidden');
            notificationsList.classList.add('hidden');
        } else {
            emptyState.classList.add('hidden');
            notificationsList.classList.remove('hidden');
        }
    }
    
    function searchNotifications(searchTerm) {
        currentFilter = searchTerm;
        filterNotifications();
    }
    
    function filterByDate(dateFilter) {
        // In production: Filter notifications by date
        console.log('Filter by date:', dateFilter);
        // This would typically be handled server-side
    }
    
    function toggleImportantOnly(isChecked) {
        importantOnly = isChecked;
        filterNotifications();
    }
    
    function markAllAsRead() {
        if (confirm('Mark all notifications as read?')) {
            document.querySelectorAll('.notification-item').forEach(notification => {
                notification.classList.remove('notification-unread');
                notification.classList.add('notification-read');
                notification.querySelector('.unread-indicator').classList.add('hidden');
            });
            
            // Update unread count
            updateUnreadCount();
            
            // In production: Send API request
            console.log('All notifications marked as read');
        }
    }
    
    function clearAllNotifications() {
        if (confirm('Clear all notifications? This action cannot be undone.')) {
            // In production: Send API request to clear notifications
            console.log('Clearing all notifications');
            
            // Show empty state
            document.getElementById('notifications-list').classList.add('hidden');
            document.getElementById('empty-state').classList.remove('hidden');
            
            // Update counts
            updateUnreadCount();
        }
    }
    
    function loadMoreNotifications() {
        // In production: Load more notifications via AJAX
        console.log('Loading more notifications...');
        
        // Show loading state
        const button = event.target;
        const originalText = button.textContent;
        button.textContent = 'Loading...';
        button.disabled = true;
        
        // Simulate API call
        setTimeout(() => {
            // Add more notifications (in production, this would come from server)
            console.log('More notifications loaded');
            
            // Reset button
            button.textContent = originalText;
            button.disabled = false;
            
            // In production: Append new notifications to the list
        }, 1000);
    }
    
    function saveNotificationSettings() {
        // In production: Save notification settings via API
        console.log('Saving notification settings...');
        
        // Show success message
        alert('Notification settings saved successfully!');
    }
    
    function updateUnreadCount() {
        const unreadCount = document.querySelectorAll('.notification-unread').length;
        console.log('Unread notifications:', unreadCount);
        
        // Update tab count (in production, update all tab counts)
        const unreadTab = document.getElementById('tab-unread');
        const countSpan = unreadTab.querySelector('span');
        countSpan.textContent = unreadCount;
    }
</script>
@endpush