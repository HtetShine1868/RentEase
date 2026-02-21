{{-- resources/views/laundry-provider/components/status-badge.blade.php --}}
@php
    $statusConfig = [
        'PENDING' => ['bg' => 'yellow', 'text' => 'Pending', 'icon' => 'fa-clock'],
        'PICKUP_SCHEDULED' => ['bg' => 'blue', 'text' => 'Pickup Scheduled', 'icon' => 'fa-calendar-check'],
        'PICKED_UP' => ['bg' => 'indigo', 'text' => 'Picked Up', 'icon' => 'fa-box-open'],
        'IN_PROGRESS' => ['bg' => 'purple', 'text' => 'In Progress', 'icon' => 'fa-spinner fa-spin'],
        'READY' => ['bg' => 'green', 'text' => 'Ready', 'icon' => 'fa-check-circle'],
        'OUT_FOR_DELIVERY' => ['bg' => 'orange', 'text' => 'Out for Delivery', 'icon' => 'fa-truck'],
        'DELIVERED' => ['bg' => 'gray', 'text' => 'Delivered', 'icon' => 'fa-check-double'],
        'CANCELLED' => ['bg' => 'red', 'text' => 'Cancelled', 'icon' => 'fa-times-circle']
    ];
    
    $config = $statusConfig[$status] ?? ['bg' => 'gray', 'text' => $status, 'icon' => 'fa-circle'];
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $config['bg'] }}-100 text-{{ $config['bg'] }}-800">
    <i class="fas {{ $config['icon'] }} mr-1"></i>
    {{ $config['text'] }}
</span>