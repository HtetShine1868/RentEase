@extends('owner.layout.owner-layout')

@section('title', 'Complaint Management - RentEase')
@section('page-title', 'Complaint Management')
@section('page-subtitle', 'Manage and resolve customer complaints')

@section('content')
<div class="space-y-6">
    @include('owner.components.validation-messages')

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
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-500">Open</p>
                    <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['open'] ?? 0 }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-500">In Progress</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['in_progress'] ?? 0 }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-500">High Priority</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['high_priority'] ?? 0 }}</p>
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
                                @php
                                    $currentStatus = request('status', 'all');
                                @endphp
                                
                                <a href="{{ route('owner.complaints.index', ['status' => 'all'] + request()->except(['status', 'page'])) }}" 
                                   class="px-3 py-1.5 text-xs font-medium rounded-lg border {{ $currentStatus == 'all' ? 'border-purple-300 bg-purple-50 text-purple-700' : 'border-gray-300 hover:bg-gray-50 bg-white text-gray-700' }}">
                                    All
                                </a>
                                <a href="{{ route('owner.complaints.index', ['status' => 'OPEN'] + request()->except(['status', 'page'])) }}" 
                                   class="px-3 py-1.5 text-xs font-medium rounded-lg border {{ $currentStatus == 'OPEN' ? 'border-yellow-300 bg-yellow-50 text-yellow-700' : 'border-gray-300 hover:bg-gray-50 bg-white text-gray-700' }}">
                                    Open
                                </a>
                                <a href="{{ route('owner.complaints.index', ['status' => 'IN_PROGRESS'] + request()->except(['status', 'page'])) }}" 
                                   class="px-3 py-1.5 text-xs font-medium rounded-lg border {{ $currentStatus == 'IN_PROGRESS' ? 'border-blue-300 bg-blue-50 text-blue-700' : 'border-gray-300 hover:bg-gray-50 bg-white text-gray-700' }}">
                                    In Progress
                                </a>
                                <a href="{{ route('owner.complaints.index', ['status' => 'RESOLVED'] + request()->except(['status', 'page'])) }}" 
                                   class="px-3 py-1.5 text-xs font-medium rounded-lg border {{ $currentStatus == 'RESOLVED' ? 'border-green-300 bg-green-50 text-green-700' : 'border-gray-300 hover:bg-gray-50 bg-white text-gray-700' }}">
                                    Resolved
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search Form -->
                <form method="GET" action="{{ route('owner.complaints.index') }}" class="relative mb-4">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="search" 
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search complaints by user, property, or issue..." 
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <button type="submit" class="hidden">Search</button>
                </form>
            </div>

            <!-- Complaint List -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                @if($complaints->count() > 0)
                    <div class="space-y-4 max-h-[600px] overflow-y-auto pr-2" id="complaint-list">
                        @foreach($complaints as $complaint)
                        <div class="bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md transition-all duration-200 cursor-pointer complaint-item"
                             data-id="{{ $complaint->id }}"
                             onclick="selectComplaint({{ $complaint->id }})"
                             id="complaint-{{ $complaint->id }}">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-3">
                                        <!-- Status Badge -->
                                        <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $complaint->status_badge_class }}">
                                            <i class="fas {{ $complaint->status_icon }} mr-1"></i> {{ $complaint->status_text }}
                                        </span>
                                        
                                        <!-- Priority Badge -->
                                        <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $complaint->priority_badge_class }}">
                                            <i class="fas {{ $complaint->priority_icon }} mr-1"></i> {{ $complaint->priority_text }}
                                        </span>
                                        
                                        <span class="text-xs text-gray-500 ml-auto">
                                            <i class="far fa-clock mr-1"></i> {{ $complaint->elapsed_time }}
                                        </span>
                                    </div>
                                    
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $complaint->title }}</h3>
                                    
                                    <div class="flex items-center gap-4 text-sm text-gray-600 mb-3 flex-wrap">
                                        <span class="flex items-center">
                                            <i class="fas fa-user mr-2 text-gray-400"></i>
                                            {{ $complaint->user->name ?? 'Unknown User' }}
                                        </span>
                                        <span class="flex items-center">
                                            <i class="fas {{ $complaint->related_entity_icon }} mr-2 text-gray-400"></i>
                                            {{ $complaint->related_entity_name ?? $complaint->related_type }}
                                        </span>
                                        <span class="flex items-center">
                                            <i class="fas fa-hashtag mr-2 text-gray-400"></i>
                                            {{ $complaint->complaint_reference }}
                                        </span>
                                    </div>
                                    
                                    <p class="text-gray-600 line-clamp-2 mb-3">
                                        {{ Str::limit($complaint->description, 150) }}
                                    </p>
                                    
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            @if($complaint->conversations_count > 0)
                                            <div class="flex items-center">
                                                <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center mr-2">
                                                    <i class="far fa-comment text-blue-600 text-xs"></i>
                                                </div>
                                                <span class="text-xs text-gray-600">{{ $complaint->conversations_count }} replies</span>
                                            </div>
                                            @endif
                                        </div>
                                        
                                        @if(!$complaint->assigned_to)
                                        <button onclick="event.stopPropagation(); assignToSelf({{ $complaint->id }})" 
                                                class="px-3 py-1.5 text-sm font-medium text-purple-700 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                                            Take Action
                                        </button>
                                        @elseif($complaint->assigned_to == auth()->id())
                                        <span class="px-3 py-1.5 text-sm font-medium text-green-700 bg-green-50 rounded-lg">
                                            <i class="fas fa-user-check mr-1"></i> Assigned to you
                                        </span>
                                        @else
                                        <span class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-gray-50 rounded-lg">
                                            <i class="fas fa-user-cog mr-1"></i> {{ $complaint->assignedUser->name ?? 'Assigned' }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($complaints->hasPages())
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        {{ $complaints->links() }}
                    </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-comment-slash text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-700 mb-3">No Complaints Found</h3>
                        <p class="text-gray-500 mb-6">There are no complaints matching your criteria</p>
                        <a href="{{ route('owner.complaints.index') }}" 
                           class="px-6 py-2.5 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition-colors">
                            <i class="fas fa-sync-alt mr-2"></i> Reset Filters
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Complaint Detail View -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-gray-200 p-6 sticky top-6" id="complaint-detail">
                <!-- Default State -->
                <div id="default-detail" class="text-center py-12 {{ $selectedComplaint ? 'hidden' : '' }}">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-comments text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-3">Select a Complaint</h3>
                    <p class="text-gray-500">Choose a complaint from the list to view details and take action</p>
                </div>

                <!-- Complaint Detail Content -->
                <div id="detail-content" class="{{ $selectedComplaint ? '' : 'hidden' }}">
                    @if($selectedComplaint)
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-6 pb-6 border-b border-gray-200">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900" id="detail-title">{{ $selectedComplaint->title }}</h2>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $selectedComplaint->status_badge_class }}" id="detail-status">
                                    <i class="fas {{ $selectedComplaint->status_icon }} mr-1"></i> {{ $selectedComplaint->status_text }}
                                </span>
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $selectedComplaint->priority_badge_class }}" id="detail-priority">
                                    <i class="fas {{ $selectedComplaint->priority_icon }} mr-1"></i> {{ $selectedComplaint->priority_text }}
                                </span>
                            </div>
                        </div>
                        <button onclick="closeDetail()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <!-- User & Property Info -->
                    <div class="bg-gray-50 rounded-xl p-4 mb-6">
                        <div class="grid grid-cols-1 gap-4">
                            <!-- User Info -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Complainant</h4>
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                        @if($selectedComplaint->user->avatar_url)
                                            <img src="{{ $selectedComplaint->user->avatar_url }}" alt="{{ $selectedComplaint->user->name }}" class="w-10 h-10 rounded-full">
                                        @else
                                            <i class="fas fa-user text-purple-600"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900" id="detail-user">{{ $selectedComplaint->user->name }}</p>
                                        <p class="text-sm text-gray-500" id="detail-user-email">{{ $selectedComplaint->user->email }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Related Entity Info -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2">
                                    {{ $selectedComplaint->related_type == 'PROPERTY' ? 'Property' : ($selectedComplaint->related_type == 'SERVICE_PROVIDER' ? 'Service Provider' : 'Related To') }}
                                </h4>
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-lg {{ $selectedComplaint->related_type == 'PROPERTY' ? 'bg-blue-100' : 'bg-green-100' }} flex items-center justify-center mr-3">
                                        <i class="fas {{ $selectedComplaint->related_entity_icon }} {{ $selectedComplaint->related_type == 'PROPERTY' ? 'text-blue-600' : 'text-green-600' }}"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900" id="detail-related">
                                            {{ $selectedComplaint->related_entity_name ?? $selectedComplaint->related_type }}
                                        </p>
                                        <p class="text-sm text-gray-500" id="detail-id">{{ $selectedComplaint->complaint_reference }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Timeline -->
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Timeline</h4>
                            <div class="space-y-3" id="timeline">
                                <!-- Created -->
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-plus text-green-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Complaint Filed</p>
                                        <p class="text-xs text-gray-500">{{ $selectedComplaint->created_at->format('M d, Y • h:i A') }}</p>
                                    </div>
                                </div>
                                
                                <!-- Assigned -->
                                @if($selectedComplaint->assigned_to && $selectedComplaint->assignedUser)
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-user-cog text-blue-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Assigned to {{ $selectedComplaint->assignedUser->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $selectedComplaint->updated_at->format('M d, Y • h:i A') }}</p>
                                    </div>
                                </div>
                                @endif
                                
                                <!-- Status Updates (if available) -->
                                @if(isset($selectedComplaint->statusHistory) && $selectedComplaint->statusHistory->count() > 0)
                                    @foreach($selectedComplaint->statusHistory as $history)
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center mr-3">
                                            <i class="fas fa-history text-yellow-600 text-xs"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Status changed to {{ $history->new_status }}</p>
                                            <p class="text-xs text-gray-500">{{ $history->created_at->format('M d, Y • h:i A') }}</p>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Complaint Description -->
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Description</h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-700 whitespace-pre-line" id="detail-description">{{ $selectedComplaint->description }}</p>
                        </div>
                        
                        <!-- Attachments (if any) -->
                        @if(isset($selectedComplaint->attachments_count) && $selectedComplaint->attachments_count > 0)
                        <div class="mt-4">
                            <h5 class="text-sm font-medium text-gray-700 mb-2">Attachments ({{ $selectedComplaint->attachments_count }})</h5>
                            <div class="text-sm text-gray-500">
                                <i class="fas fa-paperclip mr-1"></i> This complaint has attachments
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Conversation Thread -->
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-4">Conversation</h4>
                        <div class="space-y-4 max-h-64 overflow-y-auto pr-2" id="conversation-thread">
                            @if(isset($selectedComplaint->conversations) && $selectedComplaint->conversations->count() > 0)
                                @foreach($selectedComplaint->conversations as $conversation)
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full {{ $conversation->user_id == auth()->id() ? 'bg-green-100' : 'bg-purple-100' }} flex items-center justify-center">
                                        @if($conversation->user_id == auth()->id())
                                            <i class="fas fa-user-tie text-green-600 text-xs"></i>
                                        @else
                                            @if($conversation->user->avatar_url)
                                                <img src="{{ $conversation->user->avatar_url }}" alt="{{ $conversation->user->name }}" class="w-8 h-8 rounded-full">
                                            @else
                                                <i class="fas fa-user text-purple-600 text-xs"></i>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <div class="{{ $conversation->user_id == auth()->id() ? 'bg-green-50 border border-green-100' : 'bg-gray-100' }} rounded-lg p-3">
                                            @if($conversation->type != 'REPLY')
                                                <p class="text-xs font-medium text-gray-500 mb-1">
                                                    <i class="fas fa-{{ $conversation->type == 'STATUS_UPDATE' ? 'history' : ($conversation->type == 'ASSIGNMENT' ? 'user-cog' : 'comment') }} mr-1"></i>
                                                    {{ ucfirst(strtolower(str_replace('_', ' ', $conversation->type))) }}
                                                </p>
                                            @endif
                                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $conversation->message }}</p>
                                            
                                            @if(isset($conversation->attachments) && is_array($conversation->attachments) && count($conversation->attachments) > 0)
                                            <div class="mt-2">
                                                @foreach($conversation->attachments as $attachment)
                                                    @if(isset($attachment['url']))
                                                    <a href="{{ $attachment['url'] }}" target="_blank" class="inline-flex items-center text-xs text-blue-600 hover:text-blue-800 mr-3">
                                                        <i class="fas fa-paperclip mr-1"></i> {{ $attachment['name'] ?? 'Attachment' }}
                                                    </a>
                                                    @endif
                                                @endforeach
                                            </div>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $conversation->user->name ?? 'System' }} • {{ $conversation->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <p class="text-sm text-gray-500 text-center py-4">No conversations yet</p>
                            @endif
                        </div>
                    </div>

                    <!-- Reply Box -->
                    <div class="border-t border-gray-200 pt-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Reply to Complaint</h4>
                        <form id="reply-form" method="POST" action="{{ route('owner.complaints.reply', $selectedComplaint->id) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="space-y-3">
                                <textarea id="reply-textarea" 
                                          name="message"
                                          rows="3" 
                                          placeholder="Type your response here..." 
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 resize-none"></textarea>
                                
                                <!-- Attachment Input -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <label for="attachment-upload" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg cursor-pointer">
                                            <i class="fas fa-paperclip"></i>
                                            <input type="file" 
                                                   id="attachment-upload" 
                                                   name="attachments[]"
                                                   multiple
                                                   class="hidden"
                                                   onchange="updateAttachmentPreview()">
                                        </label>
                                        <div id="attachment-preview" class="text-xs text-gray-500"></div>
                                    </div>
                                    
                                    <div class="flex items-center gap-3">
                                        <select name="status" 
                                                id="status-select"
                                                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                            <option value="">Update Status</option>
                                            <option value="IN_PROGRESS" {{ $selectedComplaint->status == 'IN_PROGRESS' ? 'selected' : '' }}>Mark as In Progress</option>
                                            <option value="RESOLVED" {{ $selectedComplaint->status == 'RESOLVED' ? 'selected' : '' }}>Mark as Resolved</option>
                                            <option value="CLOSED" {{ $selectedComplaint->status == 'CLOSED' ? 'selected' : '' }}>Close Complaint</option>
                                        </select>
                                        
                                        <button type="submit" 
                                                class="px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition-colors">
                                            <i class="fas fa-paper-plane mr-2"></i> Send
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Quick Actions -->
                                <div class="grid grid-cols-2 gap-2 mt-4">
                                    <button type="button" onclick="useQuickReply('We have assigned our maintenance team. They will contact you shortly.')"
                                            class="px-3 py-2 text-sm text-left text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                        <i class="fas fa-tools mr-1"></i> Assigned to Maintenance
                                    </button>
                                    <button type="button" onclick="useQuickReply('Thank you for reporting. We are looking into this matter.')"
                                            class="px-3 py-2 text-sm text-left text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                        <i class="fas fa-thumbs-up mr-1"></i> Acknowledged
                                    </button>
                                    <button type="button" onclick="useQuickReply('Can you please provide more details about the issue?')"
                                            class="px-3 py-2 text-sm text-left text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                        <i class="fas fa-question-circle mr-1"></i> Need More Info
                                    </button>
                                    <button type="button" onclick="useQuickReply('The issue has been resolved. Please confirm if everything is working now.')"
                                            class="px-3 py-2 text-sm text-left text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                        <i class="fas fa-check-circle mr-1"></i> Issue Resolved
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Complaint Management Functions
let selectedComplaintId = {{ $selectedComplaint ? $selectedComplaint->id : 'null' }};

// Get CSRF token safely
function getCsrfToken() {
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    return metaTag ? metaTag.content : '';
}

// Get CSRF token from Laravel's built-in token if available
function getCsrfTokenAlternative() {
    // Try to get token from meta tag first
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    if (metaTag && metaTag.content) {
        return metaTag.content;
    }
    
    // Try to get token from input field
    const tokenInput = document.querySelector('input[name="_token"]');
    if (tokenInput && tokenInput.value) {
        return tokenInput.value;
    }
    
    // Return empty string as fallback
    return '';
}

function selectComplaint(complaintId) {
    // Get current URL parameters
    const url = new URL(window.location.href);
    
    // Update complaint_id parameter
    url.searchParams.set('complaint_id', complaintId);
    
    // Remove page parameter to go to first page
    url.searchParams.delete('page');
    
    // Navigate to the updated URL
    window.location.href = url.toString();
}

function closeDetail() {
    // Navigate back to complaint list without complaint_id
    const url = new URL(window.location.href);
    url.searchParams.delete('complaint_id');
    window.location.href = url.toString();
}

function assignToSelf(complaintId) {
    if (!confirm('Do you want to assign this complaint to yourself?')) {
        return;
    }
    
    showLoading('Assigning complaint...');
    
    const csrfToken = getCsrfTokenAlternative();
    
    fetch(`/owner/complaints/${complaintId}/assign-self`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        hideLoading();
        if (data.success) {
            showToast('success', data.message);
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showToast('error', data.message);
        }
    })
    .catch(error => {
        hideLoading();
        showToast('error', 'Failed to assign complaint: ' + error.message);
        console.error('Error:', error);
    });
}

function updateComplaintStatus(complaintId, status) {
    if (!status) return;
    
    if (!confirm(`Are you sure you want to change status to "${status}"?`)) {
        return;
    }
    
    showLoading('Updating status...');
    
    const csrfToken = getCsrfTokenAlternative();
    
    fetch(`/owner/complaints/${complaintId}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            status: status,
            _method: 'PUT' // For Laravel method spoofing if needed
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        hideLoading();
        if (data.success) {
            showToast('success', data.message || 'Status updated successfully');
            
            // Update UI immediately
            updateStatusInUI(complaintId, status);
            
            // Reload after 2 seconds to get fresh data
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            showToast('error', data.message || 'Failed to update status');
        }
    })
    .catch(error => {
        hideLoading();
        showToast('error', 'Failed to update status: ' + error.message);
        console.error('Error:', error);
    });
}

function updateStatusInUI(complaintId, status) {
    // Update status badge in the list
    const complaintItem = document.querySelector(`#complaint-${complaintId}`);
    if (complaintItem) {
        const statusMap = {
            'OPEN': { text: 'Open', icon: 'clock', class: 'bg-yellow-100 text-yellow-800' },
            'IN_PROGRESS': { text: 'In Progress', icon: 'tools', class: 'bg-blue-100 text-blue-800' },
            'RESOLVED': { text: 'Resolved', icon: 'check-circle', class: 'bg-green-100 text-green-800' },
            'CLOSED': { text: 'Closed', icon: 'check-double', class: 'bg-gray-100 text-gray-800' }
        };
        
        const newStatus = statusMap[status];
        if (newStatus) {
            // Update status badge in list
            const statusBadge = complaintItem.querySelector('.status-badge');
            if (statusBadge) {
                statusBadge.innerHTML = `<i class="fas ${newStatus.icon} mr-1"></i> ${newStatus.text}`;
                statusBadge.className = `px-2.5 py-1 text-xs font-medium rounded-full status-badge ${newStatus.class}`;
            }
        }
    }
    
    // If we're viewing this complaint's detail, update detail view too
    if (selectedComplaintId == complaintId) {
        const statusMap = {
            'OPEN': { text: 'Open', icon: 'clock', class: 'bg-yellow-100 text-yellow-800' },
            'IN_PROGRESS': { text: 'In Progress', icon: 'tools', class: 'bg-blue-100 text-blue-800' },
            'RESOLVED': { text: 'Resolved', icon: 'check-circle', class: 'bg-green-100 text-green-800' },
            'CLOSED': { text: 'Closed', icon: 'check-double', class: 'bg-gray-100 text-gray-800' }
        };
        
        const newStatus = statusMap[status];
        if (newStatus) {
            const detailBadge = document.getElementById('detail-status');
            if (detailBadge) {
                detailBadge.innerHTML = `<i class="fas ${newStatus.icon} mr-1"></i> ${newStatus.text}`;
                detailBadge.className = `px-2.5 py-1 text-xs font-medium rounded-full ${newStatus.class}`;
            }
        }
    }
}

function useQuickReply(text) {
    const textarea = document.getElementById('reply-textarea');
    if (textarea) {
        textarea.value = text;
        textarea.focus();
    }
}

function updateAttachmentPreview() {
    const input = document.getElementById('attachment-upload');
    const preview = document.getElementById('attachment-preview');
    
    if (input && preview) {
        if (input.files.length > 0) {
            preview.textContent = `${input.files.length} file(s) selected`;
        } else {
            preview.textContent = '';
        }
    }
}

// Handle form submission
const replyForm = document.getElementById('reply-form');
if (replyForm) {
    replyForm.addEventListener('submit', function(e) {
        const textarea = document.getElementById('reply-textarea');
        const message = textarea ? textarea.value.trim() : '';
        
        if (!message) {
            e.preventDefault();
            showToast('error', 'Please type a message before sending.');
            if (textarea) textarea.focus();
        }
    });
}

// Handle status change from detail view
const statusSelect = document.getElementById('status-select');
if (statusSelect) {
    statusSelect.addEventListener('change', function() {
        if (this.value && selectedComplaintId) {
            updateComplaintStatus(selectedComplaintId, this.value);
            this.value = '';
        }
    });
}

// Search functionality
const searchInput = document.querySelector('input[name="search"]');
if (searchInput) {
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            this.form.submit();
        }
    });
}

