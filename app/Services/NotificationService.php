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
        $userName = $booking->user->full_name ?? 'Pengguna';
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
     * Notifikasi ke karyawan yang bookingnya perlu ditinjau ulang akibat
     * bentrok jadwal. Sengaja TIDAK menyebut "prioritas" — karyawan biasa
     * tidak perlu (dan tidak boleh) tahu ada sistem prioritas, supaya tidak
     * menimbulkan kecemburuan antar pengguna. Detail lengkap hanya
     * dikirim ke admin lewat notifyPriorityConflictToAdmins().
     */
    public function notifyPriorityConflict(Booking $displacedBooking, Booking $priorityBooking)
    {
        $message = "Peminjaman ruangan {$displacedBooking->room->name} Anda pada " .
                   $displacedBooking->start_time->format('d M Y, H:i') . " - " . $displacedBooking->end_time->format('H:i') .
                   " sedang ditinjau ulang oleh admin karena ada penyesuaian jadwal. " .
                   "Admin akan segera menghubungi Anda jika perlu dialihkan ke ruangan atau waktu lain.";

        $this->createNotification($displacedBooking->user_id, $message, $displacedBooking->id);
    }

    /**
     * Notifikasi ke seluruh admin saat terjadi konflik prioritas, supaya
     * segera ditindaklanjuti (dialihkan ke ruangan/jam lain). Detail
     * lengkap (termasuk kata "prioritas") HANYA boleh dikirim ke admin —
     * jangan pernah dipakai untuk notifikasi ke karyawan biasa.
     */
    public function notifyPriorityConflictToAdmins(Booking $displacedBooking, Booking $priorityBooking)
    {
        $priorityUserName = $priorityBooking->user->full_name ?? 'Direksi';
        $displacedUserName = $displacedBooking->user->full_name ?? 'Unknown';
        $message = "Konflik jadwal prioritas: peminjaman {$priorityUserName} bentrok dengan peminjaman " .
                   "{$displacedUserName} di ruangan {$displacedBooking->room->name} pada " .
                   $displacedBooking->start_time->format('d M Y, H:i') . " - " . $displacedBooking->end_time->format('H:i') .
                   ". Perlu dialihkan segera.";

        $admins = User::where('role', 1)->where('is_active', true)->get();
        foreach ($admins as $admin) {
            $this->createNotification($admin->id, $message, $displacedBooking->id);
        }
    }

    /**
     * Notifikasi ke karyawan saat bookingnya berhasil dialihkan admin ke
     * ruangan/waktu lain. Netral — tidak menyebut "prioritas".
     */
    public function notifyRelocated(Booking $booking, string $oldRoomName, $oldStartTime, string $note)
    {
        $message = "Peminjaman ruangan Anda telah dialihkan oleh admin dari {$oldRoomName} (" .
                   \Carbon\Carbon::parse($oldStartTime)->format('d M Y, H:i') . ") ke {$booking->room->name} (" .
                   $booking->start_time->format('d M Y, H:i') . " - " . $booking->end_time->format('H:i') .
                   "). Catatan admin: \"{$note}\"";

        $this->createNotification($booking->user_id, $message, $booking->id);
    }

    /**
     * Notifikasi ke karyawan saat bentrok jadwal pada bookingnya batal
     * dengan sendirinya — booking miliknya aman kembali di jadwal semula.
     * Netral — tidak menyebut "prioritas".
     */
    public function notifyConflictResolvedAutomatically(Booking $booking)
    {
        $message = "Kabar baik — peminjaman ruangan {$booking->room->name} Anda pada " .
                   $booking->start_time->format('d M Y, H:i') . " - " . $booking->end_time->format('H:i') .
                   " tidak jadi dialihkan. Jadwal Anda tetap seperti semula.";

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
     * Get notifications for user with pagination, opsional filter belum dibaca saja.
     */
    public function getUserNotifications(int $userId, int $limit = 10, bool $unreadOnly = false)
    {
        $query = Notification::where('user_id', $userId)
                         ->with(['booking.room'])
                         ->orderBy('created_at', 'desc');

        if ($unreadOnly) {
            $query->where('is_read', false);
        }

        return $query->limit($limit)->get();
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