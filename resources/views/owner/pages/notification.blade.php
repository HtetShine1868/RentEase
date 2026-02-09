@extends('owner.layout.owner-layout')

@section('title', 'Notifications - RentEase')
@section('page-title', 'Notifications')
@section('page-subtitle', 'Stay updated with system alerts and messages')

@section('content')
<div class="space-y-6">
    @include('owner.components.validation-messages')
    @include('owner.components.empty-states')

    <!-- Header with Actions -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
                <p class="text-gray-600 mt-1">Important updates, alerts, and system messages</p>
            </div>
            
            <div class="flex items-center gap-3">
                <!-- Mark All as Read -->
                <button onclick="markAllAsRead()" 
                        class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                    <i class="fas fa-check-double mr-2"></i> Mark All Read
                </button>
                
                <!-- Clear All -->
                <button onclick="clearAllNotifications()" 
                        class="px-4 py-2.5 border border-red-300 text-red-700 bg-red-50 rounded-lg font-medium hover:bg-red-100 transition-colors">
                    <i class="fas fa-trash mr-2"></i> Clear All
                </button>
                
                <!-- Settings -->
                <button onclick="openNotificationSettings()" 
                        class="w-10 h-10 flex items-center justify-center border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-cog"></i>
                </button>
            </div>
        </div>
        
        <!-- Stats Tabs -->
        <div class="grid grid-cols-4 gap-4 mt-6">
            <button class="tab-btn active" data-filter="all">
                <div class="text-center">
                    <p class="text-sm text-gray-600">All</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">24</p>
                </div>
            </button>
            
            <button class="tab-btn" data-filter="unread">
                <div class="text-center">
                    <p class="text-sm text-gray-600">Unread</p>
                    <p class="text-2xl font-bold text-purple-600 mt-1">8</p>
                </div>
            </button>
            
            <button class="tab-btn" data-filter="important">
                <div class="text-center">
                    <p class="text-sm text-gray-600">Important</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">3</p>
                </div>
            </button>
            
            <button class="tab-btn" data-filter="archived">
                <div class="text-center">
                    <p class="text-sm text-gray-600">Archived</p>
                    <p class="text-2xl font-bold text-gray-600 mt-1">12</p>
                </div>
            </button>
        </div>
    </div>

    <!-- Notifications Container -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <!-- Date Filter -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <select class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="today">Today</option>
                        <option value="week" selected>Last 7 days</option>
                        <option value="month">Last 30 days</option>
                        <option value="all">All Time</option>
                    </select>
                    
                    <div class="hidden md:flex items-center gap-2">
                        <span class="text-sm text-gray-600">Filter by:</span>
                        <div class="flex gap-1">
                            <button class="px-3 py-1 text-xs font-medium rounded-lg border border-purple-300 bg-purple-50 text-purple-700">
                                All Types
                            </button>
                            <button class="px-3 py-1 text-xs font-medium rounded-lg border border-gray-300 hover:bg-gray-50">
                                Bookings
                            </button>
                            <button class="px-3 py-1 text-xs font-medium rounded-lg border border-gray-300 hover:bg-gray-50">
                                Payments
                            </button>
                            <button class="px-3 py-1 text-xs font-medium rounded-lg border border-gray-300 hover:bg-gray-50">
                                System
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center gap-2">
                    <button class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-sort-amount-down"></i>
                    </button>
                    <button class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="divide-y divide-gray-200 max-h-[700px] overflow-y-auto" id="notifications-list">
            <!-- Today Section -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700">
                    <i class="far fa-sun mr-2 text-yellow-500"></i> Today
                </h3>
            </div>

            <!-- Notification 1 - New Booking (Unread) -->
            <div class="notification-item bg-purple-50 border-l-4 border-purple-500" data-id="1" data-type="booking" data-important="true">
                <div class="px-6 py-4">
                    <div class="flex items-start gap-4">
                        <!-- Icon -->
                        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center">
                            <i class="fas fa-calendar-plus text-purple-600 text-lg"></i>
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-3">
                                    <h4 class="text-base font-semibold text-gray-900">New Booking Received</h4>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                                        <i class="fas fa-star mr-1"></i> Important
                                    </span>
                                    <span class="unread-dot"></span>
                                </div>
                                <span class="text-sm text-gray-500">10:30 AM</span>
                            </div>
                            
                            <p class="text-gray-600 mb-3">
                                <strong>John Doe</strong> booked <strong>Sunshine Apartments - Unit 302</strong> for 30 days starting Feb 1, 2024.
                            </p>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="px-3 py-1 text-xs font-medium rounded-lg bg-green-100 text-green-800">
                                        <i class="fas fa-dollar-sign mr-1"></i> $1,250.00
                                    </span>
                                    <span class="text-sm text-gray-500">
                                        <i class="far fa-clock mr-1"></i> Requires confirmation
                                    </span>
                                </div>
                                
                                <div class="flex items-center gap-2">
                                    <button onclick="viewNotification(1)" 
                                            class="px-3 py-1.5 text-sm font-medium text-purple-700 bg-white border border-purple-300 rounded-lg hover:bg-purple-50 transition-colors">
                                        View Booking
                                    </button>
                                    <button onclick="markAsRead(1)" 
                                            class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg"
                                            title="Mark as read">
                                        <i class="far fa-check-circle"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notification 2 - Payment Received (Unread) -->
            <div class="notification-item bg-green-50 border-l-4 border-green-500" data-id="2" data-type="payment" data-important="false">
                <div class="px-6 py-4">
                    <div class="flex items-start gap-4">
                        <!-- Icon -->
                        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                            <i class="fas fa-money-check-alt text-green-600 text-lg"></i>
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-3">
                                    <h4 class="text-base font-semibold text-gray-900">Payment Received</h4>
                                    <span class="unread-dot"></span>
                                </div>
                                <span class="text-sm text-gray-500">09:15 AM</span>
                            </div>
                            
                            <p class="text-gray-600 mb-3">
                                Payment of <strong>$450.00</strong> received from <strong>Sarah Smith</strong> for <strong>City Hostel - Room 101</strong>.
                            </p>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="text-sm text-gray-600">
                                        <i class="fas fa-receipt mr-1"></i> Transaction: TXN-001233
                                    </span>
                                    <span class="text-sm text-gray-600">
                                        <i class="fas fa-percentage mr-1"></i> Commission: $22.50
                                    </span>
                                </div>
                                
                                <div class="flex items-center gap-2">
                                    <button onclick="viewNotification(2)" 
                                            class="px-3 py-1.5 text-sm font-medium text-green-700 bg-white border border-green-300 rounded-lg hover:bg-green-50 transition-colors">
                                        View Receipt
                                    </button>
                                    <button onclick="markAsRead(2)" 
                                            class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
                                        <i class="far fa-check-circle"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notification 3 - Maintenance Alert (Read) -->
            <div class="notification-item" data-id="3" data-type="maintenance" data-important="true">
                <div class="px-6 py-4">
                    <div class="flex items-start gap-4">
                        <!-- Icon -->
                        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-yellow-100 flex items-center justify-center">
                            <i class="fas fa-tools text-yellow-600 text-lg"></i>
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-3">
                                    <h4 class="text-base font-semibold text-gray-900">Maintenance Request</h4>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> Urgent
                                    </span>
                                </div>
                                <span class="text-sm text-gray-500">08:45 AM</span>
                            </div>
                            
                            <p class="text-gray-600 mb-3">
                                <strong>Water leakage</strong> reported in <strong>Room 101 - City Hostel</strong>. Requires immediate attention.
                            </p>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="px-3 py-1 text-xs font-medium rounded-lg bg-red-100 text-red-800">
                                        <i class="fas fa-clock mr-1"></i> High Priority
                                    </span>
                                    <span class="text-sm text-gray-600">
                                        <i class="fas fa-user mr-1"></i> Reported by: John Doe
                                    </span>
                                </div>
                                
                                <div class="flex items-center gap-2">
                                    <button onclick="viewNotification(3)" 
                                            class="px-3 py-1.5 text-sm font-medium text-yellow-700 bg-white border border-yellow-300 rounded-lg hover:bg-yellow-50 transition-colors">
                                        View Details
                                    </button>
                                    <button onclick="archiveNotification(3)" 
                                            class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
                                        <i class="far fa-folder"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Yesterday Section -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700">
                    <i class="far fa-moon mr-2 text-indigo-500"></i> Yesterday
                </h3>
            </div>

            <!-- Notification 4 - Review Received (Read) -->
            <div class="notification-item" data-id="4" data-type="review" data-important="false">
                <div class="px-6 py-4">
                    <div class="flex items-start gap-4">
                        <!-- Icon -->
                        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-star text-blue-600 text-lg"></i>
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-base font-semibold text-gray-900">New Property Review</h4>
                                <span class="text-sm text-gray-500">Yesterday, 4:20 PM</span>
                            </div>
                            
                            <p class="text-gray-600 mb-3">
                                <strong>Mike Johnson</strong> left a <strong>5-star review</strong> for <strong>Sunshine Apartments</strong>.
                            </p>
                            
                            <div class="flex items-center gap-3 mb-3">
                                <div class="flex items-center text-yellow-400">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <span class="text-sm text-gray-600">"Excellent location and clean apartment!"</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <button onclick="viewNotification(4)" 
                                        class="px-3 py-1.5 text-sm font-medium text-blue-700 bg-white border border-blue-300 rounded-lg hover:bg-blue-50 transition-colors">
                                    View Review
                                </button>
                                <div class="flex items-center gap-2">
                                    <button onclick="archiveNotification(4)" 
                                            class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
                                        <i class="far fa-folder"></i>
                                    </button>
                                    <button onclick="deleteNotification(4)" 
                                            class="p-2 text-gray-500 hover:text-red-700 hover:bg-red-50 rounded-lg">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notification 5 - System Update (Read) -->
            <div class="notification-item" data-id="5" data-type="system" data-important="false">
                <div class="px 6 py-4">
                    <div class="flex items-start gap-4">
                        <!-- Icon -->
                        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center">
                            <i class="fas fa-cogs text-indigo-600 text-lg"></i>
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-base font-semibold text-gray-900">System Maintenance</h4>
                                <span class="text-sm text-gray-500">Yesterday, 11:30 AM</span>
                            </div>
                            
                            <p class="text-gray-600 mb-3">
                                Scheduled maintenance on <strong>January 20, 2024</strong> from <strong>2:00 AM to 4:00 AM</strong>. System may be temporarily unavailable.
                            </p>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">
                                    <i class="fas fa-info-circle mr-1"></i> No action required
                                </span>
                                <button onclick="archiveNotification(5)" 
                                        class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                    Dismiss
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Last Week Section -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700">
                    <i class="far fa-calendar-alt mr-2 text-purple-500"></i> Last Week
                </h3>
            </div>

            <!-- Notification 6 - Booking Cancellation (Archived) -->
            <div class="notification-item bg-gray-50 opacity-75" data-id="6" data-type="booking" data-important="false" data-archived="true">
                <div class="px-6 py-4">
                    <div class="flex items-start gap-4">
                        <!-- Icon -->
                        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-calendar-times text-gray-500 text-lg"></i>
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-base font-semibold text-gray-600">Booking Cancelled</h4>
                                <span class="text-sm text-gray-500">Jan 10, 2024</span>
                            </div>
                            
                            <p class="text-gray-500 mb-3">
                                <strong>Emma Wilson</strong> cancelled booking for <strong>Luxury Villa</strong> starting Mar 1, 2024.
                            </p>
                            
                            <div class="flex items-center justify-between">
                                <span class="px-3 py-1 text-xs font-medium rounded-lg bg-gray-200 text-gray-700">
                                    <i class="fas fa-undo mr-1"></i> Refunded: $2,800.00
                                </span>
                                <span class="text-xs text-gray-500 italic">Archived</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State (Hidden by default) -->
        <div id="empty-notifications" class="hidden">
            <div class="text-center py-16">
                <div class="mx-auto w-48 h-48 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-8">
                    <i class="far fa-bell text-gray-400 text-6xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">No Notifications</h3>
                <p class="text-gray-600 max-w-md mx-auto mb-8">
                    You're all caught up! When you have new notifications, they'll appear here.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button onclick="checkForNewNotifications()" 
                            class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white px-8 py-3 rounded-lg font-medium hover:from-purple-700 hover:to-purple-800 transition-all">
                        <i class="fas fa-sync-alt"></i>
                        Check for New
                    </button>
                    <button onclick="openNotificationSettings()" 
                            class="inline-flex items-center justify-center gap-2 border border-gray-300 text-gray-700 px-8 py-3 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                        <i class="fas fa-cog"></i>
                        Notification Settings
                    </button>
                </div>
            </div>
        </div>

        <!-- Load More -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="text-center">
                <button onclick="loadMoreNotifications()" 
                        class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                    <i class="fas fa-history mr-2"></i> Load Older Notifications
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Notification Settings Modal -->
<div id="settings-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Notification Settings</h3>
                <button type="button" 
                        onclick="closeSettingsModal()"
                        class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="space-y-6">
                <!-- Email Notifications -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Email Notifications</h4>
                    <div class="space-y-3">
                        <label class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <div>
                                <p class="font-medium text-gray-900">New Bookings</p>
                                <p class="text-sm text-gray-500">Get notified when someone books your property</p>
                            </div>
                            <div class="relative">
                                <input type="checkbox" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                            </div>
                        </label>
                        
                        <label class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <div>
                                <p class="font-medium text-gray-900">Payment Receipts</p>
                                <p class="text-sm text-gray-500">Receive payment confirmations and receipts</p>
                            </div>
                            <div class="relative">
                                <input type="checkbox" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                            </div>
                        </label>
                        
                        <label class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <div>
                                <p class="font-medium text-gray-900">Maintenance Alerts</p>
                                <p class="text-sm text-gray-500">Get notified about maintenance requests</p>
                            </div>
                            <div class="relative">
                                <input type="checkbox" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Push Notifications -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Push Notifications</h4>
                    <div class="space-y-3">
                        <label class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <div>
                                <p class="font-medium text-gray-900">Important Alerts</p>
                                <p class="text-sm text-gray-500">Receive critical alerts on your device</p>
                            </div>
                            <div class="relative">
                                <input type="checkbox" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                            </div>
                        </label>
                        
                        <label class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <div>
                                <p class="font-medium text-gray-900">Booking Reminders</p>
                                <p class="text-sm text-gray-500">Get reminders for upcoming bookings</p>
                            </div>
                            <div class="relative">
                                <input type="checkbox" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Notification Frequency -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Notification Frequency</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="frequency" value="realtime" class="sr-only peer" checked>
                            <div class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-purple-500 peer-checked:bg-purple-50 text-center">
                                <i class="fas fa-bolt text-purple-600 text-xl mb-2"></i>
                                <p class="font-medium">Real-time</p>
                                <p class="text-xs text-gray-500">Immediate alerts</p>
                            </div>
                        </label>
                        
                        <label class="relative cursor-pointer">
                            <input type="radio" name="frequency" value="daily" class="sr-only peer">
                            <div class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-purple-500 peer-checked:bg-purple-50 text-center">
                                <i class="far fa-sun text-purple-600 text-xl mb-2"></i>
                                <p class="font-medium">Daily Digest</p>
                                <p class="text-xs text-gray-500">Once per day</p>
                            </div>
                        </label>
                        
                        <label class="relative cursor-pointer">
                            <input type="radio" name="frequency" value="weekly" class="sr-only peer">
                            <div class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-purple-500 peer-checked:bg-purple-50 text-center">
                                <i class="far fa-calendar text-purple-600 text-xl mb-2"></i>
                                <p class="font-medium">Weekly Summary</p>
                                <p class="text-xs text-gray-500">Every Monday</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                <button type="button" 
                        onclick="closeSettingsModal()"
                        class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50">
                    Cancel
                </button>
                <button type="button" 
                        onclick="saveNotificationSettings()"
                        class="px-6 py-3 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700">
                    Save Settings
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Notification Management Functions
function viewNotification(id) {
    Loading.show('Opening notification...');
    setTimeout(() => {
        Toast.success('Notification Opened', `Opening notification #${id}`);
        // In real app, this would redirect to relevant page
        markAsRead(id); // Auto-mark as read when opened
        Loading.hide();
    }, 500);
}

