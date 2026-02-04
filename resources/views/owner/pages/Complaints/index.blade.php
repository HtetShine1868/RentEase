@extends('owner.layout.owner-layout')

@section('title', 'Complaint Management - RentEase')
@section('page-title', 'Complaint Management')
@section('page-subtitle', 'Manage and resolve customer complaints')

@section('content')
<div class="space-y-6">
    @include('owner.components.validation-messages')
    @include('owner.components.empty-states')

    <!-- Header with Stats -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Complaint Management</h1>
                <p class="text-gray-600 mt-1">Address and resolve customer complaints efficiently</p>
            </div>
            
            <!-- Quick Stats -->
            <div class="flex gap-6">
                <div class="text-center">
                    <p class="text-sm text-gray-500">Total</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">24</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-500">Pending</p>
                    <p class="text-2xl font-bold text-yellow-600 mt-1">8</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-500">Resolved</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">14</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-500">Escalated</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">2</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Complaint List -->
        <div class="lg:col-span-2">
            <!-- Filters -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">All Complaints</h2>
                    
                    <div class="flex items-center gap-3">
                        <!-- Filter by Status -->
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600">Filter:</span>
                            <div class="flex gap-1">
                                <button class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 hover:bg-gray-50 bg-white text-gray-700">
                                    All
                                </button>
                                <button class="px-3 py-1.5 text-xs font-medium rounded-lg border border-yellow-300 bg-yellow-50 text-yellow-700">
                                    Pending
                                </button>
                                <button class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 hover:bg-gray-50 bg-white text-gray-700">
                                    In Progress
                                </button>
                                <button class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 hover:bg-gray-50 bg-white text-gray-700">
                                    Resolved
                                </button>
                            </div>
                        </div>
                        
                        <!-- Sort Button -->
                        <button class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                            <i class="fas fa-sort-amount-down mr-2"></i> Sort
                        </button>
                    </div>
                </div>

                <!-- Search -->
                <div class="relative mb-4">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="search" 
                           placeholder="Search complaints by user, property, or issue..." 
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>

                <!-- Complaint List -->
                <div class="space-y-4 max-h-[600px] overflow-y-auto pr-2" id="complaint-list">
                    <!-- Complaint Item 1 - Pending -->
                    <div class="bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md transition-all duration-200 cursor-pointer complaint-item"
                         data-id="1"
                         onclick="selectComplaint(1)">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i> Pending
                                    </span>
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> High Priority
                                    </span>
                                    <span class="text-xs text-gray-500 ml-auto">
                                        <i class="far fa-clock mr-1"></i> 2 hours ago
                                    </span>
                                </div>
                                
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Water leakage in bathroom</h3>
                                
                                <div class="flex items-center gap-4 text-sm text-gray-600 mb-3">
                                    <span class="flex items-center">
                                        <i class="fas fa-user mr-2 text-gray-400"></i>
                                        John Doe • Room 101
                                    </span>
                                    <span class="flex items-center">
                                        <i class="fas fa-building mr-2 text-gray-400"></i>
                                        City Hostel
                                    </span>
                                    <span class="flex items-center">
                                        <i class="fas fa-calendar-alt mr-2 text-gray-400"></i>
                                        Complaint #CMP-001
                                    </span>
                                </div>
                                
                                <p class="text-gray-600 line-clamp-2 mb-3">
                                    Water is leaking from the ceiling in the bathroom. It's creating a puddle on the floor and the ceiling paint is peeling. Needs immediate attention.
                                </p>
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center">
                                            <div class="w-6 h-6 rounded-full bg-purple-100 flex items-center justify-center mr-2">
                                                <i class="fas fa-camera text-purple-600 text-xs"></i>
                                            </div>
                                            <span class="text-xs text-gray-600">3 photos</span>
                                        </div>
                                        <div class="flex items-center">
                                            <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center mr-2">
                                                <i class="far fa-comment text-blue-600 text-xs"></i>
                                            </div>
                                            <span class="text-xs text-gray-600">2 replies</span>
                                        </div>
                                    </div>
                                    
                                    <button class="px-3 py-1.5 text-sm font-medium text-purple-700 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                                        Take Action
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Complaint Item 2 - In Progress -->
                    <div class="bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md transition-all duration-200 cursor-pointer complaint-item"
                         data-id="2"
                         onclick="selectComplaint(2)">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                        <i class="fas fa-tools mr-1"></i> In Progress
                                    </span>
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-800">
                                        <i class="fas fa-exclamation-circle mr-1"></i> Medium Priority
                                    </span>
                                    <span class="text-xs text-gray-500 ml-auto">
                                        <i class="far fa-clock mr-1"></i> 1 day ago
                                    </span>
                                </div>
                                
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Air conditioner not working</h3>
                                
                                <div class="flex items-center gap-4 text-sm text-gray-600 mb-3">
                                    <span class="flex items-center">
                                        <i class="fas fa-user mr-2 text-gray-400"></i>
                                        Sarah Smith • Unit 302
                                    </span>
                                    <span class="flex items-center">
                                        <i class="fas fa-home mr-2 text-gray-400"></i>
                                        Sunshine Apartments
                                    </span>
                                    <span class="flex items-center">
                                        <i class="fas fa-calendar-alt mr-2 text-gray-400"></i>
                                        Complaint #CMP-002
                                    </span>
                                </div>
                                
                                <p class="text-gray-600 line-clamp-2 mb-3">
                                    The AC unit in the living room stopped working yesterday. Room temperature is uncomfortable, especially during daytime.
                                </p>
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center">
                                            <div class="w-6 h-6 rounded-full bg-purple-100 flex items-center justify-center mr-2">
                                                <i class="fas fa-camera text-purple-600 text-xs"></i>
                                            </div>
                                            <span class="text-xs text-gray-600">1 photo</span>
                                        </div>
                                        <div class="flex items-center">
                                            <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center mr-2">
                                                <i class="far fa-comment text-blue-600 text-xs"></i>
                                            </div>
                                            <span class="text-xs text-gray-600">4 replies</span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-gray-500">Assigned to:</span>
                                        <div class="flex items-center">
                                            <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center mr-1">
                                                <i class="fas fa-user-cog text-green-600 text-xs"></i>
                                            </div>
                                            <span class="text-xs font-medium text-gray-700">Maintenance Team</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Complaint Item 3 - Resolved -->
                    <div class="bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md transition-all duration-200 cursor-pointer complaint-item"
                         data-id="3"
                         onclick="selectComplaint(3)">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i> Resolved
                                    </span>
                                    <span class="text-xs text-gray-500 ml-auto">
                                        <i class="far fa-clock mr-1"></i> 3 days ago
                                    </span>
                                </div>
                                
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">WiFi connectivity issues</h3>
                                
                                <div class="flex items-center gap-4 text-sm text-gray-600 mb-3">
                                    <span class="flex items-center">
                                        <i class="fas fa-user mr-2 text-gray-400"></i>
                                        Mike Johnson • Room 105
                                    </span>
                                    <span class="flex items-center">
                                        <i class="fas fa-bed mr-2 text-gray-400"></i>
                                        City Hostel
                                    </span>
                                    <span class="flex items-center">
                                        <i class="fas fa-calendar-alt mr-2 text-gray-400"></i>
                                        Complaint #CMP-003
                                    </span>
                                </div>
                                
                                <p class="text-gray-600 line-clamp-2 mb-3">
                                    Internet connection keeps dropping in my room. Unable to attend online classes properly.
                                </p>
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center">
                                            <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center mr-2">
                                                <i class="far fa-comment text-blue-600 text-xs"></i>
                                            </div>
                                            <span class="text-xs text-gray-600">6 replies</span>
                                        </div>
                                        <div class="flex items-center text-green-600">
                                            <i class="fas fa-star text-sm mr-1"></i>
                                            <span class="text-xs font-medium">4.5/5 Rating</span>
                                        </div>
                                    </div>
                                    
                                    <button class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                        View Resolution
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Complaint Item 4 - Escalated -->
                    <div class="bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md transition-all duration-200 cursor-pointer complaint-item"
                         data-id="4"
                         onclick="selectComplaint(4)">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-flag mr-1"></i> Escalated
                                    </span>
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> High Priority
                                    </span>
                                    <span class="text-xs text-gray-500 ml-auto">
                                        <i class="far fa-clock mr-1"></i> 5 days ago
                                    </span>
                                </div>
                                
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Noise complaint from neighbors</h3>
                                
                                <div class="flex items-center gap-4 text-sm text-gray-600 mb-3">
                                    <span class="flex items-center">
                                        <i class="fas fa-user mr-2 text-gray-400"></i>
                                        Emma Wilson • Unit 401
                                    </span>
                                    <span class="flex items-center">
                                        <i class="fas fa-home mr-2 text-gray-400"></i>
                                        Luxury Villa
                                    </span>
                                    <span class="flex items-center">
                                        <i class="fas fa-calendar-alt mr-2 text-gray-400"></i>
                                        Complaint #CMP-004
                                    </span>
                                </div>
                                
                                <p class="text-gray-600 line-clamp-2 mb-3">
                                    Loud music from adjacent unit every night. Affecting sleep and work. Multiple complaints already made.
                                </p>
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center">
                                            <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center mr-2">
                                                <i class="far fa-comment text-blue-600 text-xs"></i>
                                            </div>
                                            <span class="text-xs text-gray-600">8 replies</span>
                                        </div>
                                        <div class="flex items-center text-red-600">
                                            <i class="fas fa-exclamation-circle text-sm mr-1"></i>
                                            <span class="text-xs font-medium">Escalated to Admin</span>
                                        </div>
                                    </div>
                                    
                                    <button class="px-3 py-1.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                                        <i class="fas fa-headset mr-1"></i> Contact Admin
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Load More -->
                <div class="text-center mt-6 pt-6 border-t border-gray-200">
                    <button class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                        <i class="fas fa-sync-alt mr-2"></i> Load More Complaints
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Column: Complaint Detail View -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-gray-200 p-6 sticky top-6" id="complaint-detail">
                <!-- Default State -->
                <div id="default-detail" class="text-center py-12">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-comments text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-3">Select a Complaint</h3>
                    <p class="text-gray-500">Choose a complaint from the list to view details and take action</p>
                </div>

                <!-- Complaint Detail Content (Hidden by default) -->
                <div id="detail-content" class="hidden">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-6 pb-6 border-b border-gray-200">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900" id="detail-title">Water leakage in bathroom</h2>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800" id="detail-status">
                                    <i class="fas fa-clock mr-1"></i> Pending
                                </span>
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800" id="detail-priority">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> High Priority
                                </span>
                            </div>
                        </div>
                        <button onclick="closeDetail()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <!-- User & Property Info -->
                    <div class="bg-gray-50 rounded-xl p-4 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- User Info -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Complainant</h4>
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-purple-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900" id="detail-user">John Doe</p>
                                        <p class="text-sm text-gray-500" id="detail-room">Room 101</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Property Info -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Property</h4>
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-bed text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900" id="detail-property">City Hostel</p>
                                        <p class="text-sm text-gray-500" id="detail-id">Complaint #CMP-001</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Timeline -->
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Timeline</h4>
                            <div class="space-y-3">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-plus text-green-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Complaint Filed</p>
                                        <p class="text-xs text-gray-500">Jan 15, 2024 • 10:30 AM</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-user-cog text-blue-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Assigned to Maintenance</p>
                                        <p class="text-xs text-gray-500">Jan 15, 2024 • 11:15 AM</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Complaint Description -->
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Description</h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-700" id="detail-description">
                                Water is leaking from the ceiling in the bathroom. It's creating a puddle on the floor and the ceiling paint is peeling. Needs immediate attention.
                            </p>
                        </div>
                        
                        <!-- Attachments -->
                        <div class="mt-4">
                            <h5 class="text-sm font-medium text-gray-700 mb-2">Attachments</h5>
                            <div class="grid grid-cols-3 gap-2">
                                <div class="border border-gray-200 rounded-lg overflow-hidden">
                                    <div class="h-20 bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center">
                                        <i class="fas fa-image text-blue-400 text-xl"></i>
                                    </div>
                                    <div class="p-2">
                                        <p class="text-xs text-gray-600 truncate">leakage_1.jpg</p>
                                    </div>
                                </div>
                                <div class="border border-gray-200 rounded-lg overflow-hidden">
                                    <div class="h-20 bg-gradient-to-br from-green-50 to-green-100 flex items-center justify-center">
                                        <i class="fas fa-image text-green-400 text-xl"></i>
                                    </div>
                                    <div class="p-2">
                                        <p class="text-xs text-gray-600 truncate">damage_2.jpg</p>
                                    </div>
                                </div>
                                <div class="border border-gray-200 rounded-lg overflow-hidden">
                                    <div class="h-20 bg-gradient-to-br from-purple-50 to-purple-100 flex items-center justify-center">
                                        <i class="fas fa-video text-purple-400 text-xl"></i>
                                    </div>
                                    <div class="p-2">
                                        <p class="text-xs text-gray-600 truncate">video_1.mp4</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Conversation Thread -->
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-4">Conversation</h4>
                        <div class="space-y-4 max-h-64 overflow-y-auto pr-2" id="conversation-thread">
                            <!-- User Message -->
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center">
                                    <i class="fas fa-user text-purple-600 text-xs"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="bg-gray-100 rounded-lg p-3">
                                        <p class="text-sm text-gray-700">When can this be fixed? It's getting worse.</p>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">John Doe • 2 hours ago</p>
                                </div>
                            </div>
                            
                            <!-- Owner Reply -->
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                                    <i class="fas fa-user-tie text-green-600 text-xs"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="bg-green-50 border border-green-100 rounded-lg p-3">
                                        <p class="text-sm text-gray-700">We've assigned a plumber. They'll visit within 2 hours.</p>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">You • 1 hour ago</p>
                                </div>
                            </div>
                            
                            <!-- System Message -->
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-robot text-blue-600 text-xs"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-3">
                                        <p class="text-sm text-gray-700">Maintenance team has been notified and is on the way.</p>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">System • 45 minutes ago</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reply Box -->
                    <div class="border-t border-gray-200 pt-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Reply to Complaint</h4>
                        <div class="space-y-3">
                            <textarea id="reply-textarea" 
                                      rows="3" 
                                      placeholder="Type your response here..." 
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 resize-none"></textarea>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <button class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
                                        <i class="fas fa-paperclip"></i>
                                    </button>
                                    <button class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
                                        <i class="fas fa-image"></i>
                                    </button>
                                    <button class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
                                        <i class="fas fa-smile"></i>
                                    </button>
                                </div>
                                
                                <div class="flex items-center gap-3">
                                    <select class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                        <option value="">Update Status</option>
                                        <option value="in_progress">Mark as In Progress</option>
                                        <option value="resolved">Mark as Resolved</option>
                                        <option value="escalated">Escalate to Admin</option>
                                    </select>
                                    
                                    <button onclick="sendReply()" 
                                            class="px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition-colors">
                                        <i class="fas fa-paper-plane mr-2"></i> Send
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Quick Actions -->
                            <div class="grid grid-cols-2 gap-2 mt-4">
                                <button onclick="useQuickReply('We have assigned our maintenance team. They will contact you shortly.')"
                                        class="px-3 py-2 text-sm text-left text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                    <i class="fas fa-tools mr-1"></i> Assigned to Maintenance
                                </button>
                                <button onclick="useQuickReply('Thank you for reporting. We are looking into this matter.')"
                                        class="px-3 py-2 text-sm text-left text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                    <i class="fas fa-thumbs-up mr-1"></i> Acknowledged
                                </button>
                                <button onclick="useQuickReply('Can you please provide more details about the issue?')"
                                        class="px-3 py-2 text-sm text-left text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                    <i class="fas fa-question-circle mr-1"></i> Need More Info
                                </button>
                                <button onclick="useQuickReply('The issue has been resolved. Please confirm if everything is working now.')"
                                        class="px-3 py-2 text-sm text-left text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                    <i class="fas fa-check-circle mr-1"></i> Issue Resolved
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Complaint Management Functions
let selectedComplaintId = null;

