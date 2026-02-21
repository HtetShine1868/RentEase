@foreach($conversations as $conversation)
    @php
        $otherUser = $conversation->getOtherParticipant(Auth::id());
        $lastMessage = $conversation->getLastMessage();
        $unreadCount = $conversation->getUnreadCount(Auth::id());
    @endphp
    <div class="conversation-item p-4 hover:bg-gray-100 cursor-pointer border-b border-gray-200 {{ $conversation->id == ($activeConversation->id ?? '') ? 'active' : '' }}"
         data-conversation-id="{{ $conversation->id }}"
         data-user-name="{{ $otherUser->name }}"
         data-property-name="{{ $conversation->property->name ?? '' }}"
         data-last-message="{{ $lastMessage->message ?? '' }}"
         onclick="loadConversation({{ $conversation->id }})">
        
        <div class="flex items-center">
            <!-- Avatar -->
            <div class="flex-shrink-0 mr-3">
                @if($otherUser->avatar_url)
                    <img src="{{ $otherUser->avatar_url }}" 
                         alt="{{ $otherUser->name }}" 
                         class="w-12 h-12 rounded-full object-cover">
                @else
                    <div class="w-12 h-12 rounded-full bg-[#174455] flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <!-- Conversation Info -->
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <h4 class="font-medium text-gray-900 truncate">
                        {{ $otherUser->name }}
                    </h4>
                    @if($lastMessage)
                        <span class="text-xs text-gray-500">
                            {{ $lastMessage->created_at->diffForHumans() }}
                        </span>
                    @endif
                </div>
                
                @if($conversation->property)
                    <p class="text-xs text-[#174455] mb-1">
                        <i class="fas fa-home mr-1"></i>{{ $conversation->property->name }}
                    </p>
                @endif
                
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-600 truncate {{ $unreadCount > 0 ? 'font-semibold' : '' }}">
                        @if($lastMessage)
                            @if($lastMessage->sender_id == Auth::id())
                                <span class="text-gray-400">You: </span>
                            @endif
                            {{ $lastMessage->message }}
                        @else
                            <span class="text-gray-400">No messages yet</span>
                        @endif
                    </p>
                    
                    @if($unreadCount > 0)
                        <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full ml-2">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endforeach