function markAsRead(id) {
    const notification = document.querySelector(`.notification-item[data-id="${id}"]`);
    if (notification) {
        // Remove unread styles
        notification.classList.remove('bg-purple-50', 'bg-green-50', 'bg-yellow-50');
        notification.classList.remove('border-l-4', 'border-purple-500', 'border-green-500', 'border-yellow-500');
        
        // Remove unread dot
        const unreadDot = notification.querySelector('.unread-dot');
        if (unreadDot) unreadDot.remove();
        
        // Update counter
        updateUnreadCount();
        
        Toast.success('Marked as Read', 'Notification marked as read.');
    }
}

function markAllAsRead() {
    ConfirmationModal.show(
        'Mark All as Read',
        'Are you sure you want to mark all notifications as read?',
        'Mark All Read',
        () => {
            Loading.show('Marking all as read...');
            
            const notifications = document.querySelectorAll('.notification-item');
            notifications.forEach(notification => {
                notification.classList.remove('bg-purple-50', 'bg-green-50', 'bg-yellow-50');
                notification.classList.remove('border-l-4', 'border-purple-500', 'border-green-500', 'border-yellow-500');
                
                const unreadDot = notification.querySelector('.unread-dot');
                if (unreadDot) unreadDot.remove();
            });
            
            setTimeout(() => {
                updateUnreadCount();
                Loading.hide();
                Toast.success('All Read', 'All notifications marked as read.');
            }, 800);
        }
    );
}