function selectComplaint(complaintId) {
    selectedComplaintId = complaintId;
    
    // Remove active class from all items
    document.querySelectorAll('.complaint-item').forEach(item => {
        item.classList.remove('border-purple-500', 'bg-purple-50');
        item.classList.add('border-gray-200');
    });
    
    // Add active class to selected item
    const selectedItem = document.querySelector(`.complaint-item[data-id="${complaintId}"]`);
    if (selectedItem) {
        selectedItem.classList.remove('border-gray-200');
        selectedItem.classList.add('border-purple-500', 'bg-purple-50');
        
        // Scroll into view if needed
        selectedItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
    
    // Load complaint details
    loadComplaintDetails(complaintId);
}

function loadComplaintDetails(complaintId) {
    Loading.show('Loading complaint details...');
    
    // Hide default, show detail content
    document.getElementById('default-detail').classList.add('hidden');
    document.getElementById('detail-content').classList.remove('hidden');
    
    // Simulate API call with dummy data
    setTimeout(() => {
        // This would be replaced with actual API data
        const complaints = {
            1: {
                title: 'Water leakage in bathroom',
                status: 'Pending',
                priority: 'High',
                user: 'John Doe',
                room: 'Room 101',
                property: 'City Hostel',
                id: 'Complaint #CMP-001',
                description: 'Water is leaking from the ceiling in the bathroom. It\'s creating a puddle on the floor and the ceiling paint is peeling. Needs immediate attention.'
            },
            2: {
                title: 'Air conditioner not working',
                status: 'In Progress',
                priority: 'Medium',
                user: 'Sarah Smith',
                room: 'Unit 302',
                property: 'Sunshine Apartments',
                id: 'Complaint #CMP-002',
                description: 'The AC unit in the living room stopped working yesterday. Room temperature is uncomfortable, especially during daytime.'
            },
            3: {
                title: 'WiFi connectivity issues',
                status: 'Resolved',
                priority: 'Low',
                user: 'Mike Johnson',
                room: 'Room 105',
                property: 'City Hostel',
                id: 'Complaint #CMP-003',
                description: 'Internet connection keeps dropping in my room. Unable to attend online classes properly.'
            },
            4: {
                title: 'Noise complaint from neighbors',
                status: 'Escalated',
                priority: 'High',
                user: 'Emma Wilson',
                room: 'Unit 401',
                property: 'Luxury Villa',
                id: 'Complaint #CMP-004',
                description: 'Loud music from adjacent unit every night. Affecting sleep and work. Multiple complaints already made.'
            }
        };
        
        const complaint = complaints[complaintId];
        
        if (complaint) {
            // Update UI with complaint data
            document.getElementById('detail-title').textContent = complaint.title;
            document.getElementById('detail-status').innerHTML = `<i class="fas fa-${getStatusIcon(complaint.status)} mr-1"></i> ${complaint.status}`;
            document.getElementById('detail-status').className = `px-2.5 py-1 text-xs font-medium rounded-full ${getStatusClass(complaint.status)}`;
            document.getElementById('detail-priority').innerHTML = `<i class="fas fa-exclamation-${complaint.priority === 'High' ? 'triangle' : 'circle'} mr-1"></i> ${complaint.priority} Priority`;
            document.getElementById('detail-user').textContent = complaint.user;
            document.getElementById('detail-room').textContent = complaint.room;
            document.getElementById('detail-property').textContent = complaint.property;
            document.getElementById('detail-id').textContent = complaint.id;
            document.getElementById('detail-description').textContent = complaint.description;
        }
        
        Loading.hide();
        Toast.success('Details Loaded', `Complaint #${complaintId} details loaded successfully.`);
    }, 800);
}

function closeDetail() {
    selectedComplaintId = null;
    
    // Remove active class from all items
    document.querySelectorAll('.complaint-item').forEach(item => {
        item.classList.remove('border-purple-500', 'bg-purple-50');
        item.classList.add('border-gray-200');
    });
    
    // Hide detail, show default
    document.getElementById('detail-content').classList.add('hidden');
    document.getElementById('default-detail').classList.remove('hidden');
}

function sendReply() {
    const textarea = document.getElementById('reply-textarea');
    const message = textarea.value.trim();
    
    if (!message) {
        Toast.error('Empty Message', 'Please type a message before sending.');
        textarea.focus();
        return;
    }
    
    if (!selectedComplaintId) {
        Toast.error('No Complaint Selected', 'Please select a complaint first.');
        return;
    }
    
    Loading.show('Sending reply...');
    
    // Simulate API call
    setTimeout(() => {
        // Add message to conversation thread
        const thread = document.getElementById('conversation-thread');
        const newMessage = document.createElement('div');
        newMessage.className = 'flex items-start gap-3';
        newMessage.innerHTML = `
            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                <i class="fas fa-user-tie text-green-600 text-xs"></i>
            </div>
            <div class="flex-1">
                <div class="bg-green-50 border border-green-100 rounded-lg p-3">
                    <p class="text-sm text-gray-700">${message}</p>
                </div>
                <p class="text-xs text-gray-500 mt-1">You • Just now</p>
            </div>
        `;
        thread.appendChild(newMessage);
        
        // Clear textarea
        textarea.value = '';
        
        // Scroll to bottom
        thread.scrollTop = thread.scrollHeight;
        
        Loading.hide();
        Toast.success('Reply Sent', 'Your response has been sent successfully.');
        
        // Update status if changed
        const statusSelect = document.querySelector('select');
        if (statusSelect.value) {
            updateComplaintStatus(statusSelect.value);
            statusSelect.value = '';
        }
    }, 1000);
}

function useQuickReply(text) {
    const textarea = document.getElementById('reply-textarea');
    textarea.value = text;
    textarea.focus();
}

function updateComplaintStatus(status) {
    if (!selectedComplaintId) return;
    
    const statusMap = {
        'in_progress': { text: 'In Progress', icon: 'tools', class: 'bg-blue-100 text-blue-800' },
        'resolved': { text: 'Resolved', icon: 'check-circle', class: 'bg-green-100 text-green-800' },
        'escalated': { text: 'Escalated', icon: 'flag', class: 'bg-red-100 text-red-800' }
    };
    
    const newStatus = statusMap[status];
    if (newStatus) {
        // Update detail view
        const statusBadge = document.getElementById('detail-status');
        statusBadge.innerHTML = `<i class="fas fa-${newStatus.icon} mr-1"></i> ${newStatus.text}`;
        statusBadge.className = `px-2.5 py-1 text-xs font-medium rounded-full ${newStatus.class}`;
        
        // Update list view
        const complaintItem = document.querySelector(`.complaint-item[data-id="${selectedComplaintId}"]`);
        if (complaintItem) {
            const statusSpan = complaintItem.querySelector('.bg-yellow-100, .bg-blue-100, .bg-green-100, .bg-red-100');
            if (statusSpan) {
                statusSpan.innerHTML = `<i class="fas fa-${newStatus.icon} mr-1"></i> ${newStatus.text}`;
                statusSpan.className = `px-2.5 py-1 text-xs font-medium rounded-full ${newStatus.class}`;
            }
        }
        
        Toast.success('Status Updated', `Complaint marked as ${newStatus.text.toLowerCase()}.`);
    }
}

// Helper functions
function getStatusIcon(status) {
    const icons = {
        'Pending': 'clock',
        'In Progress': 'tools',
        'Resolved': 'check-circle',
        'Escalated': 'flag'
    };
    return icons[status] || 'question-circle';
}

function getStatusClass(status) {
    const classes = {
        'Pending': 'bg-yellow-100 text-yellow-800',
        'In Progress': 'bg-blue-100 text-blue-800',
        'Resolved': 'bg-green-100 text-green-800',
        'Escalated': 'bg-red-100 text-red-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.querySelector('input[type="search"]');
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const items = document.querySelectorAll('.complaint-item');
        
        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    });
    
    // Filter buttons
    document.querySelectorAll('.flex.gap-1 button').forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            this.parentNode.querySelectorAll('button').forEach(btn => {
                btn.classList.remove('border-yellow-300', 'bg-yellow-50', 'text-yellow-700');
                btn.classList.add('border-gray-300', 'bg-white', 'text-gray-700');
            });
            
            // Add active class to clicked button
            this.classList.remove('border-gray-300', 'bg-white', 'text-gray-700');
            this.classList.add('border-yellow-300', 'bg-yellow-50', 'text-yellow-700');
            
            // Filter complaints by status
            const filterText = this.textContent;
            filterComplaints(filterText);
        });
    });
});

