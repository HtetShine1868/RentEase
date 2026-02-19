<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Complaint;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\PropertyRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display owner dashboard with real data
     */
    public function index()
    {
        $owner = Auth::user();
        
        // Get owner's property IDs
        $propertyIds = Property::where('owner_id', $owner->id)->pluck('id');
        
        // ========== PROPERTY STATS (from properties page) ==========
        $totalProperties = $propertyIds->count();
        
        $hostelCount = Property::where('owner_id', $owner->id)
            ->where('type', 'HOSTEL')
            ->count();
            
        $apartmentCount = Property::where('owner_id', $owner->id)
            ->where('type', 'APARTMENT')
            ->count();
        
        // Property status counts
        $activeProperties = Property::where('owner_id', $owner->id)
            ->where('status', 'ACTIVE')
            ->count();
            
        $inactiveProperties = Property::where('owner_id', $owner->id)
            ->where('status', 'INACTIVE')
            ->count();
            
        $draftProperties = Property::where('owner_id', $owner->id)
            ->where('status', 'DRAFT')
            ->count();
            
        $pendingProperties = Property::where('owner_id', $owner->id)
            ->where('status', 'PENDING')
            ->count();
        
        // Calculate occupancy rate
        $totalRooms = Room::whereIn('property_id', $propertyIds)->count();
        $occupiedRooms = Room::whereIn('property_id', $propertyIds)
            ->where('status', 'OCCUPIED')
            ->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;
        
        // ========== BOOKING STATS (from bookings page) ==========
        $totalBookings = Booking::whereIn('property_id', $propertyIds)->count();
        
        // Today's bookings
        $todayBookings = Booking::whereIn('property_id', $propertyIds)
            ->whereDate('created_at', today())
            ->count();
        
        // This month's bookings
        $monthBookings = Booking::whereIn('property_id', $propertyIds)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // Booking status counts
        $pendingBookings = Booking::whereIn('property_id', $propertyIds)
            ->where('status', 'pending')
            ->count();
            
        $confirmedBookings = Booking::whereIn('property_id', $propertyIds)
            ->where('status', 'confirmed')
            ->count();
            
        $checkedInBookings = Booking::whereIn('property_id', $propertyIds)
            ->where('status', 'checked_in')
            ->count();
            
        $checkedOutBookings = Booking::whereIn('property_id', $propertyIds)
            ->where('status', 'checked_out')
            ->count();
            
        $cancelledBookings = Booking::whereIn('property_id', $propertyIds)
            ->where('status', 'cancelled')
            ->count();
        
        // Active bookings (confirmed + checked_in)
        $activeBookings = $confirmedBookings + $checkedInBookings;
        
        // Completed bookings (checked_out)
        $completedBookings = $checkedOutBookings;
        
        // ========== REVENUE STATS ==========
        // Get all completed payments for owner's properties
        $payments = Payment::whereIn('property_id', $propertyIds)
            ->where('status', 'COMPLETED')
            ->get();
        
        // Total revenue (in thousands)
        $totalRevenue = $payments->sum('amount');
        $totalRevenueInK = $totalRevenue > 1000 ? round($totalRevenue / 1000, 1) : $totalRevenue;
        
        // Monthly revenue
        $monthlyRevenue = Payment::whereIn('property_id', $propertyIds)
            ->where('status', 'COMPLETED')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
        
        // Previous month revenue for growth
        $previousMonthRevenue = Payment::whereIn('property_id', $propertyIds)
            ->where('status', 'COMPLETED')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('amount');
        
        $revenueGrowth = $previousMonthRevenue > 0 
            ? round((($monthlyRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100, 1)
            : ($monthlyRevenue > 0 ? 100 : 0);
        
        // Get commission rate from config
        $commissionRate = DB::table('commission_configs')
            ->where('service_type', 'HOSTEL')
            ->value('rate') ?? 5;
        
        // ========== RATING STATS ==========
        $averageRating = PropertyRating::whereIn('property_id', $propertyIds)
            ->avg('overall_rating') ?? 0;
        $averageRating = round($averageRating, 1);
        
        $totalRatings = PropertyRating::whereIn('property_id', $propertyIds)->count();
        
        $highRatings = PropertyRating::whereIn('property_id', $propertyIds)
            ->where('overall_rating', '>=', 4)
            ->count();
        $satisfactionRate = $totalRatings > 0 ? round(($highRatings / $totalRatings) * 100) : 0;
        
        // Booking success rate (non-cancelled)
        $successfulBookings = $totalBookings - $cancelledBookings;
        $bookingSuccessRate = $totalBookings > 0 
            ? round(($successfulBookings / $totalBookings) * 100) 
            : 0;
        
        // Average response time (from notifications or complaints)
        $avgResponseTime = $this->calculateAverageResponseTime($owner);
        
        // ========== RECENT BOOKINGS (from bookings page) ==========
        $recentBookings = Booking::with(['user', 'property', 'room'])
            ->whereIn('property_id', $propertyIds)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // ========== RECENT COMPLAINTS ==========
        // Get owner's service providers if any
        $serviceProviderIds = DB::table('service_providers')
            ->where('user_id', $owner->id)
            ->pluck('id');
        
        $recentComplaints = Complaint::with(['user'])
            ->where(function($query) use ($propertyIds, $serviceProviderIds) {
                $query->where(function($q) use ($propertyIds) {
                    $q->where('related_type', 'PROPERTY')
                      ->whereIn('related_id', $propertyIds);
                });
                
                if ($serviceProviderIds->isNotEmpty()) {
                    $query->orWhere(function($q) use ($serviceProviderIds) {
                        $q->where('related_type', 'SERVICE_PROVIDER')
                          ->whereIn('related_id', $serviceProviderIds);
                    });
                }
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // ========== NOTIFICATIONS ==========
        $recentNotifications = Notification::where('user_id', $owner->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $unreadNotifications = Notification::where('user_id', $owner->id)
            ->where('is_read', false)
            ->count();
        
        // ========== PROPERTIES LIST (for dropdown) ==========
        $properties = Property::where('owner_id', $owner->id)
            ->select('id', 'name', 'type', 'status')
            ->get();
        
        // ========== BOOKING STATS ARRAY ==========
        $bookingStats = [
            'today_count' => $todayBookings,
            'month_count' => $monthBookings,
            'month_revenue' => $monthlyRevenue,
            'status_counts' => [
                'PENDING' => $pendingBookings,
                'CONFIRMED' => $confirmedBookings,
                'CHECKED_IN' => $checkedInBookings,
                'CHECKED_OUT' => $checkedOutBookings,
                'CANCELLED' => $cancelledBookings
            ]
        ];
        
        // ========== PROPERTY STATS ARRAY ==========
        $propertyStats = [
            'total' => $totalProperties,
            'active' => $activeProperties,
            'inactive' => $inactiveProperties,
            'draft' => $draftProperties,
            'pending' => $pendingProperties,
            'hostels' => $hostelCount,
            'apartments' => $apartmentCount
        ];
        
        return view('owner.pages.dashboard', compact(
            // Property stats
            'totalProperties',
            'hostelCount',
            'apartmentCount',
            'activeProperties',
            'inactiveProperties',
            'draftProperties',
            'pendingProperties',
            'occupancyRate',
            'propertyStats',
            
            // Booking stats
            'totalBookings',
            'todayBookings',
            'monthBookings',
            'pendingBookings',
            'confirmedBookings',
            'checkedInBookings',
            'checkedOutBookings',
            'cancelledBookings',
            'activeBookings',
            'completedBookings',
            'bookingStats',
            
            // Revenue stats
            'totalRevenue',
            'totalRevenueInK',
            'monthlyRevenue',
            'revenueGrowth',
            'commissionRate',
            
            // Rating stats
            'averageRating',
            'totalRatings',
            'satisfactionRate',
            'bookingSuccessRate',
            'avgResponseTime',
            
            // Lists
            'recentBookings',
            'recentComplaints',
            'recentNotifications',
            'unreadNotifications',
            'properties'
        ));
    }
    
    /**
     * Calculate average response time
     */
    private function calculateAverageResponseTime($owner)
    {
        // This is a placeholder - implement based on your business logic
        // Could be from complaints, messages, etc.
        return '2.4';
    }
    
    /**
     * Export bookings
     */
    public function exportBookings(Request $request)
    {
        $owner = Auth::user();
        $propertyIds = Property::where('owner_id', $owner->id)->pluck('id');
        
        $query = Booking::with(['user', 'property', 'room'])
            ->whereIn('property_id', $propertyIds);
        
        // Apply filters
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('check_in', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('check_out', '<=', $request->date_to);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_reference', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('property', function($propQuery) use ($search) {
                      $propQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        $bookings = $query->orderBy('created_at', 'desc')->get();
        
        $filename = 'bookings-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($bookings) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM
            
            fputcsv($file, [
                'Booking ID', 'Guest Name', 'Guest Email', 'Property', 'Room',
                'Check In', 'Check Out', 'Nights', 'Total Amount', 'Commission',
                'Your Earnings', 'Status', 'Payment Status', 'Booking Date'
            ]);
            
            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->booking_reference ?? 'BK-' . str_pad($booking->id, 6, '0', STR_PAD_LEFT),
                    $booking->user->name ?? 'N/A',
                    $booking->user->email ?? 'N/A',
                    $booking->property->name ?? 'N/A',
                    $booking->room->room_number ?? 'N/A',
                    $booking->check_in ? $booking->check_in->format('Y-m-d') : 'N/A',
                    $booking->check_out ? $booking->check_out->format('Y-m-d') : 'N/A',
                    $booking->duration_days ?? $booking->check_in->diffInDays($booking->check_out),
                    number_format($booking->total_amount ?? 0, 2),
                    number_format($booking->commission_amount ?? 0, 2),
                    number_format(($booking->total_amount - $booking->commission_amount) ?? 0, 2),
                    ucfirst($booking->status ?? 'pending'),
                    ucfirst($booking->payment_status ?? 'pending'),
                    $booking->created_at ? $booking->created_at->format('Y-m-d H:i:s') : 'N/A'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Update booking status
     */
    public function updateBookingStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled'
        ]);
        
        $owner = Auth::user();
        $propertyIds = Property::where('owner_id', $owner->id)->pluck('id');
        
        $booking = Booking::whereIn('property_id', $propertyIds)
            ->findOrFail($id);
        
        $oldStatus = $booking->status;
        $booking->status = $request->status;
        $booking->save();
        
        // Update room status if checking in/out
        if ($request->status == 'checked_in' && $booking->room_id) {
            Room::where('id', $booking->room_id)->update(['status' => 'OCCUPIED']);
        } elseif ($request->status == 'checked_out' && $booking->room_id) {
            Room::where('id', $booking->room_id)->update(['status' => 'AVAILABLE']);
        } elseif ($request->status == 'cancelled' && $booking->room_id) {
            Room::where('id', $booking->room_id)->update(['status' => 'AVAILABLE']);
        }
        
        // Create notification for user
        Notification::create([
            'user_id' => $booking->user_id,
            'type' => 'BOOKING',
            'title' => 'Booking Status Updated',
            'message' => "Your booking #{$booking->booking_reference} status has been updated from " . 
                        ucfirst($oldStatus) . " to " . ucfirst($request->status),
            'related_entity_type' => 'booking',
            'related_entity_id' => $booking->id
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Booking status updated successfully'
        ]);
    }
    
    /**
     * Send reminder to guest
     */
    public function sendReminder(Request $request, $id)
    {
        $owner = Auth::user();
        $propertyIds = Property::where('owner_id', $owner->id)->pluck('id');
        
        $booking = Booking::whereIn('property_id', $propertyIds)->findOrFail($id);
        
        // Create notification
        Notification::create([
            'user_id' => $booking->user_id,
            'type' => 'BOOKING',
            'title' => 'Booking Reminder',
            'message' => "Reminder: Your check-in at {$booking->property->name} is on " . 
                        $booking->check_in->format('M d, Y'),
            'related_entity_type' => 'booking',
            'related_entity_id' => $booking->id
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Reminder sent successfully'
        ]);
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead()
    {
        $owner = Auth::user();
        
        Notification::where('user_id', $owner->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        
        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }
}