function clearAllNotifications() {
    ConfirmationModal.show(
        'Clear All Notifications',
        'This will permanently delete all notifications. This action cannot be undone.',
        'Clear All',
        () => {
            Loading.show('Clearing notifications...');
            
            // Show empty state
            document.getElementById('notifications-list').classList.add('hidden');
            document.getElementById('empty-notifications').classList.remove('hidden');
            
            setTimeout(() => {
                Loading.hide();
                Toast.success('Cleared', 'All notifications have been cleared.');
            }, 1000);
        }
    );
}

function archiveNotification(id) {
    const notification = document.querySelector(`.notification-item[data-id="${id}"]`);
    if (notification) {
        notification.classList.add('bg-gray-50', 'opacity-75');
        notification.setAttribute('data-archived', 'true');
        
        // Add archived badge
        const contentDiv = notification.querySelector('.flex-1.min-w-0');
        if (contentDiv) {
            const existingBadge = contentDiv.querySelector('.text-xs.text-gray-500.italic');
            if (!existingBadge) {
                const badge = document.createElement('span');
                badge.className = 'text-xs text-gray-500 italic ml-2';
                badge.textContent = 'Archived';
                
                const timeSpan = notification.querySelector('.text-sm.text-gray-500:last-child');
                if (timeSpan) {
                    timeSpan.parentNode.insertBefore(badge, timeSpan.nextSibling);
                }
            }
        }
        
        Toast.info('Archived', 'Notification moved to archive.');
    }
}

