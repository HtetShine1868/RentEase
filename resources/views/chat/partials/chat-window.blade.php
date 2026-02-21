<div class="flex flex-col h-full">
    <!-- Chat Header -->
    <div class="p-4 border-b border-gray-200 bg-white">
        <div class="flex items-center">
            @php
                $otherUser = $conversation->getOtherParticipant(Auth::id());
            @endphp
            <!-- Avatar -->
            <div class="flex-shrink-0 mr-3">
                @if($otherUser->avatar_url)
                    <img src="{{ $otherUser->avatar_url }}" 
                         alt="{{ $otherUser->name }}" 
                         class="w-10 h-10 rounded-full object-cover">
                @else
                    <div class="w-10 h-10 rounded-full bg-[#174455] flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            
            <div>
                <h4 class="font-medium text-gray-900">{{ $otherUser->name }}</h4>
                @if($conversation->property)
                    <p class="text-xs text-[#174455]">
                        <i class="fas fa-home mr-1"></i>{{ $conversation->property->name }}
                    </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Messages -->
    <div class="flex-1 overflow-y-auto p-4 bg-gray-50" id="chat-messages">
        @foreach($conversation->messages()->with('sender')->orderBy('created_at', 'asc')->get() as $message)
            @php
                $isSender = $message->sender_id == Auth::id();
            @endphp
            <div class="mb-4 {{ $isSender ? 'text-right' : 'text-left' }}">
                <div class="inline-block max-w-xs lg:max-w-md">
                    <div class="flex items-center mb-1 {{ $isSender ? 'justify-end' : 'justify-start' }}">
                        <span class="text-xs text-gray-500">
                            {{ $isSender ? 'You' : $message->sender->name }} • 
                            {{ $message->created_at->format('g:i A') }}
                        </span>
                    </div>
                    <div class="rounded-lg px-4 py-2 {{ $isSender ? 'bg-[#174455] text-white' : 'bg-white text-gray-800 border border-gray-200' }}">
                        <p class="whitespace-pre-wrap">{{ $message->message }}</p>
                    </div>
                    @if($isSender && $message->is_read)
                        <span class="text-xs text-gray-500 mt-1 block">Read {{ $message->read_at->diffForHumans() }}</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Message Input -->
    <div class="p-4 border-t border-gray-200 bg-white">
        <form onsubmit="event.preventDefault(); sendMessage({{ $conversation->id }});" class="flex space-x-2">
            <textarea id="message-input" 
                      rows="1"
                      class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#174455] focus:border-transparent resize-none"
                      placeholder="Type your message..."
                      required></textarea>
            <button type="submit" 
                    class="px-4 py-2 bg-[#174455] text-white rounded-lg hover:bg-[#286b7f] transition focus:outline-none focus:ring-2 focus:ring-[#174455]">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </div>
</div>

<script>
// Auto-scroll to bottom
const messagesContainer = document.getElementById('chat-messages');
messagesContainer.scrollTop = messagesContainer.scrollHeight;

// Auto-resize textarea
const textarea = document.getElementById('message-input');
textarea.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
});

// Poll for new messages every 5 seconds
let lastMessageId = {{ $conversation->messages()->max('id') ?? 0 }};

setInterval(function() {
    if (!document.hidden) {
        fetch(`/chat/{{ $conversation->id }}/messages/new/${lastMessageId}`)
            .then(response => response.json())
            .then(messages => {
                messages.forEach(message => {
                    appendMessage(message);
                    lastMessageId = Math.max(lastMessageId, message.id);
                });
            });
    }
}, 5000);
</script>