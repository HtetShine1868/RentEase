@extends('owner.layout.owner-layout')

@section('title', 'Chat with ' . $tenant->name)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center">
                <a href="{{ route('owner.chats') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Chat with {{ $tenant->name }}</h1>
                    <p class="text-sm text-gray-600">{{ $booking->property->name }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Chat Container -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Chat Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center">
                    <div class="h-12 w-12 rounded-full bg-gradient-to-r from-indigo-500 to-blue-600 flex items-center justify-center text-white font-bold text-lg">
                        @if($tenant->avatar_url)
                            <img src="{{ $tenant->avatar_url }}" class="h-12 w-12 rounded-full object-cover">
                        @else
                            {{ strtoupper(substr($tenant->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-gray-900">{{ $tenant->name }}</h3>
                        <p class="text-sm text-gray-600">Tenant</p>
                    </div>
                    <div class="ml-auto text-sm text-gray-500">
                        Booking: {{ \Carbon\Carbon::parse($booking->check_in)->format('M d') }} - {{ \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') }}
                    </div>
                </div>
            </div>

            <!-- Messages -->
            <div class="h-96 overflow-y-auto p-6 bg-gray-50" id="messagesContainer">
                @foreach($messages as $message)
                    @include('owner.pages.chats.partials.message', ['message' => $message])
                @endforeach
            </div>

            <!-- Message Input -->
            <div class="px-6 py-4 border-t border-gray-200 bg-white">
                <form id="messageForm" class="flex space-x-3">
                    @csrf
                    <input type="text" 
                           id="messageInput"
                           class="flex-1 border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           placeholder="Type your message..."
                           autocomplete="off">
                    <button type="submit" 
                            class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Send
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.getElementById('messagesContainer');
    const messageForm = document.getElementById('messageForm');
    const messageInput = document.getElementById('messageInput');
    let lastMessageId = {{ $messages->last()->id ?? 0 }};

    messagesContainer.scrollTop = messagesContainer.scrollHeight;

    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        if (!message) return;

        fetch('{{ route("owner.chat.send", $booking) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                appendMessage(data.message);
                messageInput.value = '';
                lastMessageId = data.message.id;
            }
        });
    });

    setInterval(function() {
        if (document.hidden) return;

        fetch('{{ route("owner.chat.new", $booking) }}?last_message_id=' + lastMessageId)
            .then(response => response.json())
            .then(data => {
                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(message => {
                        appendMessage(message);
                    });
                    lastMessageId = data.last_message_id;
                }
            });
    }, 3000);

    function appendMessage(message) {
        const isSender = message.sender_id === {{ auth()->id() }};
        const messageHtml = `
            <div class="flex ${isSender ? 'justify-end' : 'justify-start'} mb-4">
                <div class="flex ${isSender ? 'flex-row-reverse' : 'flex-row'} items-start max-w-[70%]">
                    <div class="flex-shrink-0 ${isSender ? 'ml-3' : 'mr-3'}">
                        <div class="h-8 w-8 rounded-full bg-gradient-to-r from-indigo-500 to-blue-500 flex items-center justify-center text-white text-xs font-bold">
                            ${message.sender.avatar_url ? 
                                `<img src="${message.sender.avatar_url}" class="h-8 w-8 rounded-full object-cover">` : 
                                message.sender.name.charAt(0).toUpperCase()}
                        </div>
                    </div>
                    <div>
                        <div class="rounded-lg px-4 py-2 ${isSender ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-900'}">
                            <p class="text-sm">${message.message}</p>
                        </div>
                        <p class="text-xs text-gray-500 mt-1 ${isSender ? 'text-right' : 'text-left'}">
                            ${message.created_at}
                        </p>
                    </div>
                </div>
            </div>
        `;
        messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
});
</script>
@endsection