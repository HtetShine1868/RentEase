@php
    $isSender = $message->sender_id === auth()->id();
@endphp

<div class="flex {{ $isSender ? 'justify-end' : 'justify-start' }} mb-4">
    <div class="flex {{ $isSender ? 'flex-row-reverse' : 'flex-row' }} items-start max-w-[70%]">
        <!-- Avatar -->
        <div class="flex-shrink-0 {{ $isSender ? 'ml-3' : 'mr-3' }}">
            <div class="h-8 w-8 rounded-full bg-gradient-to-r from-indigo-500 to-blue-500 flex items-center justify-center text-white text-xs font-bold">
                @if($message->sender->avatar_url)
                    <img src="{{ $message->sender->avatar_url }}" class="h-8 w-8 rounded-full object-cover">
                @else
                    {{ strtoupper(substr($message->sender->name, 0, 1)) }}
                @endif
            </div>
        </div>
        
        <!-- Message Content -->
        <div>
            <div class="rounded-lg px-4 py-2 {{ $isSender ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-900' }}">
                <p class="text-sm">{{ $message->message }}</p>
            </div>
            <p class="text-xs text-gray-500 mt-1 {{ $isSender ? 'text-right' : 'text-left' }}">
                {{ $message->created_at->format('g:i A') }}
                @if($isSender && $message->is_read)
                    • Read
                @endif
            </p>
        </div>
    </div>
</div>