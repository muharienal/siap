<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Booking;
use App\Models\Facility;
use App\Models\BookingFacility;
use App\Models\Attendance;
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
        $hasDateFilter = $request->filled('date') || $request->filled('date_from') || $request->filled('date_to');
        if ($request->filled('date')) {
            $query->whereDate('start_time', $request->date);
        } elseif (!$hasDateFilter) {
            // UX: tanpa filter tanggal eksplisit, jangan diam-diam batasi ke
            // hari ini saja — tampilkan hari ini & semua yang akan datang,
            // supaya admin/user tidak "kehilangan" booking 2-3 hari ke depan
            // hanya karena defaultnya kebetulan menyempit ke satu hari.
            $query->whereDate('start_time', '>=', Carbon::today());
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
        // Default diubah ke "date_asc" (waktu mulai terdekat lebih dulu) karena
        // dropdown sort tidak diekspos di UI — ini yang selalu dilihat user,
        // dan lebih masuk akal untuk daftar jadwal dibanding urutan dibuat.
        $sort = $request->get('sort', 'date_asc');
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

        return view('bookings.index', compact('bookings', 'rooms', 'stats', 'hasDateFilter'));
    }

    /**
     * Show create form with pre-filled data.
     */
    public function create(Request $request)
    {
        $facilities = Facility::orderBy('name')->get();
        $rooms = Room::with('photos')->where('status', 1)->orderBy('name')->get();

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

            // Tentukan booking_type berdasarkan role:
            // - Karyawan (2): selalu Regular, apa pun yang dikirim di form.
            // - Sesdir (3): selalu Priority.
            // - Admin (1): mengikuti pilihan eksplisit di form (default Priority
            //   jika tidak dikirim / tidak valid), sesuai dropdown yang memang
            //   sudah ditampilkan untuknya di form Tambah Peminjaman.
            $userRole = Auth::user()->role;
            if ($userRole == 2) {
                $bookingType = 'Regular';
            } elseif ($userRole == 3) {
                $bookingType = 'Priority';
            } else {
                $bookingType = in_array($request->booking_type, ['Regular', 'Priority'])
                    ? $request->booking_type
                    : 'Priority';
            }

            $booking = Booking::create([
                'user_id' => Auth::id(),
                'room_id' => $request->room_id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'purpose' => $request->purpose,
                'consumption_qty' => $request->boolean('needs_consumption') ? $request->consumption_qty : null,
                'other_notes' => $request->boolean('needs_other') ? $request->other_notes : null,
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

            $booking->load(['user', 'room']);
            $this->notificationService->notifyNewBookingToAdmins($booking);

            // Sistem konflik prioritas: kalau booking ini Priority dan bentrok
            // dengan booking Reguler yang sudah ada (pending/approved), tandai
            // booking Reguler tersebut sebagai "bentrok" dan beri tahu
            // karyawan pemiliknya + seluruh admin supaya segera dialihkan.
            if ($bookingType === 'Priority') {
                $this->flagPriorityConflicts($booking);
            }

            DB::commit();

            // FIX: Tambahkan header Accept: application/json agar frontend fetch() selalu dapat JSON
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking berhasil diajukan dan menunggu persetujuan.',
                    'redirect' => route('bookings.show', $booking->id)
                ], 200, ['Content-Type' => 'application/json']);
            }

            return redirect()->route('bookings.show', $booking->id)
                ->with('success', 'Booking berhasil diajukan dan menunggu persetujuan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking store error: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan booking: ' . $e->getMessage()
                ], 500, ['Content-Type' => 'application/json']);
            }


            return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan booking.'])->withInput();
        }
    }

    /**
     * Tandai booking Reguler yang bentrok dengan booking Priority yang baru
     * dibuat, lalu beri tahu karyawan pemiliknya dan seluruh admin.
     *
     * Dipanggil setelah booking Priority berhasil disimpan — bukan berarti
     * booking Reguler yang bentrok otomatis dibatalkan, hanya ditandai
     * "perlu ditinjau admin" supaya bisa segera dialihkan ke ruangan/jam lain.
     */
    private function flagPriorityConflicts(Booking $priorityBooking)
    {
        $conflicts = Booking::where('room_id', $priorityBooking->room_id)
            ->where('id', '!=', $priorityBooking->id)
            ->where('booking_type', 'Regular')
            ->whereIn('status', [0, 1]) // pending & approved sama-sama bisa bentrok
            // FIX: whereBetween di kedua ujung bersifat inklusif, jadi
            // booking yang cuma bersambungan tepat di batas waktu (bukan
            // benar-benar tumpang tindih) ikut kena tandai bentrok. Diganti
            // rumus overlap standar (lihat BookingRequest::validateTimeAvailability
            // untuk penjelasan lengkap).
            ->where('start_time', '<', $priorityBooking->end_time)
            ->where('end_time', '>', $priorityBooking->start_time)
            ->get();

        foreach ($conflicts as $conflict) {
            $conflict->update(['displaced_by_id' => $priorityBooking->id]);
            $conflict->load(['user', 'room']);
            $this->notificationService->notifyPriorityConflict($conflict, $priorityBooking);
            $this->notificationService->notifyPriorityConflictToAdmins($conflict, $priorityBooking);
        }
    }

    /**
     * Display booking detail with timeline.
     */
    public function show(Booking $booking)
    {
        // FIX: sebelumnya method ini tidak punya pengecekan otorisasi sama
        // sekali — user mana pun yang login bisa buka detail booking siapa
        // saja cukup lewat URL /bookings/{id}, termasuk lihat kode absensi
        // & QR-nya. Disamakan dengan pola yang sudah benar di edit()/destroy().
        if (Auth::user()->role != 1 && $booking->user_id != Auth::id()) {
            abort(403, 'Anda hanya bisa melihat detail peminjaman milik Anda sendiri.');
        }

        // FIX: Hapus 'complaints' dari array load karena relasi tersebut tidak ada / dihapus
        $booking->load([
            'room.photos',
            'facilities',
            'user',
            'bookingFacilities.facility',
            'attendances.user',
            'processedBy'
        ]);

        $timeline = $this->buildTimeline($booking);

        // Untuk modal "Alihkan ke Ruangan Lain" (hanya relevan kalau booking
        // ini sedang bentrok dan yang membuka admin, tapi dimuat sekalian
        // supaya modal siap pakai tanpa request tambahan).
        $rooms = Room::where('status', 1)->orderBy('name')->get();

        return view('bookings.show', compact('booking', 'timeline', 'rooms'));
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

        // FIX: checklist fasilitas fisik (Microphone, Videotron, dll) dihapus
        // dari form edit supaya konsisten dengan form Tambah Peminjaman yang
        // memang tidak punya fitur ini — jadi $facilities/$bookingFacilities
        // tidak perlu di-query lagi di sini.
        $rooms = Room::with('photos')->orderBy('name')->get();

        return view('bookings.edit', compact('booking', 'rooms'));
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
                'consumption_qty' => $request->boolean('needs_consumption') ? $request->consumption_qty : null,
                'other_notes' => $request->boolean('needs_other') ? $request->other_notes : null,
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

            // FIX: Tambahkan header Accept: application/json
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking berhasil diperbarui.',
                    'redirect' => route('bookings.show', $booking->id)
                ], 200, ['Content-Type' => 'application/json']);
            }

            return redirect()->route('bookings.show', $booking->id)
                ->with('success', 'Booking berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking update error: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memperbarui booking.'
                ], 500, ['Content-Type' => 'application/json']);
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

        // FIX: sebelumnya user biasa bisa menghapus booking miliknya sendiri
        // kapan pun, termasuk yang sudah disetujui admin. Sekarang disamakan
        // dengan aturan edit(): booking yang sudah diproses (disetujui/
        // ditolak) cuma boleh dihapus admin.
        if ($booking->status != 0 && Auth::user()->role != 1) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking yang sudah disetujui/ditolak tidak dapat dihapus. Hubungi admin.'
                ], 403);
            }
            return redirect()->route('bookings.index')
                ->with('error', 'Booking yang sudah disetujui/ditolak tidak dapat dihapus. Hubungi admin.');
        }

        try {
            // Kalau booking ini menyebabkan booking lain "bentrok", beri tahu
            // pemiliknya dulu sebelum dihapus (kolom displaced_by_id akan
            // otomatis ter-null-kan oleh database lewat foreign key).
            $this->clearDisplacedConflicts($booking);

            BookingFacility::where('booking_id', $booking->id)->delete();
            $booking->delete();

            // FIX: Tambahkan response JSON untuk AJAX
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking berhasil dihapus.'
                ], 200, ['Content-Type' => 'application/json']);
            }

            return redirect()->route('bookings.index')
                ->with('success', 'Booking berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Booking delete error: ' . $e->getMessage());
            
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menghapus booking.'
                ], 500, ['Content-Type' => 'application/json']);
            }

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
            // FIX: rumus lama pakai trik tambah/kurang 1 detik untuk hindari
            // false-positive di batas waktu — rapuh dan gampang meleset.
            // Diganti rumus overlap standar: dua rentang waktu tumpang
            // tindih HANYA JIKA (mulai_lama < selesai_baru) DAN
            // (selesai_lama > mulai_baru). Disamakan persis dengan
            // BookingRequest::validateTimeAvailability() supaya cek
            // ketersediaan live dan validasi submit selalu sepakat.
            $excludeBookingId = $request->exclude_booking_id;
            $query = Booking::where('room_id', $roomId)
                ->whereIn('status', [0, 1])
                ->where('start_time', '<', $endTime)
                ->where('end_time', '>', $startTime);

            if ($excludeBookingId) {
                $query->where('id', '!=', $excludeBookingId);
            }

            $conflicts = $query->get();

            if ($conflicts->count() > 0) {
                // Sistem prioritas: booking Priority (Sesdir/Admin) boleh
                // menembus bentrok dengan booking Reguler yang sudah ada.
                // PENTING: mekanisme ini disembunyikan total dari selain
                // admin — karyawan/Sesdir tidak boleh tahu ada perbedaan
                // perlakuan Priority/Reguler, supaya tidak menimbulkan
                // kecemburuan. Non-admin hanya melihat "tersedia" polos.
                $bookingType = in_array($request->booking_type, ['Regular', 'Priority'])
                    ? $request->booking_type
                    : 'Regular';

                $blockingConflicts = $bookingType === 'Priority'
                    ? $conflicts->where('booking_type', 'Priority')->values()
                    : $conflicts;

                if ($blockingConflicts->count() > 0) {
                    return response()->json([
                        'available' => false,
                        'message' => 'Ruangan sudah dipesan pada waktu tersebut.',
                        'conflicts' => $blockingConflicts->map(fn($b) => [
                            'id' => $b->id,
                            'start' => $b->start_time,
                            'end' => $b->end_time,
                            'user' => $b->user->full_name ?? 'Unknown',
                            'purpose' => $b->purpose,
                            'status' => $b->status == 0 ? 'Pending' : 'Disetujui',
                        ])
                    ]);
                }

                // Priority menembus konflik Reguler. Admin boleh tahu detailnya;
                // selain admin hanya melihat "tersedia" tanpa keterangan apa pun.
                if (Auth::user()->role == 1) {
                    return response()->json([
                        'available' => true,
                        'priority_override' => true,
                        'message' => 'Ruangan sedang dipakai peminjam reguler. Karena ini peminjaman Priority, Anda tetap bisa melanjutkan — booking reguler yang bentrok akan dialihkan.',
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
     * Alihkan booking yang bentrok (displaced_by_id terisi) ke ruangan
     * dan/atau waktu lain. Admin wajib mengisi catatan alasan yang akan
     * dikirim ke peminjam. Setelah berhasil, flag konflik dilepas.
     */
    public function relocate(Request $request, Booking $booking)
    {
        if (Auth::user()->role != 1) {
            abort(403, 'Unauthorized action.');
        }

        if (!$booking->displaced_by_id) {
            return back()->with('error', 'Booking ini tidak sedang dalam status konflik.');
        }

        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'relocation_note' => 'required|string|min:10|max:500',
        ], [
            'room_id.required' => 'Ruangan pengganti harus dipilih.',
            'start_time.required' => 'Waktu mulai harus diisi.',
            'end_time.after' => 'Waktu selesai harus setelah waktu mulai.',
            'relocation_note.required' => 'Catatan untuk peminjam harus diisi.',
            'relocation_note.min' => 'Catatan minimal 10 karakter.',
        ]);

        try {
            DB::beginTransaction();

            // Pastikan slot pengganti benar-benar kosong (kecuali booking ini sendiri)
            // FIX: sama seperti dua tempat lain — whereBetween inklusif di
            // kedua ujung bikin slot yang cuma bersambungan tepat di batas
            // waktu ikut dianggap bentrok. Diganti rumus overlap standar.
            $conflict = Booking::where('room_id', $request->room_id)
                ->where('id', '!=', $booking->id)
                ->whereIn('status', [0, 1])
                ->where('start_time', '<', $request->end_time)
                ->where('end_time', '>', $request->start_time)
                ->exists();

            if ($conflict) {
                DB::rollBack();
                return back()->with('error', 'Ruangan/waktu pengganti yang dipilih juga sudah terpakai. Silakan pilih yang lain.')->withInput();
            }

            $oldRoomName = $booking->room->name;
            $oldStartTime = $booking->start_time;

            $booking->update([
                'room_id' => $request->room_id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'relocation_note' => $request->relocation_note,
                'displaced_by_id' => null,
            ]);

            $booking->load(['user', 'room']);
            $this->notificationService->notifyRelocated($booking, $oldRoomName, $oldStartTime, $request->relocation_note);

            DB::commit();

            return redirect()->route('bookings.show', $booking->id)
                ->with('success', 'Booking berhasil dialihkan dan peminjam telah diberi tahu.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking relocate error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengalihkan booking.')->withInput();
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

            // Kalau booking ini sebelumnya menyebabkan booking lain "bentrok"
            // (booking Priority yang ditolak/dibatalkan), lepaskan flag itu
            // dan beri tahu pemiliknya bahwa jadwal mereka aman kembali.
            $this->clearDisplacedConflicts($booking);

            return back()->with('success', $actionMessage);

        } catch (\Exception $e) {
            Log::error('Booking reject error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menolak booking.');
        }
    }

    /**
     * Lepaskan flag "bentrok" pada booking-booking yang tergeser oleh
     * booking Priority ini, lalu beri tahu pemiliknya jadwal mereka aman
     * kembali. Dipanggil saat booking Priority yang bersangkutan
     * ditolak/dibatalkan admin (konflik jadi tidak relevan lagi).
     */
    private function clearDisplacedConflicts(Booking $priorityBooking)
    {
        $displaced = Booking::where('displaced_by_id', $priorityBooking->id)->get();

        foreach ($displaced as $booking) {
            $booking->update(['displaced_by_id' => null]);
            $booking->load(['user', 'room']);
            $this->notificationService->notifyConflictResolvedAutomatically($booking);
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

    /**
     * ============================================================
     * ABSENSI TAMU VIA QR CODE
     * ============================================================
     * Melengkapi fitur yang sudah setengah dibangun (kolom absent_code,
     * qr_code_path, tabel attendances, dan generate QR di halaman detail
     * sudah ada) tapi belum punya handler — sebelumnya scan QR selalu
     * berujung 500 error karena method-method ini belum dibuat.
     */

    /**
     * Cek validitas kode absensi & jendela waktu check-in. Dipakai bareng
     * oleh attendMeeting() (GET, tampilkan form) dan submitAttendance()
     * (POST, proses absen) supaya aturannya konsisten di satu tempat.
     */
    private function resolveAttendanceBooking(string $code): array
    {
        $booking = Booking::with('room')->where('absent_code', $code)->first();

        if (!$booking) {
            return [
                'ok' => false,
                'message' => 'Kode absensi tidak ditemukan. Pastikan Anda memindai QR code yang benar.',
            ];
        }

        if ($booking->status == 2) {
            return [
                'ok' => false,
                'message' => 'Peminjaman ruangan ini telah ditolak, absensi tidak tersedia.',
                'booking' => $booking,
            ];
        }

        if ($booking->status == 0) {
            return [
                'ok' => false,
                'message' => 'Peminjaman ruangan ini belum disetujui admin, absensi belum dapat dilakukan.',
                'booking' => $booking,
            ];
        }

        // Jendela absensi: 15 menit sebelum mulai s/d 15 menit setelah selesai,
        // supaya tamu yang datang sedikit lebih awal/telat tetap bisa absen.
        $now = Carbon::now();
        $opens = Carbon::parse($booking->start_time)->subMinutes(15);
        $closes = Carbon::parse($booking->end_time)->addMinutes(15);

        if ($now->lt($opens)) {
            return [
                'ok' => false,
                'message' => 'Sesi absensi belum dibuka. Absensi tersedia mulai pukul ' . $opens->format('H:i') . ' WIB.',
                'booking' => $booking,
            ];
        }

        if ($now->gt($closes)) {
            return [
                'ok' => false,
                'message' => 'Sesi absensi sudah ditutup. Rapat ini berakhir pada pukul ' . Carbon::parse($booking->end_time)->format('H:i') . ' WIB.',
                'booking' => $booking,
            ];
        }

        return ['ok' => true, 'booking' => $booking];
    }

    /**
     * Halaman form absensi yang dibuka saat tamu scan QR code (publik, tanpa login).
     */
    public function attendMeeting(string $code)
    {
        $result = $this->resolveAttendanceBooking($code);

        if (!$result['ok']) {
            return view('attendance.error', [
                'message' => $result['message'],
                'booking' => $result['booking'] ?? null,
            ]);
        }

        return view('attendance.form', ['booking' => $result['booking']]);
    }

    /**
     * Proses submit form absensi.
     */
    public function submitAttendance(Request $request, string $code)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'institution' => 'nullable|string|max:100',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
        ]);

        $result = $this->resolveAttendanceBooking($code);

        if (!$result['ok']) {
            return view('attendance.error', [
                'message' => $result['message'],
                'booking' => $result['booking'] ?? null,
            ]);
        }

        $booking = $result['booking'];

        // Hindari duplikat kalau orang yang sama tidak sengaja submit dua kali
        // (misal double-tap tombol atau refresh halaman).
        $alreadyCheckedIn = Attendance::where('booking_id', $booking->id)
            ->whereRaw('LOWER(TRIM(guest_name)) = ?', [strtolower(trim($request->name))])
            ->exists();

        if (!$alreadyCheckedIn) {
            Attendance::create([
                'booking_id' => $booking->id,
                'user_id' => Auth::id(), // terisi otomatis kalau yang scan sedang login
                'guest_name' => trim($request->name),
                'guest_institution' => $request->institution,
                'check_in_time' => now(),
                'status' => 'Hadir',
            ]);
        }

        return redirect()->route('attendance.success', $booking->absent_code);
    }

    /**
     * Halaman sukses setelah absensi berhasil.
     */
    public function attendanceSuccess(string $code)
    {
        $booking = Booking::with('room')->where('absent_code', $code)->first();

        return view('attendance.success', [
            'booking' => $booking,
            'message' => 'Terima kasih! Kehadiran Anda telah tercatat.',
        ]);
    }
}