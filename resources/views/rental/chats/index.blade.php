@extends('dashboard')

@section('title', 'My Chats')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center">
                <a href="{{ route('rental.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">My Conversations</h1>
                    <p class="text-sm text-gray-600">Chat with property owners about your rentals</p>
                </div>
            </div>
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
                    <p class="mt-2 text-sm text-gray-500">When you book a property, you can chat with the owner here.</p>
                    <a href="{{ route('properties.search') }}" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        Find a Rental
                    </a>
                </div>
            @else
                <div class="divide-y divide-gray-200">
                    @foreach($conversations as $conversation)
                        @php
                            $otherUser = $conversation->user_id === Auth::id() ? $conversation->owner : $conversation->user;
                            $lastMessage = $conversation->messages->first();
                            $unreadCount = $conversation->messages()
                                ->where('sender_id', '!=', Auth::id())
                                ->where('is_read', false)
                                ->count();
                        @endphp
                        <a href="{{ route('rental.chat.show', $conversation->booking) }}" 
                           class="block hover:bg-gray-50 transition duration-150 {{ $unreadCount > 0 ? 'bg-blue-50' : '' }}">
                            <div class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-14 w-14 rounded-full bg-gradient-to-r from-indigo-500 to-blue-600 flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                                        @if($otherUser->avatar_url)
                                            <img src="{{ $otherUser->avatar_url }}" class="h-14 w-14 rounded-full object-cover">
                                        @else
                                            {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                                        @endif
                                    </div>
                                    
                                    <div class="ml-4 flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900">
                                                    {{ $otherUser->name }}
                                                    @if($unreadCount > 0)
                                                        <span class="ml-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                                                            {{ $unreadCount }} new
                                                        </span>
                                                    @endif
                                                </h3>
                                                <p class="text-sm text-gray-600">
                                                    {{ $conversation->property->name ?? 'Property' }} • {{ $conversation->property->city ?? '' }}
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