function deleteNotification(id) {
    ConfirmationModal.show(
        'Delete Notification',
        'Are you sure you want to delete this notification?',
        'Delete',
        () => {
            const notification = document.querySelector(`.notification-item[data-id="${id}"]`);
            if (notification) {
                notification.style.opacity = '0.5';
                notification.style.transform = 'translateX(-20px)';
                
                setTimeout(() => {
                    notification.remove();
                    checkEmptyState();
                    Toast.success('Deleted', 'Notification deleted successfully.');
                }, 300);
            }
        }
    );
}

function updateUnreadCount() {
    const unreadCount = document.querySelectorAll('.unread-dot').length;
    const unreadTab = document.querySelector('[data-filter="unread"]');
    if (unreadTab) {
        unreadTab.querySelector('.text-2xl').textContent = unreadCount;
        unreadTab.querySelector('.text-2xl').className = `text-2xl font-bold ${unreadCount > 0 ? 'text-purple-600' : 'text-gray-900'} mt-1`;
    }
}

function checkEmptyState() {
    const notifications = document.querySelectorAll('.notification-item').length;
    if (notifications === 0) {
        document.getElementById('notifications-list').classList.add('hidden');
        document.getElementById('empty-notifications').classList.remove('hidden');
    }
}

function checkForNewNotifications() {
    Loading.show('Checking for new notifications...');
    setTimeout(() => {
        Toast.info('No New Notifications', 'You have no new notifications at this time.');
        Loading.hide();
    }, 1500);
}