function filterComplaints(filter) {
    const items = document.querySelectorAll('.complaint-item');
    
    items.forEach(item => {
        if (filter === 'All') {
            item.classList.remove('hidden');
        } else {
            const status = item.querySelector('.px-2\\.5')?.textContent || '';
            if (status.includes(filter)) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        }
    });
}
</script>

<style>
/* Complaint Management Styles */
.complaint-item {
    transition: all 0.2s ease;
}

.complaint-item:hover {
    transform: translateX(4px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.complaint-item.active {
    border-color: #8b5cf6;
    background-color: #faf5ff;
}

/* Status badge animations */
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.bg-yellow-100 {
    animation: pulse 2s infinite;
}

.bg-red-100 {
    animation: pulse 1.5s infinite;
}

/* Conversation thread scrollbar */
#conversation-thread::-webkit-scrollbar {
    width: 6px;
}

#conversation-thread::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

#conversation-thread::-webkit-scrollbar-thumb {
    background: #c7d2fe;
    border-radius: 3px;
}

#conversation-thread::-webkit-scrollbar-thumb:hover {
    background: #a5b4fc;
}

/* Line clamp for description */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Attachment hover effects */
.border-gray-200:hover {
    border-color: #8b5cf6;
    transform: scale(1.02);
}

/* Quick action buttons hover */
.bg-gray-100:hover {
    background-color: #e5e7eb !important;
    transform: translateY(-1px);
}

/* Sticky detail panel */
.sticky {
    position: sticky;
}

/* Responsive adjustments */
@media (max-width: 1024px) {
    .grid-cols-3 {
        grid-template-columns: 1fr;
    }
    
    #complaint-detail {
        position: static;
    }
}

/* Smooth transitions */
.transition-all {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Textarea focus effect */
textarea:focus {
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

/* Priority indicators */
.bg-red-100 {
    border-left: 3px solid #ef4444;
}

.bg-orange-100 {
    border-left: 3px solid #f97316;
}

.bg-yellow-100 {
    border-left: 3px solid #eab308;
}

.bg-green-100 {
    border-left: 3px solid #22c55e;
}
</style>
@endsection