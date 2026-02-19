<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get unread notifications count (for AJAX)
     */
    public function getUnreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
        
        return response()->json(['count' => $count]);
    }

    /**
     * Get recent notifications (for header dropdown)
     */
    public function getRecent()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'time' => $notification->created_at->diffForHumans(),
                    'is_read' => $notification->is_read,
                    'icon' => $this->getNotificationIcon($notification->type),
                    'color' => $this->getNotificationColor($notification->type),
                    'url' => $this->getNotificationUrl($notification)
                ];
            });
        
        return response()->json($notifications);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);
        
        $notification->update([
            'is_read' => true,
            'read_at' => now()
        ]);
        
        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        
        return response()->json(['success' => true]);
    }

    /**
     * Get notification icon based on type
     */
    private function getNotificationIcon($type)
    {
        return match($type) {
            'BOOKING' => 'fa-calendar-check',
            'ORDER' => 'fa-shopping-bag',
            'PAYMENT' => 'fa-credit-card',
            'COMPLAINT' => 'fa-exclamation-circle',
            'SYSTEM' => 'fa-cog',
            'MARKETING' => 'fa-tag',
            default => 'fa-bell'
        };
    }

    /**
     * Get notification color based on type
     */
    private function getNotificationColor($type)
    {
        return match($type) {
            'BOOKING' => 'text-blue-600',
            'ORDER' => 'text-green-600',
            'PAYMENT' => 'text-purple-600',
            'COMPLAINT' => 'text-red-600',
            'SYSTEM' => 'text-gray-600',
            'MARKETING' => 'text-yellow-600',
            default => 'text-indigo-600'
        };
    }

    /**
     * Get notification URL based on related entity
     */
    private function getNotificationUrl($notification)
    {
        if (!$notification->related_entity_type || !$notification->related_entity_id) {
            return '#';
        }

        return match($notification->related_entity_type) {
            'booking' => route('bookings.show', $notification->related_entity_id),
            'food_order' => route('food.orders.show', $notification->related_entity_id),
            'laundry_order' => route('laundry.orders.show', $notification->related_entity_id),
            'payment' => route('payments.show', $notification->related_entity_id),
            'complaint' => route('complaints.show', $notification->related_entity_id),
            default => '#'
        };
    }

    /**
     * Display all notifications page
     */
    public function index(Request $request)
    {
        $query = Notification::where('user_id', Auth::id());

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $isRead = $request->status === 'read';
            $query->where('is_read', $isRead);
        }

        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', today()->subDay());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', now()->month);
                    break;
            }
        }

        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Get statistics
        $stats = [
            'total' => Notification::where('user_id', Auth::id())->count(),
            'unread' => Notification::where('user_id', Auth::id())->where('is_read', false)->count(),
            'read' => Notification::where('user_id', Auth::id())->where('is_read', true)->count(),
        ];

        // Get notification types for filter
        $types = Notification::where('user_id', Auth::id())
            ->select('type')
            ->distinct()
            ->pluck('type');

        return view('notifications.index', compact('notifications', 'stats', 'types'));
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Clear all notifications
     */
    public function clearAll()
    {
        Notification::where('user_id', Auth::id())->delete();

        return response()->json(['success' => true]);
    }
}