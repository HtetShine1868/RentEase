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
                <h1>Welcome back, Owner!</h1>
                <p>Track your properties, manage bookings, and monitor earnings in real-time.</p>
                <div class="welcome-stats">
                    <div class="stat-item">
                        <span class="stat-number">12</span>
                        <span class="stat-label">Properties</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">48</span>
                        <span class="stat-label">Bookings</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">$24.5K</span>
                        <span class="stat-label">Revenue</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="welcome-actions">
            <button class="btn-primary">
                <i class="fas fa-plus"></i>
                Add New Property
            </button>
            <button class="btn-secondary">
                <i class="fas fa-chart-bar"></i>
                View Reports
            </button>
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
                    <span class="stat-number">12</span>
                    <span class="stat-unit">Properties</span>
                </div>
                <div class="stat-details">
                    <div class="stat-detail">
                        <span class="detail-label">Hostels</span>
                        <span class="detail-value">8</span>
                    </div>
                    <div class="stat-detail">
                        <span class="detail-label">Apartments</span>
                        <span class="detail-value">4</span>
                    </div>
                    <div class="stat-detail">
                        <span class="detail-label">Occupancy</span>
                        <span class="detail-value success">83%</span>
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
                    <span class="stat-number">8</span>
                    <span class="stat-unit">Active</span>
                </div>
                <div class="stat-details">
                    <div class="stat-detail">
                        <span class="detail-label">Pending</span>
                        <span class="detail-value warning">3</span>
                    </div>
                    <div class="stat-detail">
                        <span class="detail-label">Completed</span>
                        <span class="detail-value">37</span>
                    </div>
                    <div class="stat-detail">
                        <span class="detail-label">Revenue</span>
                        <span class="detail-value success">$2,450</span>
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
                    <span class="stat-number">$24.5K</span>
                    <span class="stat-unit">Total</span>
                </div>
                <div class="stat-details">
                    <div class="stat-detail">
                        <span class="detail-label">This Month</span>
                        <span class="detail-value success">$2,450</span>
                    </div>
                    <div class="stat-detail">
                        <span class="detail-label">Growth</span>
                        <span class="detail-value success">+14%</span>
                    </div>
                    <div class="stat-detail">
                        <span class="detail-label">Commission</span>
                        <span class="detail-value">5%</span>
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
                    <span class="stat-number">4.7</span>
                    <span class="stat-unit">/5 Rating</span>
                </div>
                <div class="stat-details">
                    <div class="stat-detail">
                        <span class="detail-label">Satisfaction</span>
                        <span class="detail-value success">98%</span>
                    </div>
                    <div class="stat-detail">
                        <span class="detail-label">Response Time</span>
                        <span class="detail-value">2.4h</span>
                    </div>
                    <div class="stat-detail">
                        <span class="detail-label">Success Rate</span>
                        <span class="detail-value success">94%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="content-grid">
        <!-- Recent Bookings - New Table Design -->
        <div class="content-card content-card-wide">
            <div class="card-header">
                <div class="card-title-section">
                    <h2 class="card-title">Recent Bookings</h2>
                    <p class="card-subtitle">Latest property reservations</p>
                </div>
                <div class="card-actions">
                    <select class="card-select">
                        <option>All Properties</option>
                        <option>Hostels</option>
                        <option>Apartments</option>
                    </select>
                    <button class="card-button">
                        <i class="fas fa-download"></i>
                        Export
                    </button>
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
                        <tr>
                            <td><span class="booking-id">#BK-2024-001</span></td>
                            <td><span class="guest">John Doe</span></td>
                            <td><span class="property">Sunshine Apartments Unit #302</span></td>
                            <td><span class="dates">Mar 15 - Apr 30</span><span class="days-badge">47 days</span></td>
                            <td><span class="amount">$1,250</span> <span style="color: #10b981; font-weight: 600;">Paid</span></td>
                            <td><span class="status status-active">Active</span></td>
                            <td>
                                <div class="actions">
                                    <button class="action-btn email-btn" title="Send Email">
                                        <i class="fas fa-envelope"></i>
                                    </button>
                                    <button class="action-btn email-btn" title="Send Reminder">
                                        <i class="fas fa-bell"></i>
                                    </button>
                                    <button class="action-btn email-btn" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="booking-id">#BK-2024-002</span></td>
                            <td><span class="guest">Sarah Johnson</span></td>
                            <td><span class="property">City Hostel Room #12</span></td>
                            <td><span class="dates">Mar 1 - Jun 1</span><span class="days-badge">92 days</span></td>
                            <td><span class="amount">$850</span> <span style="color: #f59e0b; font-weight: 600;">Pending</span></td>
                            <td><span class="status status-pending">Pending</span></td>
                            <td>
                                <div class="actions">
                                    <button class="action-btn cancel-btn" title="Cancel Booking">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <button class="action-btn cancel-btn" title="Send Warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </button>
                                    <button class="action-btn cancel-btn" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="booking-id">#BK-2024-003</span></td>
                            <td><span class="guest">Michael Chen</span></td>
                            <td><span class="property">Luxury Apartments Unit #405</span></td>
                            <td><span class="dates">Jan 15 - Feb 15</span><span class="days-badge">31 days</span></td>
                            <td><span class="amount">$2,100</span> <span style="color: #10b981; font-weight: 600;">Paid</span></td>
                            <td><span class="status status-completed">Completed</span></td>
                            <td>
                                <div class="actions">
                                    <button class="action-btn email-btn" title="Send Email">
                                        <i class="fas fa-envelope"></i>
                                    </button>
                                    <button class="action-btn email-btn" title="Request Review">
                                        <i class="fas fa-star"></i>
                                    </button>
                                    <button class="action-btn email-btn" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="card-footer">
                <div class="pagination-info">
                    Showing <span class="font-semibold">1 to 3</span> of <span class="font-semibold">48</span> bookings
                </div>
                <div class="pagination-controls">
                    <button class="pagination-btn disabled">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="pagination-btn active">1</button>
                    <button class="pagination-btn">2</button>
                    <button class="pagination-btn">3</button>
                    <span class="pagination-ellipsis">...</span>
                    <button class="pagination-btn">10</button>
                    <button class="pagination-btn">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Content -->
        <div class="sidebar-grid">
            <!-- Notifications -->
            <div class="content-card">
                <div class="card-header">
                    <h2 class="card-title">Notifications</h2>
                    <button class="text-sm text-blue-600 hover:text-blue-800">
                        Mark all read
                    </button>
                </div>
                
                <div class="notification-list">
                    <div class="notification-item new">
                        <div class="notification-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="notification-content">
                            <p class="notification-title">New booking received</p>
                            <p class="notification-desc">Apartment #302 booked for 6 months</p>
                            <p class="notification-time">2 hours ago</p>
                        </div>
                    </div>
                    
                    <div class="notification-item">
                        <div class="notification-icon icon-green">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="notification-content">
                            <p class="notification-title">Payment confirmed</p>
                            <p class="notification-desc">$850 payment for Hostel Room #12</p>
                            <p class="notification-time">5 hours ago</p>
                        </div>
                    </div>
                    
                    <div class="notification-item">
                        <div class="notification-icon icon-yellow">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="notification-content">
                            <p class="notification-title">Complaint received</p>
                            <p class="notification-desc">New complaint about water supply</p>
                            <p class="notification-time">1 day ago</p>
                        </div>
                    </div>
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
                    <button class="quick-action-btn">
                        <div class="quick-action-icon">
                            <i class="fas fa-plus"></i>
                        </div>
                        <span>New Booking</span>
                    </button>
                    <button class="quick-action-btn">
                        <div class="quick-action-icon">
                            <i class="fas fa-print"></i>
                        </div>
                        <span>Print Report</span>
                    </button>
                    <button class="quick-action-btn">
                        <div class="quick-action-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <span>Send Emails</span>
                    </button>
                    <button class="quick-action-btn">
                        <div class="quick-action-icon">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <span>Analytics</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

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
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
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

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #1f2937;
}

