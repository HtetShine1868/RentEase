@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">My Conversations</h1>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            @if($conversations->isEmpty())
                <div class="p-8 text-center text-gray-500">
                    <p>No conversations yet.</p>
                    <a href="{{ route('properties.index') }}" class="text-blue-500 hover:underline mt-2 inline-block">
                        Browse Properties to Start Chatting
                    </a>
                </div>
            @else
                <div class="divide-y">
                    @foreach($conversations as $conversation)
                        @php
                            $otherUser = $conversation->getOtherParticipant(Auth::id());
                            $lastMessage = $conversation->getLastMessage();
                        @endphp
                        
                        <a href="{{ route('chat.show', $conversation) }}" 
                           class="block hover:bg-gray-50 transition {{ $conversation->unread_count > 0 ? 'bg-blue-50' : '' }}">
                            <div class="p-4 flex items-center">
                                <!-- User Avatar -->
                                <div class="flex-shrink-0 mr-4">
                                    @if($otherUser->avatar_url)
                                        <img src="{{ $otherUser->avatar_url }}" 
                                             alt="{{ $otherUser->name }}" 
                                             class="w-12 h-12 rounded-full object-cover">
                                    @else
                                        <div class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-gray-600 font-bold">
                                                {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Conversation Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h3 class="font-semibold text-gray-900">
                                            {{ $otherUser->name }}
                                            @if($conversation->property)
                                                <span class="text-sm font-normal text-gray-500 ml-2">
                                                    re: {{ $conversation->property->name }}
                                                </span>
                                            @endif
                                        </h3>
                                        @if($lastMessage)
                                            <span class="text-xs text-gray-500">
                                                {{ $lastMessage->created_at->diffForHumans() }}
                                            </span>
                                        @endif
                                    </div>

                                    @if($lastMessage)
                                        <p class="text-sm {{ $conversation->unread_count > 0 ? 'font-semibold text-gray-900' : 'text-gray-500' }} truncate">
                                            @if($lastMessage->sender_id == Auth::id())
                                                <span class="text-gray-400">You: </span>
                                            @endif
                                            {{ $lastMessage->message }}
                                        </p>
                                    @endif
                                </div>

                                <!-- Unread Badge -->
                                @if($conversation->unread_count > 0)
                                    <div class="ml-4 flex-shrink-0">
                                        <span class="bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                            {{ $conversation->unread_count }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection