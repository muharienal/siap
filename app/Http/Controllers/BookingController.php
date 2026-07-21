<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Booking;
use App\Models\Facility;
use App\Models\BookingFacility;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\Http\Requests\BookingRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BookingController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of the resource with filters & pagination.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Booking::with(['user', 'room', 'facilities']);

        // Admin can see all, user only their own
        if ($user->role != 1) {
            $query->where('user_id', $user->id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $statusMap = [
                'pending' => 0,
                'approved' => 1,
                'rejected' => 2,
            ];
            $status = $statusMap[$request->status] ?? null;
            if ($status !== null) {
                $query->where('status', $status);
            }
        }

        // Filter by room
        if ($request->filled('room')) {
            $query->where('room_id', $request->room);
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('start_time', $request->date);
        }

        // Filter by search (name, purpose, room name)
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('purpose', 'LIKE', $search)
                  ->orWhereHas('user', function ($q2) use ($search) {
                      $q2->whereHas('employee', function ($q3) use ($search) {
                          $q3->where('full_name', 'LIKE', $search);
                      });
                  })
                  ->orWhereHas('room', function ($q2) use ($search) {
                      $q2->where('name', 'LIKE', $search);
                  });
            });
        }

        // Order by latest
        $query->orderBy('created_at', 'desc');

        // Paginate (10 per page)
        $bookings = $query->paginate(10)->appends($request->query());

        // Get all rooms for filter dropdown
        $rooms = Room::orderBy('name')->get();

        return view('bookings.index', compact('bookings', 'rooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $facilities = Facility::all();
        $rooms = Room::all();
        return view('bookings.add', compact('facilities', 'rooms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BookingRequest $request)
    {
        try {
            DB::beginTransaction();

            $booking_type = 'Regular';
            if (Auth::user()->role == 1) {
                $booking_type = 'Priority';
            }

            $booking = Booking::create([
                'user_id' => Auth::id(),
                'room_id' => $request->room_id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'purpose' => $request->purpose,
                'status' => 0,
                'booking_type' => $booking_type,
            ]);

            if ($request->has('facilities') && is_array($request->facilities)) {
                foreach ($request->facilities as $index => $facilityId) {
                    BookingFacility::create([
                        'booking_id' => $booking->id,
                        'facility_id' => $facilityId,
                        'quantity' => $request->quantities[$index] ?? 1,
                    ]);
                }
            }

            $booking->load(['user.employee', 'room']);
            $this->notificationService->notifyNewBookingToAdmins($booking);

            DB::commit();

            return redirect()->route('bookings.index')
                ->with('success', 'Booking berhasil diajukan dan menunggu persetujuan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking store error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan booking.'])->withInput();
        }
    }

    /**
     * Check room availability (dengan validasi jam kerja 07:00-16:00, interval 30 menit, weekend libur)
     */
    public function checkAvailability(Request $request)
    {
        try {
            $request->validate([
                'room_id' => 'required|integer|exists:rooms,id',
                'start_time' => 'required|date|after_or_equal:today',
                'end_time' => 'required|date|after:start_time',
            ]);

            $roomId = $request->room_id;
            $startTime = Carbon::parse($request->start_time);
            $endTime = Carbon::parse($request->end_time);

            // 1. Weekend check
            if ($startTime->isWeekend()) {
                return response()->json([
                    'available' => false,
                    'message' => 'Peminjaman tidak dapat dilakukan pada hari Sabtu atau Minggu (hari libur).'
                ]);
            }

            // 2. Past date check
            if ($startTime->isPast() && !$startTime->isToday()) {
                return response()->json([
                    'available' => false,
                    'message' => 'Tidak dapat melakukan peminjaman pada tanggal yang sudah lewat.'
                ]);
            }

            // 3. Working hours 07:00-16:00
            $workStart = Carbon::createFromTime(7, 0, 0);
            $workEnd = Carbon::createFromTime(16, 0, 0);

            if ($startTime->lt($workStart) || $endTime->gt($workEnd) || $startTime->gt($workEnd) || $endTime->lt($workStart)) {
                return response()->json([
                    'available' => false,
                    'message' => 'Booking hanya dapat dilakukan pada jam kerja 07:00 – 16:00 WIB.'
                ]);
            }

            // 4. 30-minute interval check
            if ($startTime->minute % 30 != 0 || $endTime->minute % 30 != 0) {
                return response()->json([
                    'available' => false,
                    'message' => 'Waktu mulai dan selesai harus kelipatan 30 menit (contoh: 07:00, 07:30, 08:00).'
                ]);
            }

            $excludeBookingId = $request->exclude_booking_id;

            // 5. Overlap check with approved or pending bookings
            $query = Booking::where('room_id', $roomId)
                ->whereIn('status', [0, 1])
                ->where(function ($q) use ($startTime, $endTime) {
                    $q->whereBetween('start_time', [$startTime, $endTime->copy()->subSecond()])
                        ->orWhereBetween('end_time', [$startTime->copy()->addSecond(), $endTime])
                        ->orWhere(function ($q2) use ($startTime, $endTime) {
                            $q2->where('start_time', '<=', $startTime)
                                ->where('end_time', '>=', $endTime);
                        });
                });

            if ($excludeBookingId) {
                $query->where('id', '!=', $excludeBookingId);
            }

            $conflicts = $query->get();

            if ($conflicts->count() > 0) {
                return response()->json([
                    'available' => false,
                    'message' => 'Ruangan sudah dipesan pada waktu tersebut.',
                    'conflicts' => $conflicts->map(fn($b) => [
                        'id' => $b->id,
                        'start' => $b->start_time,
                        'end' => $b->end_time,
                        'user' => $b->user->email,
                        'purpose' => $b->purpose,
                    ])
                ]);
            }

            return response()->json([
                'available' => true,
                'message' => 'Ruangan tersedia.'
            ]);
        } catch (\Exception $e) {
            Log::error('Availability check error: ' . $e->getMessage());
            return response()->json([
                'available' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        $booking->load('room', 'facilities', 'user.employee', 'bookingFacilities.facility', 'attendances.user', 'processedBy');
        return view('bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        if (Auth::user()->role != 1 && $booking->user_id != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($booking->status != 0) {
            return redirect()->route('bookings.index')
                ->with('error', 'Booking yang sudah disetujui/ditolak tidak dapat diubah.');
        }

        $facilities = Facility::all();
        $rooms = Room::all();
        $bookingFacilities = $booking->bookingFacilities()->with('facility')->get();

        return view('bookings.edit', compact('booking', 'facilities', 'rooms', 'bookingFacilities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BookingRequest $request, Booking $booking)
    {
        if (Auth::user()->role != 1 && $booking->user_id != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($booking->status != 0 && Auth::user()->role != 1) {
            return redirect()->route('bookings.index')
                ->with('error', 'Booking yang sudah disetujui/ditolak tidak dapat diubah.');
        }

        try {
            DB::beginTransaction();

            $oldRoomId = $booking->room_id;
            $oldRoomName = $booking->room->name;
            $oldStartTime = $booking->start_time;
            $oldEndTime = $booking->end_time;
            $isAdminEdit = Auth::user()->role == 1 && Auth::id() != $booking->user_id;

            $booking->update([
                'room_id' => $request->room_id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'purpose' => $request->purpose,
                'status' => $booking->status,
            ]);

            BookingFacility::where('booking_id', $booking->id)->delete();

            if ($request->has('facilities') && is_array($request->facilities)) {
                foreach ($request->facilities as $index => $facilityId) {
                    BookingFacility::create([
                        'booking_id' => $booking->id,
                        'facility_id' => $facilityId,
                        'quantity' => $request->quantities[$index] ?? 1,
                    ]);
                }
            }

            $booking->load(['user.employee', 'room']);

            if ($isAdminEdit) {
                if ($oldRoomId != $request->room_id) {
                    $this->notificationService->notifyRoomChange($booking, $oldRoomName, $booking->room->name);
                }
                if ($oldStartTime != $request->start_time || $oldEndTime != $request->end_time) {
                    $this->notificationService->notifyTimeChange($booking, $oldStartTime, $oldEndTime);
                }
            }

            DB::commit();

            return redirect()->route('bookings.index')
                ->with('success', 'Booking berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking update error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui booking.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        if (Auth::user()->role != 1 && $booking->user_id != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            BookingFacility::where('booking_id', $booking->id)->delete();
            $booking->delete();

            return redirect()->route('bookings.index')
                ->with('success', 'Booking berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Booking delete error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus booking.');
        }
    }

    /**
     * Approve booking (admin only)
     */
    public function approve(Booking $booking)
    {
        if (Auth::user()->role != 1) {
            abort(403, 'Unauthorized action.');
        }

        if ($booking->status != 0) {
            return back()->with('error', 'Booking sudah diproses sebelumnya.');
        }

        try {
            $oldStatus = (string) $booking->status;
            $absentCode = 'MTG-' . strtoupper(uniqid());

            $booking->update([
                'status' => 1,
                'absent_code' => $absentCode,
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);

            $booking->load(['user.employee', 'room']);
            $this->notificationService->notifyBookingStatusChange($booking, $oldStatus, '1');

            return back()->with('success', 'Booking berhasil disetujui dan kode absensi telah dibuat.');
        } catch (\Exception $e) {
            Log::error('Booking approve error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyetujui booking.');
        }
    }

    /**
     * Reject booking (admin only)
     */
    public function reject(Request $request, Booking $booking)
    {
        if (Auth::user()->role != 1) {
            abort(403, 'Unauthorized action.');
        }

        if (!in_array($booking->status, [0, 1])) {
            return back()->with('error', 'Booking dengan status ini tidak dapat ditolak.');
        }

        if ($booking->status == 1) {
            $request->validate([
                'rejection_reason' => 'required|string|min:10|max:500'
            ], [
                'rejection_reason.required' => 'Alasan pembatalan persetujuan harus diisi',
                'rejection_reason.min' => 'Alasan pembatalan minimal 10 karakter',
                'rejection_reason.max' => 'Alasan pembatalan maksimal 500 karakter'
            ]);
        }

        try {
            $oldStatus = (string) $booking->status;
            $actionMessage = $booking->status == 1 ? 'Persetujuan booking berhasil dibatalkan' : 'Booking berhasil ditolak';

            $booking->update([
                'status' => 2,
                'rejection_reason' => $request->input('rejection_reason'),
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);

            $booking->load(['user.employee', 'room']);
            $this->notificationService->notifyBookingStatusChange($booking, $oldStatus, '2');

            return back()->with('success', $actionMessage);
        } catch (\Exception $e) {
            Log::error('Booking reject error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menolak booking.');
        }
    }

    /**
     * Bulk action (admin only)
     */
    public function bulkAction(Request $request)
    {
        if (Auth::user()->role != 1) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'booking_ids' => 'required|array',
            'booking_ids.*' => 'exists:bookings,id',
            'action' => 'required|in:approve,reject'
        ]);

        try {
            $status = $request->action === 'approve' ? 1 : 2;
            $message = $request->action === 'approve' ? 'disetujui' : 'ditolak';

            $bookings = Booking::whereIn('id', $request->booking_ids)
                ->where('status', 0)
                ->get();

            foreach ($bookings as $booking) {
                $updateData = ['status' => $status];
                if ($request->action === 'approve') {
                    $updateData['absent_code'] = 'MTG-' . strtoupper(uniqid());
                }
                $booking->update($updateData);
            }

            return back()->with('success', "Booking yang dipilih berhasil {$message}.");
        } catch (\Exception $e) {
            Log::error('Bulk action error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memproses booking.');
        }
    }

    /**
     * Show QR Code
     */
    public function showQrCode(Booking $booking)
    {
        try {
            if (Auth::user()->role != 0 && $booking->user_id != Auth::id()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
            }

            if ($booking->status != 1 || !$booking->absent_code) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code hanya tersedia untuk booking yang sudah disetujui.'
                ], 400);
            }

            $attendanceUrl = url('/booking/meet/' . $booking->absent_code);

            $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(250)
                ->format('svg')
                ->generate($attendanceUrl);

            return response()->json([
                'success' => true,
                'qr_code' => (string) $qrCode,
                'attendance_url' => $attendanceUrl,
                'booking_info' => [
                    'room' => $booking->room->name,
                    'date' => Carbon::parse($booking->start_time)->format('d M Y'),
                    'time' => Carbon::parse($booking->start_time)->format('H:i') . ' - ' . Carbon::parse($booking->end_time)->format('H:i'),
                    'purpose' => $booking->purpose
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('QR Code generation error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Handle attendance via QR code
     */
    public function attendMeeting($code)
    {
        $booking = Booking::where('absent_code', $code)->where('status', 1)->first();

        if (!$booking) {
            return view('attendance.error', ['message' => 'Kode absensi tidak valid atau booking tidak ditemukan.']);
        }

        $now = now();
        $startTime = Carbon::parse($booking->start_time);
        $endTime = Carbon::parse($booking->end_time);

        $attendanceStartTime = $startTime->copy()->subMinutes(30);
        $attendanceEndTime = $endTime->copy()->addHour();

        if ($now->lt($attendanceStartTime)) {
            return view('attendance.error', [
                'message' => 'Absensi belum dibuka. Anda dapat melakukan absensi 30 menit sebelum acara dimulai.',
                'booking' => $booking
            ]);
        }

        if ($now->gt($attendanceEndTime)) {
            return view('attendance.error', [
                'message' => 'Waktu absensi telah berakhir.',
                'booking' => $booking
            ]);
        }

        if (Auth::check()) {
            return $this->recordAttendance($booking, Auth::user());
        }

        return view('attendance.form', compact('booking'));
    }

    /**
     * Record attendance
     */
    public function recordAttendance($booking, $user = null)
    {
        try {
            $existing = \App\Models\Attendance::where('booking_id', $booking->id)
                ->where('user_id', $user ? $user->id : Auth::id())
                ->first();

            if ($existing) {
                if (Auth::check()) {
                    return redirect()->route('bookings.index')->with('info', 'Anda sudah melakukan absensi untuk meeting ini.');
                } else {
                    return view('attendance.success', ['message' => 'Anda sudah melakukan absensi untuk meeting ini.', 'booking' => $booking]);
                }
            }

            \App\Models\Attendance::create([
                'booking_id' => $booking->id,
                'user_id' => $user ? $user->id : Auth::id(),
                'guest_name' => $user ? $user->name : null,
                'check_in_time' => now(),
            ]);

            if (Auth::check()) {
                return redirect()->route('bookings.index')->with('success', 'Absensi berhasil dicatat.');
            } else {
                return view('attendance.success', ['message' => 'Absensi berhasil dicatat.', 'booking' => $booking]);
            }
        } catch (\Exception $e) {
            Log::error('Attendance record error: ' . $e->getMessage());
            if (Auth::check()) {
                return redirect()->route('bookings.index')->with('error', 'Terjadi kesalahan saat mencatat absensi.');
            } else {
                return view('attendance.error', ['message' => 'Terjadi kesalahan saat mencatat absensi.']);
            }
        }
    }

    /**
     * Submit attendance for guest
     */
    public function submitAttendance(Request $request, $code)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $booking = Booking::where('absent_code', $code)->where('status', 1)->first();

        if (!$booking) {
            return back()->with('error', 'Kode absensi tidak valid.');
        }

        try {
            \App\Models\Attendance::create([
                'booking_id' => $booking->id,
                'guest_name' => $request->name,
                'check_in_time' => now(),
            ]);

            return redirect()->route('attendance.success', $code)->with('success', 'Absensi berhasil dicatat.');
        } catch (\Exception $e) {
            Log::error('Guest attendance error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mencatat absensi.');
        }
    }
}