.stat-unit {
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
    border: 1px solid #2c5bc9;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    background-color: white;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
}

.card-button {
    padding: 0.5rem 1rem;
    background: #142f9d;
    color: white;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
}

.card-button:hover {
    background: #8d149d;
}

/* New Table Styles from your HTML */
.table-container {
    background-color: #ffffff;
    border-radius: 12px;
    border: 1px solid #e2e8f0; /* NEW */
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
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
    border-bottom: 1.5px solid #cbd5f5;
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
    border-bottom: 1px solid #e5e7eb; /* cleaner line */
    vertical-align: middle;
}


.table-container tbody tr {
    transition: background-color 0.15s ease, box-shadow 0.15s ease;
}

.table-container tbody tr:hover {
    background-color: #f8fafc;
    box-shadow: inset 3px 0 0 #3b82f6; /* subtle left accent */
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
}

.dates {
    color: #475569;
}

.days-badge {
    display: inline-block;
    background-color: #e0f2fe;
    color: #0369a1;
    font-size: 12px;
    padding: 3px 8px;
    border-radius: 10px;
    margin-left: 8px;
    font-weight: 500;
}

.amount {
    font-weight: 700;
    color: #1e293b;
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

.cancel-btn {
    background-color: #fee2e2;
    color: #dc2626;
}

.cancel-btn:hover {
    background-color: #fecaca;
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

.pagination-info {
    font-size: 0.875rem;
    color: #6b7280;
}

.pagination-controls {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.pagination-btn {
    width: 2rem;
    height: 2rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.pagination-btn:hover:not(.disabled):not(.active) {
    background: #f3f4f6;
}

.pagination-btn.active {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

.pagination-btn.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.pagination-ellipsis {
    padding: 0 0.5rem;
    color: #6b7280;
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

.notification-item.new {
    background: #f0f9ff;
    margin: 0 -1.5rem;
    padding: 0.75rem 1.5rem;
    border-left: 4px solid #3b82f6;
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

.notification-icon.icon-green {
    background: #d1fae5;
    color: #10b981;
}

.notification-icon.icon-yellow {
    background: #fef3c7;
    color: #f59e0b;
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

.view-all-link {
    display: block;
    text-align: center;
    color: #3b82f6;
    font-weight: 500;
    font-size: 0.875rem;
    padding: 0.5rem;
}

.view-all-link:hover {
    color: #2563eb;
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

.quick-action-btn:nth-child(2) .quick-action-icon {
    background: #d1fae5;
    color: #10b981;
}

.quick-action-btn:nth-child(3) .quick-action-icon {
    background: #ede9fe;
    color: #8b5cf6;
}

.quick-action-btn:nth-child(4) .quick-action-icon {
    background: #fef3c7;
    color: #f59e0b;
}

.quick-action-btn span {
    font-size: 0.875rem;
    font-weight: 500;
    color: #1f2937;
}

/* Footer info from your HTML */
.footer-info {
    margin-top: 20px;
    text-align: right;
    color: #64748b;
    font-size: 14px;
    padding: 0 1.5rem 1.5rem;
}
</style>

<script>
    // Add interactivity to action buttons
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.action-btn').forEach(button => {
            button.addEventListener('click', function() {
                const action = this.getAttribute('title');
                const bookingId = this.closest('tr').querySelector('.booking-id').textContent;
                const guest = this.closest('tr').querySelector('.guest').textContent;
                
                // Show a simple alert for demonstration
                alert(`Action: ${action}\nBooking: ${bookingId}\nGuest: ${guest}`);
                
                // In a real application, you would trigger specific functionality here
                // For example, open a modal, send an API request, etc.
            });
        });
    });
</script>
@endsection