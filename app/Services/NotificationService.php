<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Booking;

class NotificationService
{
    /**
     * Create notification for booking status change
     */
    public function notifyBookingStatusChange(Booking $booking, string $oldStatus, string $newStatus)
    {
        $statusMessages = [
            '0' => 'Pending',
            '1' => 'Disetujui', 
            '2' => 'Ditolak'
        ];

        $message = "Status peminjaman ruangan {$booking->room->name} Anda telah diubah dari {$statusMessages[$oldStatus]} menjadi {$statusMessages[$newStatus]}.";
        
        if ($newStatus == '1') {
            $message .= " Anda dapat menggunakan ruangan pada " . $booking->start_time->format('d M Y, H:i') . " - " . $booking->end_time->format('H:i') . ".";
        } elseif ($newStatus == '2') {
            $message .= " Mohon ajukan peminjaman ulang dengan waktu atau ruangan yang berbeda.";
            if ($booking->rejection_reason) {
                $message .= " Alasan: " . $booking->rejection_reason;
            }
        }

        $this->createNotification($booking->user_id, $message, $booking->id);
    }

    /**
     * Create notification for booking room change by admin
     */
    public function notifyRoomChange(Booking $booking, string $oldRoomName, string $newRoomName)
    {
        $message = "Ruangan peminjaman Anda telah dipindahkan dari {$oldRoomName} ke {$newRoomName} pada " . 
                   $booking->start_time->format('d M Y, H:i') . " - " . $booking->end_time->format('H:i') . 
                   " oleh admin.";

        $this->createNotification($booking->user_id, $message, $booking->id);
    }

    /**
     * Create notification for new booking to all admins
     */
    public function notifyNewBookingToAdmins(Booking $booking)
    {
        $userName = $booking->user->full_name ?? $booking->user->email;
        $message = "Peminjaman ruangan baru dari {$userName} untuk ruangan {$booking->room->name} pada " . 
                   $booking->start_time->format('d M Y, H:i') . " - " . $booking->end_time->format('H:i') . 
                   ". Keperluan: {$booking->purpose}";

        // Get all admin users (role = 1)
        $admins = User::where('role', 1)->where('is_active', true)->get();
        
        foreach ($admins as $admin) {
            $this->createNotification($admin->id, $message, $booking->id);
        }
    }

    /**
     * Create notification for booking time change
     */
    public function notifyTimeChange(Booking $booking, $oldStartTime, $oldEndTime)
    {
        $message = "Waktu peminjaman ruangan {$booking->room->name} Anda telah diubah dari " . 
                   \Carbon\Carbon::parse($oldStartTime)->format('d M Y, H:i') . " - " . 
                   \Carbon\Carbon::parse($oldEndTime)->format('H:i') . " menjadi " .
                   $booking->start_time->format('d M Y, H:i') . " - " . $booking->end_time->format('H:i') . 
                   " oleh admin.";

        $this->createNotification($booking->user_id, $message, $booking->id);
    }

    /**
     * Create notification for booking cancellation
     */
    public function notifyBookingCancellation(Booking $booking, string $reason = '')
    {
        $message = "Peminjaman ruangan {$booking->room->name} pada " . 
                   $booking->start_time->format('d M Y, H:i') . " - " . $booking->end_time->format('H:i') . 
                   " telah dibatalkan.";
        
        if ($reason) {
            $message .= " Alasan: {$reason}";
        }

        $this->createNotification($booking->user_id, $message, $booking->id);
    }

    /**
     * Create base notification
     */
    private function createNotification(int $userId, string $message, int $bookingId = null)
    {
        Notification::create([
            'user_id' => $userId,
            'message' => $message,
            'booking_id' => $bookingId,
            'is_read' => false
        ]);
    }

    /**
     * Get unread notifications count for user
     */
    public function getUnreadCount(int $userId)
    {
        return Notification::where('user_id', $userId)
                         ->where('is_read', false)
                         ->count();
    }

    /**
     * Get notifications for user with pagination
     */
    public function getUserNotifications(int $userId, int $limit = 10)
    {
        return Notification::where('user_id', $userId)
                         ->with(['booking.room'])
                         ->orderBy('created_at', 'desc')
                         ->limit($limit)
                         ->get();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId, int $userId)
    {
        return Notification::where('id', $notificationId)
                         ->where('user_id', $userId)
                         ->update(['is_read' => true]);
    }

    /**
     * Mark all notifications as read for user
     */
    public function markAllAsRead(int $userId)
    {
        return Notification::where('user_id', $userId)
                         ->where('is_read', false)
                         ->update(['is_read' => true]);
    }
}