function loadMoreNotifications() {
    Loading.show('Loading more notifications...');
    setTimeout(() => {
        // In real app, this would load more notifications from API
        Toast.info('Loaded', 'Older notifications loaded.');
        Loading.hide();
    }, 1000);
}

// Settings Modal Functions
function openNotificationSettings() {
    const modal = document.getElementById('settings-modal');
    modal.classList.remove('hidden');
    
    // Animate in
    setTimeout(() => {
        modal.querySelector('.bg-white').classList.add('modal-show');
    }, 10);
}

function closeSettingsModal() {
    const modal = document.getElementById('settings-modal');
    modal.querySelector('.bg-white').classList.remove('modal-show');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

function saveNotificationSettings() {
    Loading.show('Saving settings...');
    setTimeout(() => {
        closeSettingsModal();
        Toast.success('Settings Saved', 'Notification preferences updated successfully.');
        Loading.hide();
    }, 1000);
}

// Filter Functionality
document.addEventListener('DOMContentLoaded', function() {
    // Tab filtering
    const tabButtons = document.querySelectorAll('.tab-btn');
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all tabs
            tabButtons.forEach(btn => btn.classList.remove('active', 'border-b-2', 'border-purple-500'));
            
            // Add active class to clicked tab
            this.classList.add('active', 'border-b-2', 'border-purple-500');
            
            // Filter notifications
            const filter = this.getAttribute('data-filter');
            filterNotifications(filter);
        });
    });
    
    // Type filter buttons
    const typeButtons = document.querySelectorAll('.flex.gap-1 button');
    typeButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            typeButtons.forEach(btn => {
                btn.classList.remove('border-purple-300', 'bg-purple-50', 'text-purple-700');
                btn.classList.add('border-gray-300', 'hover:bg-gray-50');
            });
            
            // Add active class to clicked button
            this.classList.remove('border-gray-300', 'hover:bg-gray-50');
            this.classList.add('border-purple-300', 'bg-purple-50', 'text-purple-700');
            
            // Filter by type
            const type = this.textContent.toLowerCase();
            filterByType(type);
        });
    });
    
    // Date filter
    const dateSelect = document.querySelector('select');
    dateSelect.addEventListener('change', function() {
        Toast.info('Filter Applied', `Showing notifications from ${this.value}`);
    });
});

