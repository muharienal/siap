<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Halaman "Semua Notifikasi". Mendukung filter ?status=unread untuk
     * menampilkan yang belum dibaca saja.
     */
    public function index(Request $request)
    {
        $filter = $request->get('status', 'all');
        $unreadOnly = $filter === 'unread';

        $notifications = $this->notificationService->getUserNotifications(Auth::id(), 50, $unreadOnly);
        $unreadCount = $this->notificationService->getUnreadCount(Auth::id());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'list' => view('notifications.partials._list', compact('notifications', 'filter'))->render(),
                'unread_count' => $unreadCount,
                'signature' => md5($notifications->pluck('id')->implode(',') . '|' . $notifications->pluck('is_read')->implode(',')),
            ]);
        }

        return view('notifications.index', compact('notifications', 'unreadCount', 'filter'));
    }

    /**
     * Get notifications for AJAX (for header dropdown)
     */
    public function getNotifications()
    {
        $notifications = $this->notificationService->getUserNotifications(Auth::id(), 5);
        $unreadCount = $this->notificationService->getUnreadCount(Auth::id());

        return response()->json([
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'message' => $notification->message,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'booking_id' => $notification->booking_id,
                    'room_name' => $notification->booking->room->name ?? null,
                ];
            }),
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request)
    {
        $notificationId = $request->input('notification_id');

        $this->notificationService->markAsRead($notificationId, Auth::id());

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $this->notificationService->markAllAsRead(Auth::id());

        return response()->json(['success' => true]);
    }
}