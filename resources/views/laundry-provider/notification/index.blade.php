@extends('layouts.laundry-provider')

@section('title', 'Notifications')

@section('header', 'Notifications')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-6">
    <h2 class="text-xl font-semibold mb-4">Your Notifications</h2>
    
    <div class="space-y-4">
        @php
            $sampleNotifications = [
                ['type' => 'order', 'message' => 'New order #ORD001 received', 'time' => '5 minutes ago', 'read' => false],
                ['type' => 'payment', 'message' => 'Payment of ₹500 received', 'time' => '1 hour ago', 'read' => false],
                ['type' => 'review', 'message' => 'New review received', 'time' => '2 hours ago', 'read' => true],
            ];
        @endphp
        
        @forelse($sampleNotifications as $notification)
            <div class="flex items-start p-4 {{ $notification['read'] ? 'bg-gray-50' : 'bg-blue-50' }} rounded-lg">
                <div class="flex-shrink-0">
                    @if($notification['type'] == 'order')
                        <i class="fas fa-shopping-bag text-blue-500 text-xl"></i>
                    @elseif($notification['type'] == 'payment')
                        <i class="fas fa-rupee-sign text-green-500 text-xl"></i>
                    @else
                        <i class="fas fa-star text-yellow-500 text-xl"></i>
                    @endif
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm {{ $notification['read'] ? 'text-gray-600' : 'text-gray-900 font-medium' }}">
                        {{ $notification['message'] }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">{{ $notification['time'] }}</p>
                </div>
                @if(!$notification['read'])
                    <button class="text-xs text-blue-600 hover:text-blue-800">Mark as read</button>
                @endif
            </div>
        @empty
            <p class="text-gray-500 text-center py-8">No notifications yet</p>
        @endforelse
    </div>
</div>
@endsection