function filterNotifications(filter) {
    const notifications = document.querySelectorAll('.notification-item');
    
    notifications.forEach(notification => {
        switch(filter) {
            case 'all':
                notification.classList.remove('hidden');
                break;
            case 'unread':
                if (notification.querySelector('.unread-dot')) {
                    notification.classList.remove('hidden');
                } else {
                    notification.classList.add('hidden');
                }
                break;
            case 'important':
                if (notification.getAttribute('data-important') === 'true') {
                    notification.classList.remove('hidden');
                } else {
                    notification.classList.add('hidden');
                }
                break;
            case 'archived':
                if (notification.getAttribute('data-archived') === 'true') {
                    notification.classList.remove('hidden');
                } else {
                    notification.classList.add('hidden');
                }
                break;
        }
    });
}

function filterByType(type) {
    const notifications = document.querySelectorAll('.notification-item');
    
    notifications.forEach(notification => {
        if (type === 'all types' || notification.getAttribute('data-type') === type) {
            notification.classList.remove('hidden');
        } else {
            notification.classList.add('hidden');
        }
    });
}
</script>

<style>
/* Notifications Styles */
.notification-item {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.notification-item:hover {
    background-color: #f8fafc;
    transform: translateX(4px);
}

/* Unread dot */
.unread-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    background-color: #8b5cf6;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.5;
        transform: scale(1.1);
    }
}

