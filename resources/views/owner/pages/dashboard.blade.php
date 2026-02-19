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
                <h1>Welcome back, {{ Auth::user()->name }}!</h1>
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
                        <span class="stat-number">
                            @if($totalRevenue >= 1000)
                                ${{ number_format($totalRevenue / 1000, 1) }}K
                            @else
                                ${{ number_format($totalRevenue, 0) }}
                            @endif
                        </span>
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
            <a href="{{ route('owner.bookings.index') }}" class="btn-secondary">
                <i class="fas fa-calendar-check"></i>
                View Bookings
            </a>
        </div>
    </div>

    <!-- Stats Grid - Combining property and booking stats -->
    <div class="stats-grid">
        <!-- Property Stats (from properties page) -->
        <div class="stat-card stat-card-blue">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div>
                    <h3 class="stat-title">Property Overview</h3>
                    <p class="stat-subtitle">Total properties & status</p>
                </div>
            </div>
            <div class="stat-content">
                <div class="stat-main">
                    <span class="stat-number">{{ $totalProperties }}</span>
                    <span class="stat-unit">Properties</span>
                </div>
                <div class="stat-details">
                    <div class="stat-detail">
                        <span class="detail-label">Active</span>
                        <span class="detail-value success">{{ $activeProperties }}</span>
                    </div>
                    <div class="stat-detail">
                        <span class="detail-label">Inactive</span>
                        <span class="detail-value warning">{{ $inactiveProperties }}</span>
                    </div>
                    <div class="stat-detail">
                        <span class="detail-label">Draft</span>
                        <span class="detail-value">{{ $draftProperties }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Property Type Stats -->
        <div class="stat-card stat-card-green">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-door-open"></i>
                </div>
                <div>
                    <h3 class="stat-title">Property Types</h3>
                    <p class="stat-subtitle">Hostels & Apartments</p>
                </div>
            </div>
            <div class="stat-content">
                <div class="stat-main">
                    <span class="stat-number">{{ $totalProperties }}</span>
                    <span class="stat-unit">Total</span>
                </div>
                <div class="stat-details">
                    <div class="stat-detail">
                        <span class="detail-label">Hostels</span>
                        <span class="detail-value">{{ $hostelCount }}</span>
                    </div>
                    <div class="stat-detail">
                        <span class="detail-label">Apartments</span>
                        <span class="detail-value">{{ $apartmentCount }}</span>
                    </div>
                    <div class="stat-detail">
                        <span class="detail-label">Occupancy</span>
                        <span class="detail-value success">{{ $occupancyRate }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Stats (from bookings page) -->
        <div class="stat-card stat-card-purple">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <h3 class="stat-title">Booking Status</h3>
                    <p class="stat-subtitle">Current bookings</p>
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
                        <span class="detail-value warning">{{ $pendingBookings }}</span>
                    </div>
                    <div class="stat-detail">
                        <span class="detail-label">Confirmed</span>
                        <span class="detail-value success">{{ $confirmedBookings }}</span>
                    </div>
                    <div class="stat-detail">
                        <span class="detail-label">Checked In</span>
                        <span class="detail-value info">{{ $checkedInBookings }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Stats -->
        <div class="stat-card stat-card-yellow">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div>
                    <h3 class="stat-title">Revenue</h3>
                    <p class="stat-subtitle">Earnings & commission</p>
                </div>
            </div>
            <div class="stat-content">
                <div class="stat-main">
                    <span class="stat-number">
                        @if($monthlyRevenue >= 1000)
                            ${{ number_format($monthlyRevenue / 1000, 1) }}K
                        @else
                            ${{ number_format($monthlyRevenue, 0) }}
                        @endif
                    </span>
                    <span class="stat-unit">This Month</span>
                </div>
                <div class="stat-details">
                    <div class="stat-detail">
                        <span class="detail-label">Total</span>
                        <span class="detail-value">${{ number_format($totalRevenue, 0) }}</span>
                    </div>
                    <div class="stat-detail">
                        <span class="detail-label">Growth</span>
                        <span class="detail-value {{ $revenueGrowth >= 0 ? 'success' : 'warning' }}">
                            {{ $revenueGrowth >= 0 ? '+' : '' }}{{ $revenueGrowth }}%
                        </span>
                    </div>
                    <div class="stat-detail">
                        <span class="detail-label">Commission</span>
                        <span class="detail-value">{{ $commissionRate }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="content-grid">
        <!-- Recent Bookings (from bookings page) -->
        <div class="content-card content-card-wide">
            <div class="card-header">
                <div class="card-title-section">
                    <h2 class="card-title">Recent Bookings</h2>
                    <p class="card-subtitle">Latest property reservations</p>
                </div>
                <div class="card-actions">
                    <form method="GET" action="{{ route('owner.bookings.index') }}" class="flex gap-2">
                        <select name="property_filter" class="card-select" onchange="this.form.submit()">
                            <option value="all">All Properties</option>
                            @foreach($properties as $property)
                                <option value="{{ $property->id }}">{{ $property->name }}</option>
                            @endforeach
                        </select>
                        <a href="{{ route('owner.bookings.export') }}" class="card-button">
                            <i class="fas fa-download"></i>
                            Export
                        </a>
                    </form>
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
                            <td><span class="booking-id">#{{ $booking->booking_reference ?? 'BK-' . str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</span></td>
                            <td><span class="guest">{{ $booking->user->name ?? 'N/A' }}</span></td>
                            <td>
                                <span class="property">{{ $booking->property->name ?? 'N/A' }} 
                                    @if($booking->room)
                                        <br><small>Room #{{ $booking->room->room_number ?? '' }}</small>
                                    @endif
                                </span>
                            </td>
                            <td>
                                <span class="dates">{{ \Carbon\Carbon::parse($booking->check_in)->format('M d') }} - {{ \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') }}</span>
                                <span class="days-badge">{{ $booking->duration_days ?? $booking->check_in->diffInDays($booking->check_out) }} days</span>
                            </td>
                            <td>
                                <span class="amount">${{ number_format($booking->total_amount ?? 0, 0) }}</span> 
                                <span style="color: {{ ($booking->payment_status ?? 'pending') == 'completed' ? '#10b981' : '#f59e0b' }}; font-weight: 600;">
                                    {{ ucfirst($booking->payment_status ?? 'pending') }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusClasses = [
                                        'pending' => 'status-pending',
                                        'confirmed' => 'status-active',
                                        'checked_in' => 'status-active',
                                        'checked_out' => 'status-completed',
                                        'cancelled' => 'status-cancelled'
                                    ];
                                    $statusText = [
                                        'pending' => 'Pending',
                                        'confirmed' => 'Confirmed',
                                        'checked_in' => 'Checked In',
                                        'checked_out' => 'Completed',
                                        'cancelled' => 'Cancelled'
                                    ];
                                    $status = $booking->status ?? 'pending';
                                    $statusClass = $statusClasses[$status] ?? 'status-pending';
                                @endphp
                                <span class="status {{ $statusClass }}">{{ $statusText[$status] ?? ucfirst($status) }}</span>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="mailto:{{ $booking->user->email ?? '' }}" class="action-btn email-btn" title="Send Email">
                                        <i class="fas fa-envelope"></i>
                                    </a>
                                    <a href="{{ route('owner.bookings.show', $booking->id) }}" class="action-btn view-btn" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($booking->status == 'pending')
                                    <button onclick="updateBookingStatus({{ $booking->id }}, 'confirmed')" 
                                            class="action-btn confirm-btn" title="Confirm Booking">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px;">
                                <div style="color: #6b7280;">
                                    <i class="fas fa-calendar-times fa-2x mb-2"></i>
                                    <p>No recent bookings</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="card-footer">
                <a href="{{ route('owner.bookings.index') }}" class="view-all-link">
                    View All Bookings <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        <!-- Sidebar Content -->
        <div class="sidebar-grid">
            <!-- Quick Stats Summary -->
            <div class="content-card">
                <div class="card-header">
                    <h2 class="card-title">Quick Summary</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Today's Bookings:</span>
                            <span class="font-semibold text-purple-600">{{ $todayBookings }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">This Month:</span>
                            <span class="font-semibold">{{ $monthBookings }} bookings</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Completed:</span>
                            <span class="font-semibold text-green-600">{{ $completedBookings }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Cancelled:</span>
                            <span class="font-semibold text-red-600">{{ $cancelledBookings }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t">
                            <span class="text-gray-600">Average Rating:</span>
                            <span class="font-semibold">
                                {{ $averageRating }} / 5 
                                <i class="fas fa-star text-yellow-500 ml-1"></i>
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Satisfaction:</span>
                            <span class="font-semibold text-green-600">{{ $satisfactionRate }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="content-card">
                <h2 class="card-title">Quick Actions</h2>
                <div class="quick-actions-grid">
                    <a href="{{ route('owner.bookings.index') }}" class="quick-action-btn">
                        <div class="quick-action-icon">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <span>Manage Bookings</span>
                    </a>
                    <a href="{{ route('owner.properties.index') }}" class="quick-action-btn">
                        <div class="quick-action-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <span>Properties</span>
                    </a>
                    <a href="{{ route('owner.complaints.index') }}" class="quick-action-btn">
                        <div class="quick-action-icon">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <span>Complaints</span>
                    </a>
                    <a href="{{ route('owner.properties.create') }}" class="quick-action-btn">
                        <div class="quick-action-icon">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <span>Add Property</span>
                    </a>
                </div>
            </div>

            <!-- Recent Complaints -->
            @if($recentComplaints->count() > 0)
            <div class="content-card">
                <div class="card-header">
                    <h2 class="card-title">Recent Complaints</h2>
                    <a href="{{ route('owner.complaints.index') }}" class="text-sm text-purple-600 hover:text-purple-800">
                        View all
                    </a>
                </div>
                <div class="notification-list">
                    @foreach($recentComplaints as $complaint)
                    <div class="notification-item">
                        <div class="notification-icon icon-red">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="notification-content">
                            <p class="notification-title">{{ $complaint->title ?? 'Complaint' }}</p>
                            <p class="notification-desc">{{ Str::limit($complaint->description ?? '', 40) }}</p>
                            <p class="notification-time">{{ $complaint->created_at ? $complaint->created_at->diffForHumans() : '' }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Keep all your existing styles from the original dashboard -->
<style>
/* Welcome Section */
.welcome-section {
    background: linear-gradient(135deg, #26244d 0%, #3f6798 100%);
    border-radius: 1rem;
    padding: 2rem;
    color: white;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.welcome-content {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.welcome-icon {
    width: 4rem;
    height: 4rem;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.welcome-text h1 {
    font-size: 1.875rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.welcome-text p {
    opacity: 0.9;
    margin-bottom: 1rem;
}

.welcome-stats {
    display: flex;
    gap: 2rem;
}

.stat-item {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: bold;
}

.stat-label {
    font-size: 0.875rem;
    opacity: 0.8;
}

.welcome-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.btn-primary, .btn-secondary {
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
    text-decoration: none;
}

.btn-primary {
    background: white;
    color: #3b085f;
}

.btn-primary:hover {
    background: #f8fafc;
    transform: translateY(-1px);
}

.btn-secondary {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.btn-secondary:hover {
    background: rgba(255, 255, 255, 0.3);
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.stat-card {
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: all 0.3s;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.stat-card-blue {
    border-top: 4px solid #3b82f6;
}

.stat-card-green {
    border-top: 4px solid #10b981;
}

.stat-card-purple {
    border-top: 4px solid #8b5cf6;
}

.stat-card-yellow {
    border-top: 4px solid #f59e0b;
}

.stat-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.stat-icon {
    width: 3rem;
    height: 3rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.stat-card-blue .stat-icon {
    background: #dbeafe;
    color: #0f5b84;
}

.stat-card-green .stat-icon {
    background: #d1fae5;
    color: #10b981;
}

.stat-card-purple .stat-icon {
    background: #ede9fe;
    color: #8b5cf6;
}

.stat-card-yellow .stat-icon {
    background: #fef3c7;
    color: #f59e0b;
}

.stat-title {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.stat-subtitle {
    font-size: 0.875rem;
    color: #6b7280;
}

.stat-content {
    margin-top: 1rem;
}

.stat-main {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.stat-main .stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #1f2937;
}

.stat-main .stat-unit {
    font-size: 0.875rem;
    color: #6b7280;
}

.stat-details {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.75rem;
}

.stat-detail {
    display: flex;
    flex-direction: column;
}

.detail-label {
    font-size: 0.75rem;
    color: #6b7280;
    margin-bottom: 0.25rem;
}

.detail-value {
    font-weight: 600;
    color: #1f2937;
}

.detail-value.success {
    color: #10b981;
}

.detail-value.warning {
    color: #f59e0b;
}

.detail-value.info {
    color: #3b82f6;
}

/* Content Grid */
.content-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}

@media (min-width: 1024px) {
    .content-grid {
        grid-template-columns: 2fr 1fr;
    }
}

.content-card {
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.content-card-wide {
    grid-column: 1 / -1;
}

@media (min-width: 1024px) {
    .content-card-wide {
        grid-column: 1;
    }
}

.card-header {
    padding: 1.5rem 1.5rem 0;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 1rem;
}

.card-title-section {
    flex: 1;
}

.card-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.card-subtitle {
    font-size: 0.875rem;
    color: #6b7280;
}

.card-actions {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

.card-select {
    padding: 0.5rem 2rem 0.5rem 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    background-color: white;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    appearance: none;
}

.card-button {
    padding: 0.5rem 1rem;
    background: #3b82f6;
    color: white;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
    text-decoration: none;
    border: none;
    cursor: pointer;
}

.card-button:hover {
    background: #2563eb;
}

/* Table Styles */
.table-container {
    background-color: #ffffff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    overflow-x: auto;
    margin: 1.5rem;
}

.table-container table {
    width: 100%;
    border-collapse: collapse;
    min-width: 900px;
}

.table-container thead {
    background-color: #f8fafc;
    border-bottom: 1.5px solid #e2e8f0;
}

.table-container th {
    padding: 18px 16px;
    text-align: left;
    font-weight: 600;
    color: #475569;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    white-space: nowrap;
    border-right: 1px solid #e2e8f0;
}

.table-container th:last-child {
    border-right: none;
}

.table-container td {
    padding: 18px 16px;
    border-bottom: 1px solid #e5e7eb;
    vertical-align: middle;
}

.table-container tbody tr {
    transition: background-color 0.15s ease, box-shadow 0.15s ease;
}

.table-container tbody tr:hover {
    background-color: #f8fafc;
    box-shadow: inset 3px 0 0 #3b82f6;
}

.booking-id {
    font-weight: 600;
    color: #3b82f6;
}

.guest {
    font-weight: 500;
    color: #1e293b;
}

.property {
    max-width: 200px;
    color: #475569;
    display: block;
}

.property small {
    font-size: 11px;
    color: #6b7280;
}

.dates {
    color: #475569;
    display: block;
}

.days-badge {
    display: inline-block;
    background-color: #e0f2fe;
    color: #0369a1;
    font-size: 12px;
    padding: 3px 8px;
    border-radius: 10px;
    margin-top: 4px;
    font-weight: 500;
}

.amount {
    font-weight: 700;
    color: #1e293b;
    display: block;
}

.status {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    text-align: center;
    min-width: 100px;
}

.status-active {
    background-color: #d1fae5;
    color: #065f46;
}

.status-pending {
    background-color: #fef3c7;
    color: #92400e;
}

.status-completed {
    background-color: #e0e7ff;
    color: #3730a3;
}

.status-cancelled {
    background-color: #fee2e2;
    color: #b91c1c;
}

.actions {
    display: flex;
    gap: 10px;
}

.action-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
    font-size: 16px;
    text-decoration: none;
}

.action-btn:hover {
    transform: translateY(-2px);
}

.email-btn {
    background-color: #dbeafe;
    color: #1d4ed8;
}

.email-btn:hover {
    background-color: #bfdbfe;
}

.view-btn {
    background-color: #e0e7ff;
    color: #3730a3;
}

.view-btn:hover {
    background-color: #c7d2fe;
}

.confirm-btn {
    background-color: #d1fae5;
    color: #059669;
}

.confirm-btn:hover {
    background-color: #a7f3d0;
}

/* Card Footer */
.card-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.view-all-link {
    display: inline-block;
    color: #3b82f6;
    font-weight: 500;
    font-size: 0.875rem;
    text-decoration: none;
}

.view-all-link:hover {
    color: #2563eb;
}

/* Sidebar Grid */
.sidebar-grid {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

/* Notifications */
.notification-list {
    padding: 1rem 1.5rem;
}

.notification-item {
    display: flex;
    gap: 1rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f1f5f9;
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-icon {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #dbeafe;
    color: #3b82f6;
    flex-shrink: 0;
}

.notification-icon.icon-red {
    background: #fee2e2;
    color: #dc2626;
}

.notification-content {
    flex: 1;
}

.notification-title {
    font-weight: 500;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.notification-desc {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 0.25rem;
}

.notification-time {
    font-size: 0.75rem;
    color: #9ca3af;
}

/* Quick Actions */
.quick-actions-grid {
    padding: 1.5rem;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.quick-action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    transition: all 0.2s;
    text-decoration: none;
    color: inherit;
}

.quick-action-btn:hover {
    border-color: #3b82f6;
    background: #f8fafc;
    transform: translateY(-2px);
}

.quick-action-icon {
    width: 3rem;
    height: 3rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    background: #dbeafe;
    color: #3b82f6;
}

.quick-action-btn span {
    font-size: 0.875rem;
    font-weight: 500;
    color: #1f2937;
}
</style>

<script>
function updateBookingStatus(bookingId, status) {
    if (confirm('Are you sure you want to update this booking status?')) {
        fetch(`/owner/bookings/${bookingId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ 
                _method: 'PUT',
                status: status 
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            alert('Error updating booking status');
            console.error(error);
        });
    }
}
</script>
@endsection