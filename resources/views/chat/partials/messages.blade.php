@foreach($messages as $message)
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