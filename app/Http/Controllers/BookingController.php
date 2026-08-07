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
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class BookingController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display listing with advanced filters & stats.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Booking::with(['user', 'room', 'facilities', 'bookingFacilities.facility']);

        // Role-based access
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

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('start_time', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('start_time', '<=', $request->date_to);
        }

        // Filter by specific date
        if ($request->filled('date')) {
            $query->whereDate('start_time', $request->date);
        }

        // Search
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('purpose', 'LIKE', $search)
                  ->orWhereHas('user', function ($q2) use ($search) {
                      $q2->where('full_name', 'LIKE', $search)
                         ->orWhere('nip', 'LIKE', $search);
                  })
                  ->orWhereHas('room', function ($q2) use ($search) {
                      $q2->where('name', 'LIKE', $search);
                  });
            });
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'date_asc':
                $query->orderBy('start_time', 'asc');
                break;
            case 'date_desc':
                $query->orderBy('start_time', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $bookings = $query->paginate(10)->appends($request->query());
        $rooms = Room::orderBy('name')->get();

        // Stats for cards
        $statsQuery = $user->role == 1 ? Booking::query() : Booking::where('user_id', $user->id);
        $stats = [
            'total' => $statsQuery->count(),
            'pending' => (clone $statsQuery)->where('status', 0)->count(),
            'approved' => (clone $statsQuery)->where('status', 1)->count(),
            'rejected' => (clone $statsQuery)->where('status', 2)->count(),
            'today' => (clone $statsQuery)->whereDate('start_time', Carbon::today())->count(),
        ];

        return view('bookings.index', compact('bookings', 'rooms', 'stats'));
    }

    /**
     * Show create form with pre-filled data.
     */
    public function create(Request $request)
    {
        $facilities = Facility::where('name', 'Konsumsi')->orderBy('name')->get();
        $rooms = Room::with(['photos', 'facilities'])->where('status', 1)->orderBy('name')->get();

        $prefill = [
            'room_id' => $request->get('room'),
            'date' => $request->get('date', date('Y-m-d')),
            'start_time' => $request->get('start_time', '07:00'),
            'end_time' => $request->get('end_time', '08:00'),
        ];

        return view('bookings.create', compact('facilities', 'rooms', 'prefill'));
    }

    /**
     * Store new booking with transaction.
     */
    public function store(BookingRequest $request)
    {
        try {
            DB::beginTransaction();

            // Tipe booking: hanya admin yang boleh pilih manual (Regular/Priority).
            // Karyawan biasa selalu Regular, gak bisa klaim Priority sendiri.
            $bookingType = 'Regular';
            if (Auth::user()->role == 1 && in_array($request->booking_type, ['Regular', 'Priority'])) {
                $bookingType = $request->booking_type;
            }

            $booking = Booking::create([
                'user_id' => Auth::id(),
                'room_id' => $request->room_id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'purpose' => $request->purpose,
                'status' => 0,
                'booking_type' => $bookingType,
            ]);

            // Save facilities
            if ($request->has('facilities') && is_array($request->facilities)) {
                foreach ($request->facilities as $index => $facilityId) {
                    BookingFacility::create([
                        'booking_id' => $booking->id,
                        'facility_id' => $facilityId,
                        'quantity' => $request->quantities[$index] ?? 1,
                    ]);
                }
            }

            // Booking Priority otomatis "menang" atas booking Regular yang masih pending
            // di ruangan & jam yang sama (mis. rapat direksi mendadak menggeser booking biasa).
            if ($bookingType === 'Priority') {
                $this->autoRejectConflictingRegularBookings($booking);
            }

            $booking->load(['user', 'room']);
            $this->notificationService->notifyNewBookingToAdmins($booking);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking berhasil diajukan dan menunggu persetujuan.',
                    'redirect' => route('bookings.show', $booking->id)
                ]);
            }

            return redirect()->route('bookings.show', $booking->id)
                ->with('success', 'Booking berhasil diajukan dan menunggu persetujuan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking store error: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan booking: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan booking.'])->withInput();
        }
    }

    /**
     * Tolak otomatis booking Regular (pending ATAU sudah approved) yang bentrok jadwal
     * dengan booking Priority yang baru dibuat (di ruangan yang sama). Priority selalu menang.
     */
    private function autoRejectConflictingRegularBookings(Booking $priorityBooking): void
    {
        $conflicts = Booking::where('room_id', $priorityBooking->room_id)
            ->where('booking_type', 'Regular')
            ->whereIn('status', [0, 1]) // pending & approved, dua-duanya bisa digeser Priority
            ->where('id', '!=', $priorityBooking->id)
            ->where(function ($q) use ($priorityBooking) {
                $q->whereBetween('start_time', [$priorityBooking->start_time, $priorityBooking->end_time])
                  ->orWhereBetween('end_time', [$priorityBooking->start_time, $priorityBooking->end_time])
                  ->orWhere(function ($q2) use ($priorityBooking) {
                      $q2->where('start_time', '<=', $priorityBooking->start_time)
                         ->where('end_time', '>=', $priorityBooking->end_time);
                  });
            })
            ->get();

        foreach ($conflicts as $conflict) {
            $oldStatus = (string) $conflict->status;

            $conflict->update([
                'status' => 2,
                'rejection_reason' => 'Ruangan & jadwal ini digunakan untuk peminjaman prioritas.',
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);

            $conflict->load(['user', 'room']);
            $this->notificationService->notifyBookingStatusChange($conflict, $oldStatus, '2');
        }
    }

    /**
     * Display booking detail with timeline.
     */
    public function show(Booking $booking)
    {
        $booking->load([
            'room.photos',
            'facilities',
            'user',
            'bookingFacilities.facility',
            'attendances.user',
            'processedBy',
        ]);

        $timeline = $this->buildTimeline($booking);

        return view('bookings.show', compact('booking', 'timeline'));
    }

    /**
     * Show edit form.
     */
    public function edit(Booking $booking)
    {
        if (Auth::user()->role != 1 && $booking->user_id != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($booking->status != 0 && Auth::user()->role != 1) {
            return redirect()->route('bookings.index')
                ->with('error', 'Booking yang sudah disetujui/ditolak tidak dapat diubah.');
        }

        $facilities = Facility::where('name', 'Konsumsi')->orderBy('name')->get();
        $rooms = Room::with('photos')->orderBy('name')->get();
        $bookingFacilities = $booking->bookingFacilities()->with('facility')->get();

        return view('bookings.edit', compact('booking', 'facilities', 'rooms', 'bookingFacilities'));
    }

    /**
     * Update booking.
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

            // Sync facilities
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

            $booking->load(['user', 'room']);

            // Notify if admin changed room or time
            if ($isAdminEdit) {
                if ($oldRoomId != $request->room_id) {
                    $this->notificationService->notifyRoomChange($booking, $oldRoomName, $booking->room->name);
                }
                if ($oldStartTime != $request->start_time || $oldEndTime != $request->end_time) {
                    $this->notificationService->notifyTimeChange($booking, $oldStartTime, $oldEndTime);
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking berhasil diperbarui.',
                    'redirect' => route('bookings.show', $booking->id)
                ]);
            }

            return redirect()->route('bookings.show', $booking->id)
                ->with('success', 'Booking berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking update error: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memperbarui booking.'
                ], 500);
            }

            return back()->with('error', 'Terjadi kesalahan saat memperbarui booking.')->withInput();
        }
    }

    /**
     * Delete booking.
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
     * Check room availability (AJAX).
     */
    public function checkAvailability(Request $request)
    {
        try {
            $request->validate([
                'room_id' => 'required|integer|exists:rooms,id',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',
            ]);

            $roomId = $request->room_id;
            $startTime = Carbon::parse($request->start_time);
            $endTime = Carbon::parse($request->end_time);

            // Weekend check
            if ($startTime->isWeekend()) {
                return response()->json([
                    'available' => false,
                    'message' => 'Peminjaman tidak dapat dilakukan pada hari Sabtu atau Minggu.'
                ]);
            }

            // Past date check
            if ($startTime->isPast() && !$startTime->isToday()) {
                return response()->json([
                    'available' => false,
                    'message' => 'Tidak dapat melakukan peminjaman pada tanggal yang sudah lewat.'
                ]);
            }

            // Working hours check (07:00 - 16:00)
            $workStart = $startTime->copy()->setTime(7, 0, 0);
            $workEnd = $startTime->copy()->setTime(16, 0, 0);
            if ($startTime->lt($workStart) || $endTime->gt($workEnd)) {
                return response()->json([
                    'available' => false,
                    'message' => 'Booking hanya dapat dilakukan pada jam kerja 07:00 – 16:00 WIB.'
                ]);
            }

            // Overlap check
            $excludeBookingId = $request->exclude_booking_id;
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
                        'user' => $b->user->full_name ?? 'Unknown',
                        'purpose' => $b->purpose,
                        'status' => $b->status == 0 ? 'Pending' : 'Disetujui',
                    ])
                ]);
            }

            return response()->json([
                'available' => true,
                'message' => 'Ruangan tersedia untuk waktu tersebut.'
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
     * Approve booking (admin only).
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

            $booking->load(['user', 'room']);
            $this->notificationService->notifyBookingStatusChange($booking, $oldStatus, '1');

            return back()->with('success', 'Booking berhasil disetujui dan kode absensi telah dibuat.');

        } catch (\Exception $e) {
            Log::error('Booking approve error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyetujui booking.');
        }
    }

    /**
     * Reject booking (admin only).
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

            $booking->load(['user', 'room']);
            $this->notificationService->notifyBookingStatusChange($booking, $oldStatus, '2');

            return back()->with('success', $actionMessage);

        } catch (\Exception $e) {
            Log::error('Booking reject error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menolak booking.');
        }
    }

    /**
     * Bulk action (admin only).
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
                $updateData = [
                    'status' => $status,
                    'processed_by' => Auth::id(),
                    'processed_at' => now(),
                ];

                if ($request->action === 'approve') {
                    $updateData['absent_code'] = 'MTG-' . strtoupper(uniqid());
                }

                $oldStatus = (string) $booking->status;
                $booking->update($updateData);
                $booking->load(['user', 'room']);
                $this->notificationService->notifyBookingStatusChange($booking, $oldStatus, (string) $status);
            }

            return response()->json([
                'success' => true,
                'message' => count($bookings) . " booking berhasil {$message}."
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk action error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses booking.'
            ], 500);
        }
    }

    /**
     * Export bookings (CSV).
     */
    public function export(Request $request)
    {
        if (Auth::user()->role != 1) {
            abort(403, 'Unauthorized action.');
        }

        $query = Booking::with(['user', 'room']);

        if ($request->filled('status')) {
            $statusMap = ['pending' => 0, 'approved' => 1, 'rejected' => 2];
            if (isset($statusMap[$request->status])) {
                $query->where('status', $statusMap[$request->status]);
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('start_time', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('start_time', '<=', $request->date_to);
        }

        $bookings = $query->orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="bookings_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($bookings) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Peminjam', 'NIP', 'Ruangan', 'Tanggal', 'Jam Mulai', 'Jam Selesai', 'Keperluan', 'Status', 'Tipe', 'Dibuat Pada']);

            foreach ($bookings as $b) {
                $status = $b->status == 0 ? 'Pending' : ($b->status == 1 ? 'Disetujui' : 'Ditolak');
                fputcsv($file, [
                    $b->id,
                    $b->user->full_name ?? 'Unknown',
                    $b->user->nip ?? '-',
                    $b->room->name,
                    Carbon::parse($b->start_time)->format('d/m/Y'),
                    Carbon::parse($b->start_time)->format('H:i'),
                    Carbon::parse($b->end_time)->format('H:i'),
                    $b->purpose,
                    $status,
                    $b->booking_type,
                    $b->created_at->format('d/m/Y H:i'),
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Build timeline for booking detail.
     */
    private function buildTimeline($booking)
    {
        $timeline = [];

        $timeline[] = [
            'icon' => 'bi-plus-circle',
            'color' => 'primary',
            'title' => 'Booking Dibuat',
            'description' => 'Pengajuan peminjaman dibuat oleh ' . ($booking->user->full_name ?? 'Unknown'),
            'time' => $booking->created_at,
        ];

        if ($booking->processed_at) {
            $action = $booking->status == 1 ? 'Disetujui' : 'Ditolak';
            $timeline[] = [
                'icon' => $booking->status == 1 ? 'bi-check-circle' : 'bi-x-circle',
                'color' => $booking->status == 1 ? 'success' : 'danger',
                'title' => 'Booking ' . $action,
                'description' => $booking->rejection_reason ? 'Alasan: ' . $booking->rejection_reason : 'Booking telah diproses oleh admin',
                'time' => $booking->processed_at,
            ];
        }

        if ($booking->status == 1) {
            $timeline[] = [
                'icon' => 'bi-qr-code',
                'color' => 'info',
                'title' => 'Kode Absensi Dibuat',
                'description' => 'Kode: ' . $booking->absent_code,
                'time' => $booking->processed_at,
            ];

            if ($booking->attendances->count() > 0) {
                $timeline[] = [
                    'icon' => 'bi-people',
                    'color' => 'success',
                    'title' => 'Absensi Dimulai',
                    'description' => $booking->attendances->count() . ' peserta telah melakukan absensi',
                    'time' => $booking->attendances->first()->check_in_time ?? null,
                ];
            }
        }

        return $timeline;
    }
}