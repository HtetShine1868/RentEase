@extends('owner.layout.owner-layout')

@section('title', 'Owner Dashboard - RentEase')
@section('page-title', 'Dashboard Overview')
@section('page-subtitle', 'Manage your properties, bookings, and earnings')

@section('content')
<div class="space-y-8">
    <!-- Welcome Section -->
    <div class="welcome-section">
        <div class="welcome-content">
            <div class="welcome-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="welcome-text">
                <h1>Welcome back, {{ $owner->name }}!</h1>
                <p>Track your properties, manage bookings, and monitor earnings in real-time.</p>
                <div class="welcome-stats">
                    <div class="stat-item">
                        <span class="stat-number">{{ $totalProperties }}</span>
                        <span class="stat-label">Properties</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $totalBookings }}</span>
                        <span class="stat-label">Bookings</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">${{ number_format($totalEarnings, 2) }}</span>
                        <span class="stat-label">Revenue</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="welcome-actions">
            <a href="{{ route('owner.properties.create') }}" class="btn-primary">
                <i class="fas fa-plus"></i>
                Add New Property
            </a>
            <form action="{{ route('owner.dashboard.quick-action') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="action" value="view_analytics">
                <button type="submit" class="btn-secondary">
                    <i class="fas fa-chart-bar"></i>
                    View Reports
                </button>
            </form>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <!-- Property Stats -->
        <div class="stat-card stat-card-blue">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div>
                    <h3 class="stat-title">Property Overview</h3>
                    <p class="stat-subtitle">Total properties & occupancy</p>
                </div>
            </div>
            <div class="stat-content">
                <div class="stat-main">
                    <span class="stat-number">{{ $totalProperties }}</span>
                    <span class="stat-unit">Properties</span>
                </div>
                <div class="stat-details">
                    <div class="stat-detail">
                        <span class="detail-label">Hostels</span>
                        <span class="detail-value">{{ $hostelsCount }}</span>
                    </div>
                    <div class="stat-detail">
                        <span class="detail-label">Apartments</span>
                        <span class="detail-value">{{ $apartmentsCount }}</span>
                    </div>
                    <div class="stat-detail">
                        <span class="detail-label">Occupancy</span>
                        <span class="detail-value {{ $occupancyRate > 70 ? 'success' : 'warning' }}">
                            {{ $occupancyRate }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Stats -->
        <div class="stat-card stat-card-green">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <h3 class="stat-title">Bookings</h3>
                    <p class="stat-subtitle">Current & upcoming bookings</p>
                </div>
            </div>
            <div class="stat-content">
                <div class="stat-main">
                    <span class="stat-number">{{ $activeBookings }}</span>
                    <span class="stat-unit">Active</span>
                </div>
                <div class="stat-details">
                    <div class="stat-detail">
                        <span class="detail-label">Pending</span>
                        <span class="detail-value {{ $pendingBookings > 0 ? 'warning' : '' }}">
                            {{ $pendingBookings }}
                        </span>
                    </div>
                    <div class="stat-detail">
                        <span class="detail-label">Completed</span>
                        <span class="detail-value">{{ $completedBookings }}</span>
                    </div>
                    <div class="stat-detail">
                        <span class="detail-label">Revenue</span>
                        <span class="detail-value {{ $monthlyEarnings > 0 ? 'success' : '' }}">
                            ${{ number_format($monthlyEarnings, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Stats -->
        <div class="stat-card stat-card-purple">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div>
                    <h3 class="stat-title">Financial Summary</h3>
                    <p class="stat-subtitle">Earnings & revenue</p>
                </div>
            </div>
            <div class="stat-content">
                <div class="stat-main">
                    <span class="stat-number">${{ number_format($totalEarnings, 2) }}</span>
                    <span class="stat-unit">Total</span>
                </div>
                <div class="stat-details">
                    <div class="stat-detail">
                        <span class="detail-label">This Month</span>
                        <span class="detail-value {{ $monthlyEarnings > 0 ? 'success' : '' }}">
                            ${{ number_format($monthlyEarnings, 2) }}
                        </span>
                    </div>
                    <div class="stat-detail">
                        <span class="detail-label">Growth</span>
                        <span class="detail-value {{ $growthPercentage > 0 ? 'success' : ($growthPercentage < 0 ? 'danger' : '') }}">
                            {{ $growthPercentage > 0 ? '+' : '' }}{{ $growthPercentage }}%
                        </span>
                    </div>
                    <div class="stat-detail">
                        <span class="detail-label">Commission</span>
                        <span class="detail-value">{{ $commissionRate }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Stats -->
        <div class="stat-card stat-card-yellow">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div>
                    <h3 class="stat-title">Performance</h3>
                    <p class="stat-subtitle">Ratings & satisfaction</p>
                </div>
            </div>
            <div class="stat-content">
                <div class="stat-main">
                    <span class="stat-number">{{ $averageRating }}</span>
                    <span class="stat-unit">/5 Rating</span>
                </div>
                <div class="stat-details">
                    <div class="stat-detail">
                        <span class="detail-label">Satisfaction</span>
                        <span class="detail-value success">{{ $satisfactionRate }}%</span>
                    </div>
                    <div class="stat-detail">
                        <span class="detail-label">Response Time</span>
                        <span class="detail-value">{{ $responseTime }}h</span>
                    </div>
                    <div class="stat-detail">
                        <span class="detail-label">Success Rate</span>
                        <span class="detail-value success">{{ $successRate }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="content-grid">
        <!-- Recent Bookings -->
        <div class="content-card content-card-wide">
            <div class="card-header">
                <div class="card-title-section">
                    <h2 class="card-title">Recent Bookings</h2>
                    <p class="card-subtitle">Latest property reservations</p>
                </div>
                <div class="card-actions">
                    <select class="card-select" id="propertyFilter">
                        <option value="">All Properties</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}">{{ $property->name }}</option>
                        @endforeach
                    </select>
                    <a href="{{ route('owner.bookings.index') }}" class="card-button">
                        <i class="fas fa-download"></i>
                        Export
                    </a>
                </div>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Guest</th>
                            <th>Property</th>
                            <th>Dates</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentBookings as $booking)
                            <tr>
                                <td><span class="booking-id">#{{ $booking->booking_reference }}</span></td>
                                <td><span class="guest">{{ $booking->user->name ?? 'N/A' }}</span></td>
                                <td><span class="property">{{ $booking->property->name }}</span></td>
                                <td>
                                    <span class="dates">
                                        {{ \Carbon\Carbon::parse($booking->check_in)->format('M d') }} - 
                                        {{ \Carbon\Carbon::parse($booking->check_out)->format('M d') }}
                                    </span>
                                    <span class="days-badge">{{ $booking->duration_days }} days</span>
                                </td>
                                <td>
                                    <span class="amount">${{ number_format($booking->total_amount, 2) }}</span> 
                                    <span style="color: #10b981; font-weight: 600;">
                                        {{ $booking->payment ? 'Paid' : 'Pending' }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $statusClass = 'status-';
                                        $statusText = '';
                                        switch($booking->status) {
                                            case 'CONFIRMED':
                                                $statusClass .= 'active';
                                                $statusText = 'Active';
                                                break;
                                            case 'PENDING':
                                                $statusClass .= 'pending';
                                                $statusText = 'Pending';
                                                break;
                                            case 'CHECKED_OUT':
                                                $statusClass .= 'completed';
                                                $statusText = 'Completed';
                                                break;
                                            default:
                                                $statusClass .= 'pending';
                                                $statusText = ucfirst(strtolower($booking->status));
                                        }
                                    @endphp
                                    <span class="status {{ $statusClass }}">{{ $statusText }}</span>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a href="mailto:{{ $booking->user->email ?? '#' }}" class="action-btn email-btn" title="Send Email">
                                            <i class="fas fa-envelope"></i>
                                        </a>
                                        <a href="{{ route('owner.bookings.show', $booking->id) }}" class="action-btn email-btn" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($booking->status === 'PENDING')
                                            <form action="{{ route('owner.bookings.update', $booking->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="CONFIRMED">
                                                <button type="submit" class="action-btn cancel-btn" title="Confirm Booking">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8 text-gray-500">
                                    No bookings found. Start by adding a property!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="card-footer">
                <div class="pagination-info">
                    Showing <span class="font-semibold">1 to {{ min($recentBookings->count(), 10) }}</span> of 
                    <span class="font-semibold">{{ $totalBookings }}</span> bookings
                </div>
                <div class="pagination-controls">
                    <a href="{{ route('owner.bookings.index') }}" class="view-all-link">
                        View All Bookings
                    </a>
                </div>
            </div>
        </div>

        <!-- Sidebar Content -->
        <div class="sidebar-grid">
            <!-- Notifications -->
            <div class="content-card">
                <div class="card-header">
                    <h2 class="card-title">Notifications</h2>
                    @if($unreadNotifications > 0)
                        <form action="{{ route('owner.dashboard.mark-read') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">
                                Mark all read
                            </button>
                        </form>
                    @endif
                </div>
                
                <div class="notification-list">
                    @forelse($notifications as $notification)
                        <div class="notification-item {{ !$notification->is_read ? 'new' : '' }}">
                            <div class="notification-icon {{ $this->getNotificationIconClass($notification->type) }}">
                                <i class="{{ $this->getNotificationIcon($notification->type) }}"></i>
                            </div>
                            <div class="notification-content">
                                <p class="notification-title">{{ $notification->title }}</p>
                                <p class="notification-desc">{{ Str::limit($notification->message, 50) }}</p>
                                <p class="notification-time">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-gray-500">
                            No new notifications
                        </div>
                    @endforelse
                </div>
                
                <div class="card-footer">
                    <a href="{{ route('owner.notifications') }}" class="view-all-link">
                        View all notifications
                    </a>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="content-card">
                <h2 class="card-title">Quick Actions</h2>
                <div class="quick-actions-grid">
                    <a href="{{ route('owner.properties.create') }}" class="quick-action-btn">
                        <div class="quick-action-icon">
                            <i class="fas fa-plus"></i>
                        </div>
                        <span>New Property</span>
                    </a>
                    <form action="{{ route('owner.dashboard.quick-action') }}" method="POST" class="contents">
                        @csrf
                        <input type="hidden" name="action" value="export_report">
                        <button type="submit" class="quick-action-btn">
                            <div class="quick-action-icon">
                                <i class="fas fa-print"></i>
                            </div>
                            <span>Print Report</span>
                        </button>
                    </form>
                    <a href="{{ route('owner.email.templates') }}" class="quick-action-btn">
                        <div class="quick-action-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <span>Send Emails</span>
                    </a>
                    <a href="{{ route('owner.properties.analytics') }}" class="quick-action-btn">
                        <div class="quick-action-icon">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <span>Analytics</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Your existing CSS styles here */
/* Add to existing styles */
.status-danger {
    background-color: #fee2e2;
    color: #dc2626;
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
}

.empty-state-icon {
    font-size: 3rem;
    color: #9ca3af;
    margin-bottom: 1rem;
}

.empty-state-text {
    color: #6b7280;
    font-size: 0.875rem;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Property filter functionality
    const propertyFilter = document.getElementById('propertyFilter');
    if (propertyFilter) {
        propertyFilter.addEventListener('change', function() {
            // In a real implementation, this would filter the bookings table
            // For now, we'll reload the page with filter
            if (this.value) {
                window.location.href = `{{ route('owner.dashboard') }}?property_id=${this.value}`;
            } else {
                window.location.href = `{{ route('owner.dashboard') }}`;
            }
        });
    }
    
    // Add confirmation for actions
    document.querySelectorAll('.action-btn[title*="Cancel"], .action-btn[title*="Delete"]').forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to perform this action?')) {
                e.preventDefault();
            }
        });
    });
    
    // Real-time updates (simulated)
    setInterval(() => {
        // In a real app, this would fetch new notifications via AJAX
        // For demonstration, we'll just show a toast
        const notifications = {{ $unreadNotifications }};
        if (notifications > 0 && Math.random() > 0.7) {
            showToast('New notification received!', 'info');
        }
    }, 30000); // Check every 30 seconds
});

function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <i class="fas fa-bell"></i>
            <span>${message}</span>
        </div>
        <button onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Add to page
    document.body.appendChild(toast);
    
    // Remove after 5 seconds
    setTimeout(() => {
        toast.remove();
    }, 5000);
}
</script>
@endpush

@endsection

@php
    // Helper methods for Blade
    function getNotificationIcon($type) {
        switch($type) {
            case 'BOOKING': return 'fas fa-calendar-check';
            case 'PAYMENT': return 'fas fa-money-bill-wave';
            case 'COMPLAINT': return 'fas fa-exclamation-triangle';
            case 'SYSTEM': return 'fas fa-cog';
            default: return 'fas fa-bell';
        }
    }
    
    function getNotificationIconClass($type) {
        switch($type) {
            case 'BOOKING': return 'icon-blue';
            case 'PAYMENT': return 'icon-green';
            case 'COMPLAINT': return 'icon-yellow';
            case 'SYSTEM': return 'icon-purple';
            default: return '';
        }
    }
@endphp