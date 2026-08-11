<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $selectedDate = $request->get('date', date('Y-m-d'));
        $selectedRoomIds = $request->get('rooms', []);
        $statusFilter = $request->get('status', 'all');
        $startTime = $request->get('start_time', '07:00');
        $endTime = $request->get('end_time', '16:00');

        if (is_string($selectedRoomIds)) {
            $selectedRoomIds = $selectedRoomIds ? explode(',', $selectedRoomIds) : [];
        }
        if (!is_array($selectedRoomIds)) {
            $selectedRoomIds = [];
        }

        $selectedDateObj = Carbon::parse($selectedDate);
        $isWeekend = $selectedDateObj->isWeekend();
        $isToday = $selectedDateObj->isToday();

        // ===== STATISTIK PEMAKAIAN RUANGAN (KHUSUS ADMIN) =====
        // Cuma dihitung untuk admin — selain berat query-nya untuk tiap
        // page load, non-admin juga tidak butuh gambaran global ini.
        $roomUsageStats = null;
        $busyHoursStats = null;

        if ($user->role == 1) {
            $roomUsageStats = Room::withCount(['bookings as approved_count' => function ($q) {
                $q->where('status', 1);
            }])
                ->orderByDesc('approved_count')
                ->limit(6)
                ->get();

            $busyHoursRaw = Booking::where('status', 1)
                ->where('start_time', '>=', Carbon::now()->subDays(30))
                ->selectRaw('HOUR(start_time) as hour, COUNT(*) as total')
                ->groupBy('hour')
                ->pluck('total', 'hour');

            // Normalisasi ke rentang jam kerja (07-15) supaya chart selalu
            // konsisten meskipun belum ada data di jam tertentu.
            $busyHoursStats = [];
            foreach (range(7, 15) as $h) {
                $busyHoursStats[sprintf('%02d:00', $h)] = (int) ($busyHoursRaw[$h] ?? 0);
            }
        }

        // --- JADWAL ---
        $allRooms = Room::with('photos')->get();

        if (!empty($selectedRoomIds) && !in_array('all', $selectedRoomIds)) {
            $rooms = Room::with('photos')->whereIn('id', $selectedRoomIds)->get();
        } else {
            $rooms = $allRooms;
        }

        $timeSlots = $this->generateTimeSlots($startTime, $endTime);

        $currentSlot = null;
        if ($isToday && !$isWeekend) {
            $now = Carbon::now()->format('H:i');
            foreach ($timeSlots as $slot) {
                if ($slot <= $now) {
                    $currentSlot = $slot;
                }
            }
            if (!$currentSlot && count($timeSlots) > 0) {
                $currentSlot = $timeSlots[0];
            }
        }

        $dayBookings = Booking::with(['user', 'room'])
            ->whereDate('start_time', $selectedDate)
            ->whereIn('status', [0, 1, 2])
            ->when(!empty($selectedRoomIds) && !in_array('all', $selectedRoomIds), function ($query) use ($selectedRoomIds) {
                return $query->whereIn('room_id', $selectedRoomIds);
            })
            ->when($statusFilter !== 'all', function ($query) use ($statusFilter) {
                $statusMap = ['approved' => 1, 'pending' => 0, 'rejected' => 2];
                $status = $statusMap[$statusFilter] ?? null;
                if ($status !== null) {
                    return $query->where('status', $status);
                }
                return $query;
            })
            ->get();

        $bookingSchedule = [];
        foreach ($allRooms as $room) {
            $bookingSchedule[$room->id] = [];
            foreach ($timeSlots as $slot) {
                $bookingSchedule[$room->id][$slot] = null;
            }
        }

        foreach ($dayBookings as $booking) {
            $start = Carbon::parse($booking->start_time);
            $end = Carbon::parse($booking->end_time);
            foreach ($timeSlots as $slot) {
                // Pakai tanggal booking itu sendiri ($start), bukan tanggal "sekarang" dari server,
                // supaya perbandingan jam benar-benar akurat terlepas dari tanggal yang dipilih.
                $slotTime = $start->copy()->setTimeFromTimeString($slot);
                if ($slotTime->between($start, $end, true) && $slotTime->lt($end)) {
                    if (array_key_exists($booking->room_id, $bookingSchedule) && array_key_exists($slot, $bookingSchedule[$booking->room_id])) {
                        $bookingSchedule[$booking->room_id][$slot] = $booking;
                    }
                }
            }
        }

        // Pastikan rooms yang dikirim ke view punya photos
        if (!empty($selectedRoomIds) && !in_array('all', $selectedRoomIds)) {
            $rooms = Room::with('photos')->whereIn('id', $selectedRoomIds)->get();
        } else {
            $rooms = $allRooms;
        }

        // ===== DATA UNTUK LIGHTBOX =====
        $roomsData = [];
        foreach ($rooms as $room) {
            $photos = $room->photos ?? collect();
            $roomsData[] = [
                'id' => $room->id,
                'name' => $room->name,
                'photos' => $photos->map(function($photo) {
                    return $photo->photo_url;
                })->toArray(),
            ];
        }

        return view('pages.dashboard', compact(
            'roomUsageStats',
            'busyHoursStats',
            'selectedDate',
            'rooms',
            'timeSlots',
            'bookingSchedule',
            'dayBookings',
            'isWeekend',
            'isToday',
            'selectedRoomIds',
            'allRooms',
            'statusFilter',
            'currentSlot',
            'roomsData',
            'startTime',
            'endTime'
        ));
    }

    private function generateTimeSlots($startTime = '07:00', $endTime = '16:00')
    {
        $slots = [];
        try {
            $start = Carbon::createFromFormat('H:i', $startTime);
            $end = Carbon::createFromFormat('H:i', $endTime);
        } catch (\Exception $e) {
            $start = Carbon::createFromTime(7, 0, 0);
            $end = Carbon::createFromTime(16, 0, 0);
        }
        if ($end->lt($start)) {
            $end = $start->copy()->addHour();
        }
        // FIX: pakai '<' bukan '<=' — jam tutup (mis. 16:00) adalah batas akhir,
        // bukan slot yang boleh dipilih sebagai jam MULAI booking baru.
        // Tanpa ini, muncul slot "+ Booking" jam 16:00 padahal itu sudah lewat
        // jam kerja (rapat tidak mungkin selesai sebelum jam tutup).
        while ($start->lt($end)) {
            $slots[] = $start->format('H:i');
            $start->addMinutes(30);
        }
        return $slots;
    }
}