/* Active tab styling */
.tab-btn {
    padding: 0.75rem;
    border-radius: 0.5rem;
    transition: all 0.2s;
    cursor: pointer;
}

.tab-btn:hover {
    background-color: #f3f4f6;
}

.tab-btn.active {
    background-color: #f5f3ff;
    border-bottom: 2px solid #8b5cf6;
}

/* Modal animation */
.modal-show {
    animation: modalSlideIn 0.3s ease-out forwards;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Scrollbar styling */
#notifications-list::-webkit-scrollbar {
    width: 6px;
}

#notifications-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

#notifications-list::-webkit-scrollbar-thumb {
    background: #c7d2fe;
    border-radius: 3px;
}

#notifications-list::-webkit-scrollbar-thumb:hover {
    background: #a5b4fc;
}

/* Toggle switch styling */
input:checked + div {
    background-color: #8b5cf6;
}

input:checked + div:after {
    transform: translateX(1.5rem);
}

/* Date section headers */
.bg-gray-50 h3 {
    position: relative;
}

.bg-gray-50 h3::before {
    content: '';
    position: absolute;
    left: -1rem;
    top: 50%;
    transform: translateY(-50%);
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background-color: currentColor;
}

/* Icon animations */
.fa-bell {
    animation: gentleRing 3s ease-in-out infinite;
}

@keyframes gentleRing {
    0%, 100% {
        transform: rotate(0deg);
    }
    5%, 15% {
        transform: rotate(15deg);
    }
    10%, 20% {
        transform: rotate(-15deg);
    }
    25% {
        transform: rotate(0deg);
    }
}

/* Empty state animation */
.empty-state-icon {
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

/* Responsive design */
@media (max-width: 768px) {
    .notification-item {
        padding: 1rem;
    }
    
    .flex.items-center.justify-between {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .flex.items-center.gap-3 {
        flex-wrap: wrap;
    }
}

/* Loading skeleton */
.loading-skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}

/* Status colors */
.bg-purple-50 {
    border-left-color: #8b5cf6;
}

.bg-green-50 {
    border-left-color: #10b981;
}

.bg-yellow-50 {
    border-left-color: #f59e0b;
}

.bg-blue-50 {
    border-left-color: #3b82f6;
}

.bg-red-50 {
    border-left-color: #ef4444;
}
</style>
@endsection