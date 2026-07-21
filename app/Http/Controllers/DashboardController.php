<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use App\Models\Booking;
use App\Models\Facility;
use App\Models\Attendance;
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

        if (is_string($selectedRoomIds)) {
            $selectedRoomIds = $selectedRoomIds ? explode(',', $selectedRoomIds) : [];
        }
        if (!is_array($selectedRoomIds)) {
            $selectedRoomIds = [];
        }

        $selectedDateObj = Carbon::parse($selectedDate);
        $isWeekend = $selectedDateObj->isWeekend();
        $isToday = $selectedDateObj->isToday();

        $totalRooms = Room::count();
        $totalUsers = User::count();
        $totalFacilities = Facility::count();

        if ($user->role == 0) {
            $totalBookings = Booking::count();
            $pendingBookings = Booking::where('status', 0)->count();
            $approvedBookings = Booking::where('status', 1)->count();
            $rejectedBookings = Booking::where('status', 2)->count();
        } else {
            $totalBookings = Booking::where('user_id', $user->id)->count();
            $pendingBookings = Booking::where('user_id', $user->id)->where('status', 0)->count();
            $approvedBookings = Booking::where('user_id', $user->id)->where('status', 1)->count();
            $rejectedBookings = Booking::where('user_id', $user->id)->where('status', 2)->count();
        }

        $popularRoom = Room::withCount('bookings')->orderBy('bookings_count', 'desc')->first();
        $bookingsByMonth = Booking::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $roomUsage = Room::withCount('bookings')->orderBy('bookings_count', 'desc')->limit(5)->get();
        $totalAttendances = Attendance::count();

        // --- JADWAL ---
        $allRooms = Room::with('photos')->get();

        if (!empty($selectedRoomIds) && !in_array('all', $selectedRoomIds)) {
            $rooms = Room::with('photos')->whereIn('id', $selectedRoomIds)->get();
        } else {
            $rooms = $allRooms;
        }

        $timeSlots = $this->generateTimeSlots();

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
                $slotTime = Carbon::createFromFormat('H:i', $slot);
                if ($slotTime->between($start, $end, true) && $slotTime->lt($end)) {
                    if (isset($bookingSchedule[$booking->room_id][$slot])) {
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
            'totalRooms',
            'totalUsers',
            'totalFacilities',
            'totalBookings',
            'pendingBookings',
            'approvedBookings',
            'rejectedBookings',
            'popularRoom',
            'bookingsByMonth',
            'roomUsage',
            'totalAttendances',
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
            'roomsData'
        ));
    }

    private function generateTimeSlots()
    {
        $slots = [];
        $start = Carbon::createFromTime(7, 0, 0);
        $end = Carbon::createFromTime(16, 0, 0);
        while ($start->lte($end)) {
            $slots[] = $start->format('H:i');
            $start->addMinutes(30);
        }
        return $slots;
    }
}