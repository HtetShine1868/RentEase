@extends('owner.layout.owner-layout')

@section('title', 'My Chats')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <h1 class="text-2xl font-bold text-gray-900">My Conversations</h1>
            <p class="mt-2 text-gray-600">Chat with tenants about their bookings</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            @if($conversations->isEmpty())
                <div class="p-12 text-center">
                    <div class="h-24 w-24 mx-auto bg-gray-100 rounded-full flex items-center justify-center">
                        <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No conversations yet</h3>
                    <p class="mt-2 text-sm text-gray-500">When tenants book your properties, you can chat with them here.</p>
                </div>
            @else
                <div class="divide-y divide-gray-200">
                    @foreach($conversations as $conversation)
                        @php
                            $tenant = $conversation->tenant;
                            $lastMessage = $conversation->messages->first();
                            $unreadCount = $conversation->messages()
                                ->where('receiver_id', Auth::id())
                                ->where('is_read', false)
                                ->count();
                        @endphp
                        <a href="{{ route('owner.chat.show', $conversation->booking) }}" 
                           class="block hover:bg-gray-50 transition duration-150 {{ $unreadCount > 0 ? 'bg-blue-50' : '' }}">
                            <div class="px-6 py-4">
                                <div class="flex items-center">
                                    <!-- Avatar -->
                                    <div class="h-14 w-14 rounded-full bg-gradient-to-r from-indigo-500 to-blue-600 flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                                        @if($tenant->avatar_url)
                                            <img src="{{ $tenant->avatar_url }}" class="h-14 w-14 rounded-full object-cover">
                                        @else
                                            {{ strtoupper(substr($tenant->name, 0, 1)) }}
                                        @endif
                                    </div>
                                    
                                    <div class="ml-4 flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900">
                                                    {{ $tenant->name }}
                                                    @if($unreadCount > 0)
                                                        <span class="ml-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                                                            {{ $unreadCount }} new
                                                        </span>
                                                    @endif
                                                </h3>
                                                <p class="text-sm text-gray-600">
                                                    {{ $conversation->property->name ?? 'Property' }}
                                                </p>
                                            </div>
                                            @if($lastMessage)
                                                <span class="text-xs text-gray-500">
                                                    {{ $lastMessage->created_at->diffForHumans() }}
                                                </span>
                                            @endif
                                        </div>
                                        
                                        @if($lastMessage)
                                            <p class="text-sm mt-1 {{ $unreadCount > 0 ? 'font-semibold text-gray-900' : 'text-gray-500' }}">
                                                @if($lastMessage->sender_id === Auth::id())
                                                    <span class="text-gray-400">You: </span>
                                                @endif
                                                {{ \Str::limit($lastMessage->message, 60) }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection