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
use Illuminate\Support\Str;
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
        $rooms = Room::orderBy('name')->get();

        // Stats for cards — tetap ringkasan GLOBAL (semua status/tanggal),
        // tidak terikat filter salah satu dari 3 tabel di bawah.
        $statsQuery = $user->role == 1 ? Booking::query() : Booking::where('user_id', $user->id);
        $stats = [
            'total' => $statsQuery->count(),
            'pending' => (clone $statsQuery)->where('status', 0)->count(),
            'approved' => (clone $statsQuery)->where('status', 1)->count(),
            'rejected' => (clone $statsQuery)->where('status', 2)->count(),
            'today' => (clone $statsQuery)->whereDate('start_time', Carbon::today())->count(),
        ];

        // FIX (struktur halaman): sebelumnya SATU tabel gabungan (semua
        // status jadi satu, difilter lewat dropdown Status) + tabel
        // "Riwayat Peminjaman" terpisah di bawahnya untuk data lampau.
        // Sekarang diganti jadi TIGA tabel independen — Pending, Disetujui,
        // Ditolak — masing-masing dengan filter (Tanggal/Jam Kerja/Ruangan)
        // dan paginasi SENDIRI (nama parameter berawalan pending_/approved_/
        // rejected_ supaya tidak saling bentrok di URL yang sama). Tabel
        // Riwayat dihapus total — data lampau sekarang otomatis kelihatan
        // di tabel status masing-masing kalau memang difilter tanggalnya
        // (bukan lagi disembunyikan ke bagian terpisah).
        $pending = $this->filteredBookingsByStatus($request, 0, 'pending');
        $approved = $this->filteredBookingsByStatus($request, 1, 'approved');
        $rejected = $this->filteredBookingsByStatus($request, 2, 'rejected');

        // Live-refresh (polling dari JS di halaman) — reuse SEMUA query/
        // filter/stats yang sama persis di atas, cuma balikin fragmen HTML
        // tiap tabel saja supaya bisa update otomatis tanpa reload, tanpa
        // duplikasi logic query di method terpisah.
        //
        // FIX (konsistensi & pencegahan): dipersempit ke wantsJson() saja
        // (cek header Accept), BUKAN "ajax() || wantsJson()" seperti
        // sebelumnya — ajax() cuma cek header X-Requested-With yang bisa
        // saja suatu saat dikirim juga oleh mekanisme AJAX lain di halaman
        // ini yang sebenarnya mengharapkan HTML utuh (persis bug yang
        // ditemukan & diperbaiki di DashboardController: request tanggal/
        // filter salah dibalas JSON, kontennya jadi tidak pernah tertukar).
        // Saat ini belum ada mekanisme lain seperti itu di halaman ini,
        // tapi kondisi ini disamakan sekarang supaya tidak jadi jebakan
        // yang sama kalau nanti ditambah fitur AJAX baru.
        if ($request->wantsJson()) {
            return response()->json([
                'stats' => view('bookings.partials._stats', compact('stats'))->render(),
                'pending_results' => view('bookings.partials._main-list', [
                    'bookings' => $pending['bookings'], 'hasDateFilter' => $pending['hasDateFilter'],
                    'prefix' => 'pending', 'showBulk' => $user->role == 1,
                ])->render(),
                'approved_results' => view('bookings.partials._main-list', [
                    'bookings' => $approved['bookings'], 'hasDateFilter' => $approved['hasDateFilter'],
                    'prefix' => 'approved', 'showBulk' => false,
                ])->render(),
                'rejected_results' => view('bookings.partials._main-list', [
                    'bookings' => $rejected['bookings'], 'hasDateFilter' => $rejected['hasDateFilter'],
                    'prefix' => 'rejected', 'showBulk' => false,
                ])->render(),
                'signature' => md5(
                    $pending['bookings']->pluck('id')->implode(',') . '|' .
                    $approved['bookings']->pluck('id')->implode(',') . '|' .
                    $rejected['bookings']->pluck('id')->implode(',') . '|' .
                    json_encode($stats)
                ),
            ]);
        }

        return view('bookings.index', [
            'rooms' => $rooms,
            'stats' => $stats,
            'pendingBookings' => $pending['bookings'],
            'pendingHasDateFilter' => $pending['hasDateFilter'],
            'approvedBookings' => $approved['bookings'],
            'approvedHasDateFilter' => $approved['hasDateFilter'],
            'rejectedBookings' => $rejected['bookings'],
            'rejectedHasDateFilter' => $rejected['hasDateFilter'],
        ]);
    }

    /**
     * Query booking untuk SATU status tertentu, dengan filter Tanggal/
     * Ruangan yang independen dari 2 tabel status lainnya di halaman yang
     * sama — nama parameter query string diberi awalan ($prefix) supaya
     * ketiga tabel tidak saling menimpa filter satu sama lain di URL yang
     * sama persis. Rumus filternya SENGAJA sama persis dengan yang dulu
     * dipakai tabel gabungan, cuma status-nya sekarang tetap (bukan
     * pilihan dropdown lagi).
     */
    private function filteredBookingsByStatus(Request $request, int $status, string $prefix)
    {
        $user = Auth::user();
        $query = Booking::with(['user', 'room', 'facilities', 'bookingFacilities.facility'])
            ->where('status', $status);

        if ($user->role != 1) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled("{$prefix}_room")) {
            $query->where('room_id', $request->get("{$prefix}_room"));
        }

        $dateParam = $request->get("{$prefix}_date");
        $dateFromParam = $request->get("{$prefix}_date_from");
        $dateToParam = $request->get("{$prefix}_date_to");

        if ($dateFromParam) {
            $query->whereDate('start_time', '>=', $dateFromParam);
        }
        if ($dateToParam) {
            $query->whereDate('start_time', '<=', $dateToParam);
        }

        $hasDateFilter = (bool) ($dateParam || $dateFromParam || $dateToParam);
        if ($dateParam) {
            $query->whereDate('start_time', $dateParam);
        } elseif (!$hasDateFilter) {
            // UX: tanpa filter tanggal eksplisit, jangan diam-diam batasi ke
            // hari ini saja — tampilkan hari ini & semua yang akan datang.
            $query->whereDate('start_time', '>=', Carbon::today());
        }

        $query->orderBy('start_time', 'asc');

        return [
            'bookings' => $query->paginate(10, ['*'], "{$prefix}_page")->appends($request->query()),
            'hasDateFilter' => $hasDateFilter,
        ];
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
        // saja cukup lewat URL /bookings/{id}, termasuk lihat kode presensi
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
            'processedBy',
            'relocatedFromRoom'
        ]);

        $timeline = $this->buildTimeline($booking);

        // FIX: variabel status ini SEBELUMNYA cuma dihitung lewat @php di
        // dalam partial _top-status.blade.php — begitu partial itu
        // di-@include, variabel yang dihitung di dalamnya TIDAK ikut
        // "bocor" balik ke file utama show.blade.php (setiap file Blade
        // yang di-include punya scope variabel sendiri). Akibatnya bagian
        // lain di show.blade.php yang juga butuh variabel ini (modal
        // konfirmasi/tolak pengalihan) menabrak "Undefined variable" dan
        // membuat SELURUH halaman gagal render dengan error 500 — inilah
        // yang bikin terasa "lemot"/error di banyak alur CRUD, karena
        // hampir semua aksi booking pada akhirnya redirect ke halaman ini.
        // Sekarang dihitung SEKALI di sini (satu sumber kebenaran), dikirim
        // ke view utama maupun ke partial live() di bawah — bukan dihitung
        // ulang terpisah di masing-masing tempat yang berisiko berbeda hasil.
        $isConflicted = (bool) $booking->displaced_by_id && $booking->status != 2;
        $awaitingConfirmation = (bool) $booking->awaiting_user_confirmation;
        $isBookingOwner = $booking->user_id == Auth::id();

        return view('bookings.show', compact('booking', 'timeline', 'isConflicted', 'awaitingConfirmation', 'isBookingOwner'));
    }

    /**
     * Endpoint live-refresh untuk halaman detail booking — di-poll berkala
     * lewat JS supaya status/timeline/QR ter-update otomatis kalau ada
     * perubahan dari perangkat/akun lain, TANPA reload manual. Otorisasi
     * disamakan persis dengan show().
     */
    public function live(Booking $booking)
    {
        if (Auth::user()->role != 1 && $booking->user_id != Auth::id()) {
            abort(403, 'Anda hanya bisa melihat detail peminjaman milik Anda sendiri.');
        }

        $booking->load([
            'room.photos',
            'facilities',
            'user',
            'bookingFacilities.facility',
            'attendances.user',
            'processedBy',
            'relocatedFromRoom'
        ]);

        $timeline = $this->buildTimeline($booking);
        $isConflicted = (bool) $booking->displaced_by_id && $booking->status != 2;
        $awaitingConfirmation = (bool) $booking->awaiting_user_confirmation;
        $isBookingOwner = $booking->user_id == Auth::id();

        return response()->json([
            'top' => view('bookings.partials._top-status', compact('booking', 'isConflicted', 'awaitingConfirmation', 'isBookingOwner'))->render(),
            'side' => view('bookings.partials._side-panel', compact('booking', 'timeline'))->render(),
            'has_qr' => (bool) ($booking->status == 1 && $booking->absent_code),
            'absent_code' => $booking->absent_code,
            'updated_at' => optional($booking->updated_at)->timestamp,
        ]);
    }

    /**
     * Show edit form.
     */
    public function edit(Booking $booking)
    {
        if (Auth::user()->role != 1 && $booking->user_id != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // FIX: sebelumnya admin (role==1) BOLEH mengubah booking yang sudah
        // diproses (disetujui/ditolak) — celah ini bikin riwayat booking
        // bisa "berubah-ubah" setelah diputuskan, padahal riwayat harusnya
        // permanen/tidak bisa diotak-atik siapa pun begitu sudah diproses.
        // Sekarang berlaku untuk SEMUA role tanpa kecuali, termasuk admin.
        if ($booking->status != 0) {
            return redirect()->route('bookings.index')
                ->with('error', 'Booking yang sudah disetujui/ditolak tidak dapat diubah — riwayat bersifat permanen.');
        }

        // Sedang menunggu peminjam konfirmasi usulan pengalihan — ruangan/
        // waktu tidak boleh diubah lewat form edit biasa selama ini,
        // supaya tidak membajak keputusan yang harus diambil peminjam
        // sendiri lewat Setuju/Tolak Pengalihan.
        if ($booking->awaiting_user_confirmation) {
            return redirect()->route('bookings.show', $booking->id)
                ->with('error', 'Booking ini sedang menunggu konfirmasi pengalihan dari peminjam — tidak bisa diedit sampai dijawab.');
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

        // FIX: sama seperti edit() — status lock sekarang berlaku untuk
        // semua role, termasuk admin.
        if ($booking->status != 0) {
            return redirect()->route('bookings.index')
                ->with('error', 'Booking yang sudah disetujui/ditolak tidak dapat diubah — riwayat bersifat permanen.');
        }

        if ($booking->awaiting_user_confirmation) {
            return redirect()->route('bookings.show', $booking->id)
                ->with('error', 'Booking ini sedang menunggu konfirmasi pengalihan dari peminjam — tidak bisa diedit sampai dijawab.');
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

        // FIX: sebelumnya admin (role==1) BOLEH menghapus booking yang sudah
        // diproses (disetujui/ditolak), padahal riwayat harusnya permanen —
        // tidak bisa dihapus siapa pun begitu sudah diputuskan. Sekarang
        // berlaku untuk SEMUA role tanpa kecuali, termasuk admin.
        if ($booking->status != 0) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking yang sudah disetujui/ditolak tidak dapat dihapus — riwayat bersifat permanen.'
                ], 403);
            }
            return redirect()->route('bookings.index')
                ->with('error', 'Booking yang sudah disetujui/ditolak tidak dapat dihapus — riwayat bersifat permanen.');
        }

        if ($booking->awaiting_user_confirmation) {
            $msg = 'Booking ini sedang menunggu konfirmasi pengalihan dari peminjam — tidak bisa dihapus sampai dijawab.';
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }
            return redirect()->route('bookings.show', $booking->id)->with('error', $msg);
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
    public function approve(Request $request, Booking $booking)
    {
        if (Auth::user()->role != 1) {
            abort(403, 'Unauthorized action.');
        }

        if ($booking->status != 0) {
            return back()->with('error', 'Booking sudah diproses sebelumnya.');
        }

        if ($booking->awaiting_user_confirmation) {
            return back()->with('error', 'Booking ini sedang menunggu konfirmasi pengalihan dari peminjam, bukan persetujuan admin.');
        }

        // Catatan persetujuan sifatnya opsional — admin boleh menyetujui
        // tanpa mengisi apa pun, beda dengan Tolak/Alihkan yang mewajibkan
        // alasan/catatan.
        $request->validate([
            'approval_note' => 'nullable|string|max:500',
        ], [
            'approval_note.max' => 'Catatan maksimal 500 karakter.',
        ]);

        try {
            $oldStatus = (string) $booking->status;
            $absentCode = 'MTG-' . strtoupper(uniqid());

            $booking->update([
                'status' => 1,
                'absent_code' => $absentCode,
                'approval_note' => $request->input('approval_note'),
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);

            $booking->load(['user', 'room']);
            $this->notificationService->notifyBookingStatusChange($booking, $oldStatus, '1');

            return back()->with('success', 'Booking berhasil disetujui dan kode presensi telah dibuat.');

        } catch (\Exception $e) {
            Log::error('Booking approve error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyetujui booking.');
        }
    }

    /**
     * Daftar ruangan yang benar-benar kosong pada rentang waktu tertentu.
     * Dipakai live oleh dropdown "Ruangan Pengganti" di modal Alihkan —
     * supaya admin tidak bisa memilih ruangan yang ternyata sudah terpakai.
     * Rumus overlap disamakan persis dengan checkAvailability()/relocate()
     * supaya hasilnya selalu sepakat satu sama lain.
     */
    public function availableRooms(Request $request)
    {
        if (Auth::user()->role != 1) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $busyRoomIds = Booking::whereIn('status', [0, 1])
            ->where('start_time', '<', $request->end_time)
            ->where('end_time', '>', $request->start_time)
            ->when($request->exclude_booking_id, function ($q) use ($request) {
                $q->where('id', '!=', $request->exclude_booking_id);
            })
            ->pluck('room_id');

        $rooms = Room::where('status', 1)
            ->whereNotIn('id', $busyRoomIds)
            ->orderBy('name')
            ->get(['id', 'name', 'location', 'capacity']);

        return response()->json(['rooms' => $rooms]);
    }

    /**
     * Alihkan booking yang bentrok (displaced_by_id terisi) ke ruangan
     * dan/atau waktu lain. Admin wajib mengisi catatan alasan yang akan
     * dikirim ke peminjam. Setelah berhasil, flag konflik dilepas.
     */
    /**
     * Admin MENGUSULKAN pengalihan booking yang bentrok (displaced_by_id
     * terisi) ke ruangan/waktu lain. Ini BUKAN keputusan final — status
     * booking dikembalikan ke 0 (Pending) dan awaiting_user_confirmation
     * diaktifkan, supaya peminjam sendiri yang memutuskan setuju/tolak
     * lewat confirmRelocation()/declineRelocation(). Kalau langsung
     * dianggap "disetujui" tanpa peminjam tahu, mereka bisa saja tidak
     * bisa hadir di ruangan/jam baru itu.
     */
    public function relocate(Request $request, Booking $booking)
    {
        if (Auth::user()->role != 1) {
            abort(403, 'Unauthorized action.');
        }

        if (!$booking->displaced_by_id) {
            return back()->with('error', 'Booking ini tidak sedang dalam status konflik.');
        }

        // Booking yang sudah ditolak/selesai tidak perlu (dan tidak boleh)
        // dialihkan lagi — displaced_by_id yang masih terisi di sini cuma
        // sisa data lama sebelum status finalnya diputuskan.
        if ($booking->status == 2) {
            return back()->with('error', 'Booking ini sudah ditolak, tidak perlu dialihkan lagi.');
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
            $oldRoomId = $booking->room_id;
            $oldStartTime = $booking->start_time;
            $oldEndTime = $booking->end_time;

            $booking->update([
                'room_id' => $request->room_id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'relocation_note' => $request->relocation_note,
                'displaced_by_id' => null,
                // Balik ke Pending — BUKAN langsung disetujui. Peminjam yang
                // harus konfirmasi ruangan/waktu penggantinya, bukan admin
                // yang memutuskan sepihak atas nama peminjam.
                'status' => 0,
                'awaiting_user_confirmation' => true,
                'relocated_from_room_id' => $oldRoomId,
                'relocated_from_start_time' => $oldStartTime,
                'relocated_from_end_time' => $oldEndTime,
                'relocated_at' => now(),
                // Kode presensi lama (kalau ada, dari sebelum bentrok) tidak
                // relevan lagi selama menunggu konfirmasi — dibuat ulang saat
                // peminjam benar-benar menyetujui di confirmRelocation().
                'absent_code' => null,
                // Bersihkan sisa keputusan SIKLUS SEBELUMNYA (approval_note/
                // rejection_reason/processed_by/processed_at) — kalau tidak,
                // timeline bisa menampilkan "Booking Ditolak" dari siklus lama
                // berdampingan dengan "Menunggu Konfirmasi" dari siklus baru,
                // padahal keduanya bukan cerita yang sama.
                'approval_note' => null,
                'rejection_reason' => null,
                'processed_by' => null,
                'processed_at' => null,
            ]);

            $booking->load(['user', 'room']);
            $this->notificationService->notifyRelocationProposed($booking, $oldRoomName, $oldStartTime, $oldEndTime, $request->relocation_note);

            DB::commit();

            return redirect()->route('bookings.show', $booking->id)
                ->with('success', 'Usulan pengalihan terkirim. Menunggu konfirmasi dari peminjam.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking relocate error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengalihkan booking.')->withInput();
        }
    }

    /**
     * Peminjam MENYETUJUI usulan pengalihan ruangan/waktu dari admin.
     * Hanya pemilik booking yang boleh — ini keputusan pribadi peminjam,
     * admin (siapa pun rolenya) tidak boleh mengonfirmasi atas nama orang
     * lain.
     */
    public function confirmRelocation(Booking $booking)
    {
        if ($booking->user_id != Auth::id()) {
            abort(403, 'Hanya pemilik booking yang bisa mengonfirmasi pengalihan ini.');
        }

        if (!$booking->awaiting_user_confirmation) {
            return back()->with('error', 'Booking ini tidak sedang menunggu konfirmasi pengalihan.');
        }

        try {
            $absentCode = $booking->absent_code ?? ('MTG-' . strtoupper(uniqid()));

            $booking->update([
                'status' => 1,
                'awaiting_user_confirmation' => false,
                'absent_code' => $absentCode,
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);

            $booking->load(['user', 'room']);
            $this->notificationService->notifyRelocationConfirmed($booking);

            return back()->with('success', 'Pengalihan disetujui. Kode presensi telah dibuat.');

        } catch (\Exception $e) {
            Log::error('Booking confirmRelocation error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyetujui pengalihan.');
        }
    }

    /**
     * Peminjam MENOLAK usulan pengalihan ruangan/waktu dari admin — booking
     * ini otomatis berstatus Ditolak (bukan "batal alih lalu balik ke
     * jadwal semula", karena jadwal semula sudah pasti terpakai booking
     * Priority yang menyebabkan konflik). Peminjam perlu mengajukan
     * peminjaman baru kalau masih membutuhkan ruangan.
     */
    public function declineRelocation(Booking $booking)
    {
        if ($booking->user_id != Auth::id()) {
            abort(403, 'Hanya pemilik booking yang bisa menolak pengalihan ini.');
        }

        if (!$booking->awaiting_user_confirmation) {
            return back()->with('error', 'Booking ini tidak sedang menunggu konfirmasi pengalihan.');
        }

        try {
            $booking->update([
                'status' => 2,
                'awaiting_user_confirmation' => false,
                'rejection_reason' => 'Peminjam menolak usulan pengalihan ruangan/waktu dari admin.',
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);

            $booking->load(['user', 'room']);
            $this->notificationService->notifyRelocationDeclined($booking);

            return back()->with('success', 'Pengalihan ditolak. Booking ini otomatis berstatus Ditolak — silakan ajukan peminjaman baru bila masih membutuhkan ruangan.');

        } catch (\Exception $e) {
            Log::error('Booking declineRelocation error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menolak pengalihan.');
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

        if ($booking->awaiting_user_confirmation) {
            return back()->with('error', 'Booking ini sedang menunggu konfirmasi pengalihan dari peminjam, bukan penolakan admin.');
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
                // FIX: kalau booking ini SENDIRI sedang bentrok (displaced_by_id
                // terisi, belum sempat dialihkan) lalu admin langsung menolaknya
                // lewat Tolak (bukan lewat Alihkan), displaced_by_id dulu tetap
                // nyangkut di database walau bookingnya sudah mati (Ditolak) —
                // efeknya badge "BENTROK" dan tombol "Alihkan" masih muncul di
                // booking yang sudah ditolak. Booking yang sudah ditolak tidak
                // relevan lagi untuk dialihkan, jadi bersihkan sekalian.
                'displaced_by_id' => null,
                'awaiting_user_confirmation' => false,
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

            // FIX (bug krusial): sebelumnya query ini cuma cek status==0
            // tanpa mengecualikan booking yang sedang MENUNGGU KONFIRMASI
            // PEMINJAM (hasil pengalihan admin — statusnya tetap 0/Pending
            // selama menunggu). Akibatnya admin bisa tidak sengaja
            // "Setujui Semua"/"Tolak Semua" booking yang keputusannya
            // seharusnya cuma boleh diambil peminjam sendiri lewat tombol
            // Setuju/Tolak Pengalihan — melangkahi alur konfirmasi yang
            // sudah dibangun khusus untuk itu. Booking seperti ini sekarang
            // dikecualikan dari bulk action sama sekali.
            $bookings = Booking::whereIn('id', $request->booking_ids)
                ->where('status', 0)
                ->where('awaiting_user_confirmation', false)
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

        // FIX (bug tersembunyi): sebelumnya method ini membaca parameter
        // filter LAMA (status/date_from/date_to) dari satu tabel gabungan
        // yang sudah tidak ada lagi sejak halaman ini diganti jadi 3 tabel
        // independen (Pending/Disetujui/Ditolak) — masing-masing sekarang
        // pakai parameter berawalan pending_/approved_/rejected_. Karena
        // parameter lama itu tidak pernah lagi dikirim dari halaman mana
        // pun, kondisi if($request->filled('status')) dkk selalu FALSE —
        // tombol "Export" diam-diam SELALU mengekspor SEMUA booking, tidak
        // peduli filter apa yang sedang aktif di layar (tanpa tanda apa
        // pun kalau itu terjadi — pengguna bisa mengira file yang diunduh
        // sudah sesuai filter, padahal tidak).
        //
        // Karena sekarang ada 3 filter independen sekaligus (bukan satu),
        // tidak ada cara jelas untuk memutuskan filter MANA yang harus
        // dipakai export satu file. Solusi paling jujur & sederhana:
        // Export selalu mengekspor SEMUA data (tanpa filter), TIDAK diam-
        // diam berpura-pura mengikuti filter yang sebenarnya tidak pernah
        // sampai ke sini. Kalau nanti dibutuhkan export per-tabel/filter,
        // itu perlu tombol Export terpisah di tiap tabel — perubahan
        // fitur baru, di luar cakupan perbaikan bug ini.
        $query = Booking::with(['user', 'room']);

        $bookings = $query->orderBy('created_at', 'desc')->get();

        $rows = $bookings->map(function ($b) {
            $status = $b->status == 0 ? 'Pending' : ($b->status == 1 ? 'Disetujui' : 'Ditolak');
            return [
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
            ];
        })->all();

        return $this->downloadStyledExcel(
            ['ID', 'Peminjam', 'NIP', 'Ruangan', 'Tanggal', 'Jam Mulai', 'Jam Selesai', 'Keperluan', 'Status', 'Tipe', 'Dibuat Pada'],
            $rows,
            'Data Peminjaman Ruangan',
            'bookings_' . date('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Export daftar hadir (presensi) satu booking ke Excel.
     * Format kolom: No. | Nama Peserta | Instansi / Divisi | Waktu Check-in | Status
     */
    public function exportAttendance(Booking $booking)
    {
        if (Auth::user()->role != 1 && $booking->user_id != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $attendees = $booking->attendances()->with('user')->orderBy('check_in_time', 'asc')->get();

        $rows = $attendees->map(function ($att, $i) {
            return [
                $i + 1,
                // FIX: nama yang diketik di form (guest_name, wajib isi) jadi
                // sumber utama — bukan nama akun yang kebetulan sedang login
                // saat mengisi presensi.
                $att->guest_name ?: ($att->user->full_name ?? '-'),
                $att->guest_institution ?: ($att->user ? 'Internal' : '-'),
                Carbon::parse($att->check_in_time)->format('d/m/Y H:i') . ' WIB',
                $att->status ?? 'Hadir',
            ];
        })->values()->all();

        return $this->downloadStyledExcel(
            ['No.', 'Nama Peserta', 'Instansi / Divisi', 'Waktu Check-in', 'Status'],
            $rows,
            'Daftar Hadir - ' . $booking->room->name . ' (' . Carbon::parse($booking->start_time)->format('d/m/Y') . ')',
            'daftar-hadir-' . Str::slug($booking->room->name) . '-' . Carbon::parse($booking->start_time)->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * FIX (export CSV dibaca berantakan di Excel): sebelumnya kedua fitur
     * export di atas menghasilkan file .csv mentah (cuma teks dipisah koma).
     * Excel menafsirkan delimiter CSV berdasarkan pengaturan region Windows
     * pengguna — di banyak setup lokal Indonesia, Excel mengharapkan titik
     * koma (;) sebagai pemisah, bukan koma (,) — hasilnya SEMUA data
     * "menempel" jadi satu kolom persis seperti yang terlihat. Solusinya
     * bukan ganti delimiter (masih rapuh, tergantung setting tiap PC),
     * tapi generate file .xlsx SUNGGUHAN pakai PhpSpreadsheet — kolom
     * sudah pasti terpisah dengan benar di komputer siapa pun, sekaligus
     * dapat header berwarna, border, dan lebar kolom otomatis supaya
     * langsung enak dibaca tanpa perlu dirapikan manual dulu.
     */
    private function downloadStyledExcel(array $headers, array $rows, string $title, string $filename)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));

        // Baris judul di atas tabel, digabung penuh selebar kolom.
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13)->setColor(
            new \PhpOffice\PhpSpreadsheet\Style\Color('FF16A34A')
        );
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->getRowDimension(1)->setRowHeight(24);

        // Baris header tabel (baris ke-3, kasih 1 baris kosong sebagai jarak).
        $headerRow = 3;
        foreach ($headers as $i => $label) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue("{$col}{$headerRow}", $label);
        }
        $headerRange = "A{$headerRow}:{$lastCol}{$headerRow}";
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFF2751A');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension($headerRow)->setRowHeight(22);

        // Baris data.
        $r = $headerRow + 1;
        foreach ($rows as $row) {
            foreach ($row as $i => $value) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
                $sheet->setCellValue("{$col}{$r}", $value);
            }
            $r++;
        }
        $lastRow = max($headerRow, $r - 1);

        // Border tipis di seluruh tabel (header + data) supaya terlihat
        // sebagai satu tabel yang jelas, bukan teks lepas.
        $tableRange = "A{$headerRow}:{$lastCol}{$lastRow}";
        $sheet->getStyle($tableRange)->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
            ->getColor()->setARGB('FFE2E8F0');

        // Baris data selang-seling warna supaya lebih mudah dipindai matanya.
        for ($row = $headerRow + 1; $row <= $lastRow; $row++) {
            if (($row - $headerRow) % 2 === 0) {
                $sheet->getStyle("A{$row}:{$lastCol}{$row}")->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF8FAFC');
            }
        }

        // Lebar kolom otomatis menyesuaikan isi.
        foreach (range(1, count($headers)) as $i) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Bekukan baris header supaya tetap terlihat saat scroll ke bawah.
        $sheet->freezePane('A' . ($headerRow + 1));

        $sheet->setTitle('Data');

        $tmpPath = tempnam(sys_get_temp_dir(), 'siap_export_') . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($tmpPath);

        return response()->download($tmpPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
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

        // Riwayat pengalihan — dicatat SEBELUM entri approve/reject supaya
        // urutannya kronologis kalau booking ini sempat dialihkan lalu
        // baru dikonfirmasi/ditolak peminjam setelahnya.
        if ($booking->relocated_at) {
            $fromRoomName = $booking->relocatedFromRoom->name ?? 'ruangan sebelumnya';
            $timeChanged = $booking->relocated_from_start_time && (
                !$booking->relocated_from_start_time->eq($booking->start_time)
                || !$booking->relocated_from_end_time->eq($booking->end_time)
            );

            $desc = "Dari {$fromRoomName}";
            if ($booking->relocated_from_start_time) {
                $desc .= ' (' . $booking->relocated_from_start_time->locale('id')->isoFormat('D MMM, HH:mm') .
                          '–' . $booking->relocated_from_end_time->format('H:i') . ')';
            }
            $desc .= " ke {$booking->room->name}";
            if ($booking->relocated_from_start_time) {
                $desc .= ' (' . $booking->start_time->locale('id')->isoFormat('D MMM, HH:mm') .
                          '–' . $booking->end_time->format('H:i') . ')';
                $desc .= $timeChanged ? '. Jam berubah.' : '. Jam tidak berubah.';
            }
            if ($booking->relocation_note) {
                $desc .= ' Catatan admin: "' . $booking->relocation_note . '"';
            }

            $timeline[] = [
                'icon' => 'bi-arrow-left-right',
                'color' => 'warning',
                'title' => $booking->awaiting_user_confirmation
                    ? 'Admin Mengusulkan Pengalihan — Menunggu Konfirmasi'
                    : 'Dialihkan oleh Admin',
                'description' => $desc,
                'time' => $booking->relocated_at,
            ];
        }

        if ($booking->processed_at) {
            $action = $booking->status == 1 ? 'Disetujui' : 'Ditolak';

            $description = 'Booking telah diproses oleh admin';
            if ($booking->status == 1 && $booking->approval_note) {
                $description = 'Catatan: ' . $booking->approval_note;
            } elseif ($booking->rejection_reason) {
                $description = 'Alasan: ' . $booking->rejection_reason;
            }

            // Kalau proses ini adalah tindak lanjut dari pengalihan (peminjam
            // sendiri yang konfirmasi/tolak, bukan admin approve/reject
            // biasa), judulnya dibedakan supaya jelas siapa yang memutuskan.
            $wasRelocationResponse = $booking->relocated_at
                && $booking->processed_at->gte($booking->relocated_at)
                && $booking->processed_by == $booking->user_id;

            if ($wasRelocationResponse) {
                $timeline[] = [
                    'icon' => $booking->status == 1 ? 'bi-check-circle' : 'bi-x-circle',
                    'color' => $booking->status == 1 ? 'success' : 'danger',
                    'title' => $booking->status == 1 ? 'Peminjam Menyetujui Pengalihan' : 'Peminjam Menolak Pengalihan',
                    'description' => $booking->status == 1
                        ? 'Ruangan & waktu pengganti dikonfirmasi oleh peminjam.'
                        : 'Peminjam menolak usulan pengalihan. Booking otomatis ditolak.',
                    'time' => $booking->processed_at,
                ];
            } else {
                $timeline[] = [
                    'icon' => $booking->status == 1 ? 'bi-check-circle' : 'bi-x-circle',
                    'color' => $booking->status == 1 ? 'success' : 'danger',
                    'title' => 'Booking ' . $action,
                    'description' => $description,
                    'time' => $booking->processed_at,
                ];
            }
        }

        if ($booking->status == 1) {
            $timeline[] = [
                'icon' => 'bi-qr-code',
                'color' => 'info',
                'title' => 'Kode Presensi Dibuat',
                'description' => 'Kode: ' . $booking->absent_code,
                'time' => $booking->processed_at,
            ];

            if ($booking->attendances->count() > 0) {
                $timeline[] = [
                    'icon' => 'bi-people',
                    'color' => 'success',
                    'title' => 'Presensi Dimulai',
                    'description' => $booking->attendances->count() . ' peserta telah melakukan presensi',
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
     * Cek validitas kode presensi & jendela waktu check-in. Dipakai bareng
     * oleh attendMeeting() (GET, tampilkan form) dan submitAttendance()
     * (POST, proses absen) supaya aturannya konsisten di satu tempat.
     */
    private function resolveAttendanceBooking(string $code): array
    {
        $booking = Booking::with('room')->where('absent_code', $code)->first();

        if (!$booking) {
            return [
                'ok' => false,
                'message' => 'Kode presensi tidak ditemukan. Pastikan Anda memindai QR code yang benar.',
            ];
        }

        if ($booking->status == 2) {
            return [
                'ok' => false,
                'message' => 'Peminjaman ruangan ini telah ditolak, presensi tidak tersedia.',
                'booking' => $booking,
            ];
        }

        if ($booking->status == 0) {
            return [
                'ok' => false,
                'message' => 'Peminjaman ruangan ini belum disetujui admin, presensi belum dapat dilakukan.',
                'booking' => $booking,
            ];
        }

        // Jendela presensi: 1 jam sebelum mulai s/d 2 jam setelah selesai,
        // supaya tamu yang datang lebih awal atau baru sempat mengisi
        // presensi agak lama setelah rapat masih tetap bisa tercatat.
        $now = Carbon::now();
        $opens = Carbon::parse($booking->start_time)->subHour();
        $closes = Carbon::parse($booking->end_time)->addHours(2);

        if ($now->lt($opens)) {
            return [
                'ok' => false,
                'message' => 'Sesi presensi belum dibuka. Presensi tersedia mulai pukul ' . $opens->format('H:i') . ' WIB.',
                'booking' => $booking,
            ];
        }

        if ($now->gt($closes)) {
            return [
                'ok' => false,
                'message' => 'Sesi presensi sudah ditutup. Rapat ini berakhir pada pukul ' . Carbon::parse($booking->end_time)->format('H:i') . ' WIB.',
                'booking' => $booking,
            ];
        }

        return ['ok' => true, 'booking' => $booking];
    }

    /**
     * Halaman form presensi yang dibuka saat tamu scan QR code (publik, tanpa login).
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

        // Daftar yang sudah absen ditampilkan di form, supaya orang yang
        // membuka halaman ini bisa langsung cek namanya sudah tercatat
        // atau belum, tanpa perlu submit ulang untuk mencari tahu.
        $attendees = Attendance::where('booking_id', $result['booking']->id)
            ->orderBy('check_in_time', 'asc')
            ->get();

        return view('attendance.form', ['booking' => $result['booking'], 'attendees' => $attendees]);
    }

    /**
     * Proses submit form presensi.
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

        // FIX: sebelumnya duplikat cuma dicek dari NAMA saja — jadi dua orang
        // yang kebetulan namanya sama (dari instansi berbeda) tidak bisa
        // absen keduanya. Sekarang dicek dari KOMBINASI nama + bidang/
        // instansi/perusahaan — kalau keduanya identik (bukan cuma salah
        // satu), baru dianggap orang yang sama dan ditolak. Selain itu,
        // sebelumnya kalau ketahuan duplikat, sistem DIAM-DIAM tidak
        // menyimpan tapi tetap mengarahkan ke halaman sukses seolah-olah
        // berhasil — sekarang ditolak eksplisit dengan pesan yang jelas,
        // sesuai permintaan: tidak boleh ada duplikat.
        $normalizedName = strtolower(trim($request->name));
        $normalizedInstitution = strtolower(trim($request->institution ?? ''));

        $alreadyCheckedIn = Attendance::where('booking_id', $booking->id)
            ->whereRaw('LOWER(TRIM(guest_name)) = ?', [$normalizedName])
            ->whereRaw('LOWER(TRIM(COALESCE(guest_institution, \'\'))) = ?', [$normalizedInstitution])
            ->exists();

        if ($alreadyCheckedIn) {
            return back()
                ->withInput()
                ->withErrors(['name' => 'Nama dan Bidang/Instansi/Perusahaan ini sudah tercatat hadir sebelumnya. Presensi tidak boleh ganda.']);
        }

        Attendance::create([
            'booking_id' => $booking->id,
            'user_id' => Auth::id(), // terisi otomatis kalau yang scan sedang login
            'guest_name' => trim($request->name),
            'guest_institution' => $request->institution,
            'check_in_time' => now(),
            'status' => 'Hadir',
        ]);

        return redirect()->route('attendance.success', $booking->absent_code);
    }

    /**
     * Halaman sukses setelah presensi berhasil.
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