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
     * Display notifications page
     */
    public function index()
    {
        $notifications = $this->notificationService->getUserNotifications(Auth::id(), 20);
        $unreadCount = $this->notificationService->getUnreadCount(Auth::id());
        
        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Get notifications for AJAX (for header dropdown)
     */
    public function getNotifications()
    {
        $notifications = $this->notificationService->getUserNotifications(Auth::id(), 5);
        $unreadCount = $this->notificationService->getUnreadCount(Auth::id());
        
        return response()->json([
            'notifications' => $notifications->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'message' => $notification->message,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'booking_id' => $notification->booking_id,
                    'room_name' => $notification->booking?->room?->name ?? null
                ];
            }),
            'unread_count' => $unreadCount
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Notification $notification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notification $notification)
    {
        //
    }
}