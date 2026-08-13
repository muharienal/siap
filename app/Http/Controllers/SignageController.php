<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomDisplay;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SignageController extends Controller
{
    /**
     * Halaman admin: konfigurasi "Papan Semua Ruangan" (kalau cuma ada 1 TV)
     * + daftar ruangan untuk konfigurasi per-ruangan (kalau nanti nambah TV).
     * Admin bebas pakai salah satu, keduanya, atau tidak dua-duanya.
     */
    public function index()
    {
        if (Auth::user()->role != 1) {
            abort(403, 'Unauthorized action.');
        }

        $rooms = Room::with(['display', 'photos'])->orderBy('name')->get();
        $allBoard = RoomDisplay::allRoomsBoard();

        return view('settings.signage.index', compact('rooms', 'allBoard'));
    }

    /**
     * Simpan/perbarui konfigurasi tampilan untuk satu ruangan (upsert).
     * Baris room_displays baru otomatis dibuat (dengan token acak) kalau
     * ruangan ini belum pernah dikonfigurasi sebelumnya.
     */
    public function save(Request $request, Room $room)
    {
        if (Auth::user()->role != 1) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'orientation' => 'required|in:landscape,portrait',
            'show_qr' => 'sometimes|boolean',
            'show_upcoming' => 'sometimes|boolean',
            'upcoming_count' => 'required|integer|min:1|max:10',
            'refresh_seconds' => 'required|integer|min:15|max:600',
            'is_active' => 'sometimes|boolean',
        ]);

        $display = $room->display ?? new RoomDisplay(['room_id' => $room->id, 'type' => 'room']);

        $display->fill([
            'type' => 'room',
            'orientation' => $request->orientation,
            'show_qr' => $request->boolean('show_qr'),
            'show_upcoming' => $request->boolean('show_upcoming'),
            'upcoming_count' => $request->upcoming_count,
            'refresh_seconds' => $request->refresh_seconds,
            'is_active' => $request->boolean('is_active', true),
        ]);
        $display->save();

        return response()->json([
            'success' => true,
            'message' => 'Konfigurasi tampilan berhasil disimpan.',
            'data' => $this->formatDisplayData($display),
        ]);
    }

    /**
     * Generate ulang token link untuk display per-ruangan — dipakai kalau
     * link lama pernah bocor/ke-share sembarangan.
     */
    public function regenerateToken(Room $room)
    {
        if (Auth::user()->role != 1) {
            abort(403, 'Unauthorized action.');
        }

        $display = $room->display;
        if (!$display) {
            return response()->json(['success' => false, 'message' => 'Ruangan ini belum dikonfigurasi.'], 422);
        }

        $display->update(['display_token' => Str::random(48)]);

        return response()->json([
            'success' => true,
            'message' => 'Link tampilan berhasil diperbarui. Link lama sudah tidak berlaku.',
            'display_url' => route('signage.show', $display->display_token),
        ]);
    }

    /**
     * Simpan/perbarui konfigurasi "Papan Semua Ruangan" (singleton — cuma
     * ada 1 baris dengan type='all', room_id selalu NULL).
     */
    public function saveAll(Request $request)
    {
        if (Auth::user()->role != 1) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'orientation' => 'required|in:landscape,portrait',
            'show_qr' => 'sometimes|boolean',
            'refresh_seconds' => 'required|integer|min:15|max:600',
            'is_active' => 'sometimes|boolean',
        ]);

        $display = RoomDisplay::allRoomsBoard() ?? new RoomDisplay(['type' => 'all', 'room_id' => null]);

        $display->fill([
            'type' => 'all',
            'room_id' => null,
            'orientation' => $request->orientation,
            'show_qr' => $request->boolean('show_qr'),
            'show_upcoming' => true, // tidak relevan untuk tipe 'all', dibiarkan default
            'upcoming_count' => 3,
            'refresh_seconds' => $request->refresh_seconds,
            'is_active' => $request->boolean('is_active', true),
        ]);
        $display->save();

        return response()->json([
            'success' => true,
            'message' => 'Konfigurasi Papan Semua Ruangan berhasil disimpan.',
            'data' => $this->formatDisplayData($display),
        ]);
    }

    /**
     * Generate ulang token link untuk Papan Semua Ruangan.
     */
    public function regenerateAllToken()
    {
        if (Auth::user()->role != 1) {
            abort(403, 'Unauthorized action.');
        }

        $display = RoomDisplay::allRoomsBoard();
        if (!$display) {
            return response()->json(['success' => false, 'message' => 'Papan Semua Ruangan belum dikonfigurasi.'], 422);
        }

        $display->update(['display_token' => Str::random(48)]);

        return response()->json([
            'success' => true,
            'message' => 'Link Papan Semua Ruangan berhasil diperbarui. Link lama sudah tidak berlaku.',
            'display_url' => route('signage.show', $display->display_token),
        ]);
    }

    private function formatDisplayData(RoomDisplay $display): array
    {
        return [
            'room_id' => $display->room_id,
            'type' => $display->type,
            'display_url' => route('signage.show', $display->display_token),
            'orientation' => $display->orientation,
            'show_qr' => $display->show_qr,
            'show_upcoming' => $display->show_upcoming,
            'upcoming_count' => $display->upcoming_count,
            'refresh_seconds' => $display->refresh_seconds,
            'is_active' => $display->is_active,
        ];
    }

    /**
     * ============================================================
     * HALAMAN TV / DIGITAL SIGNAGE (PUBLIK, TANPA LOGIN)
     * ============================================================
     * Diakses dari browser TV lewat link berisi token rahasia — bukan
     * lewat sistem auth biasa, karena perangkat TV tidak login manual.
     * Satu endpoint ini melayani dua tipe tampilan (room / all) sesuai
     * konfigurasi yang tersimpan di baliknya.
     */
    public function show(string $token)
    {
        $display = RoomDisplay::with('room')->where('display_token', $token)->first();

        if (!$display || !$display->is_active) {
            abort(404);
        }

        $now = Carbon::now();

        if ($display->type === 'all') {
            return $this->showAllRoomsBoard($display, $now);
        }

        return $this->showSingleRoom($display, $now);
    }

    private function showSingleRoom(RoomDisplay $display, Carbon $now)
    {
        $room = $display->room;

        if (!$room) {
            abort(404, 'Ruangan untuk tampilan ini sudah tidak ada.');
        }

        $todayBookings = Booking::where('room_id', $room->id)
            ->where('status', 1)
            ->whereDate('start_time', $now->toDateString())
            ->orderBy('start_time')
            ->with('user')
            ->get();

        $currentBooking = $todayBookings->first(function ($b) use ($now) {
            return $now->between(Carbon::parse($b->start_time), Carbon::parse($b->end_time));
        });

        $upcomingBookings = $todayBookings->filter(function ($b) use ($now) {
            return Carbon::parse($b->start_time)->gt($now);
        })->take($display->upcoming_count);

        return view('signage.show', compact('display', 'room', 'currentBooking', 'upcomingBookings', 'now'));
    }

    private function showAllRoomsBoard(RoomDisplay $display, Carbon $now)
    {
        $rooms = Room::where('status', 1)->orderBy('name')->get();

        $todayBookings = Booking::where('status', 1)
            ->whereDate('start_time', $now->toDateString())
            ->whereIn('room_id', $rooms->pluck('id'))
            ->with('user')
            ->orderBy('start_time')
            ->get()
            ->groupBy('room_id');

        // Sekarang dikirim SELURUH jadwal hari ini per ruangan (bukan cuma
        // status sekarang + rapat berikutnya) — supaya papan TV menampilkan
        // agenda lengkap satu hari penuh untuk tiap ruangan.
        $roomsSchedule = $rooms->map(function ($room) use ($todayBookings, $now) {
            $bookings = $todayBookings->get($room->id, collect());

            $currentId = optional($bookings->first(function ($b) use ($now) {
                return $now->between(Carbon::parse($b->start_time), Carbon::parse($b->end_time));
            }))->id;

            return [
                'room' => $room,
                'bookings' => $bookings,
                'is_occupied' => (bool) $currentId,
                'current_id' => $currentId,
            ];
        });

        return view('signage.show-all', compact('display', 'roomsSchedule', 'now'));
    }
}