// Utility Functions
function showLoading(message = 'Loading...') {
    // Remove existing loading overlay
    hideLoading();
    
    // Create loading overlay
    const overlay = document.createElement('div');
    overlay.id = 'loading-overlay';
    overlay.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    overlay.innerHTML = `
        <div class="bg-white rounded-xl p-6 flex flex-col items-center min-w-64">
            <div class="loading-spinner mb-4"></div>
            <p class="text-gray-700 font-medium">${message}</p>
        </div>
    `;
    document.body.appendChild(overlay);
}

function hideLoading() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) {
        overlay.remove();
    }
}

function showToast(type, message) {
    // Remove existing toast
    const existingToast = document.querySelector('.toast-notification');
    if (existingToast) {
        existingToast.remove();
    }
    
    // Create new toast
    const toast = document.createElement('div');
    toast.className = `toast-notification fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 animate-toast-in ${
        type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200'
    }`;
    toast.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'} mr-2"></i>
            <span class="font-medium">${message}</span>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.classList.add('animate-toast-out');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.remove();
                }
            }, 300);
        }
    }, 5000);
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Highlight selected complaint in list
    if (selectedComplaintId) {
        const selectedItem = document.querySelector(`#complaint-${selectedComplaintId}`);
        if (selectedItem) {
            selectedItem.classList.add('border-purple-500', 'bg-purple-50');
            selectedItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }
    
    // Auto-focus search if there's a search term
    if (searchInput && searchInput.value) {
        searchInput.focus();
    }
    
    // Add status badges to all complaint items for easier JS targeting
    document.querySelectorAll('.complaint-item').forEach(item => {
        const statusBadge = item.querySelector('.bg-yellow-100, .bg-blue-100, .bg-green-100, .bg-gray-100');
        if (statusBadge) {
            statusBadge.classList.add('status-badge');
        }
    });
});

// Add to your CSS section or create separate CSS
const style = document.createElement('style');
style.textContent = `
    .animate-toast-in {
        animation: toastIn 0.3s ease-out;
    }
    
    .animate-toast-out {
        animation: toastOut 0.3s ease-in forwards;
    }
    
    @keyframes toastIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes toastOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #8b5cf6;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);
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

/* Loading spinner */
.loading-spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #8b5cf6;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Toast notification */
.toast-notification {
    animation: slideIn 0.3s ease-out;
    border: 1px solid;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
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

/* Responsive adjustments */
@media (max-width: 1024px) {
    .grid-cols-3 {
        grid-template-columns: 1fr;
    }
    
    #complaint-detail {
        position: static;
    }
}
</style>
@endsection