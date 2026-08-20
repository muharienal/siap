<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomPhoto;
use App\Models\Facility;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RoomController extends Controller
{
    /**
     * Halaman "Ruang Meeting" untuk karyawan: daftar semua ruangan dalam bentuk kartu.
     */
    public function list()
    {
        // FIX: badge status Tersedia/Terisi dihapus dari tampilan (kartu
        // ruangan sekarang cuma foto + nama), jadi query booking hari ini
        // untuk menghitung $roomStatus sudah tidak diperlukan lagi.
        $rooms = Room::with(['photos', 'facilities'])->orderBy('name')->get();

        if (request()->wantsJson()) {
            return response()->json([
                'grid' => view('pages.partials._room-list-grid', compact('rooms'))->render(),
                'signature' => md5($rooms->pluck('id')->implode(',') . '|' . optional($rooms->max('updated_at'))->timestamp),
            ]);
        }

        return view('pages.room-list', compact('rooms'));
    }

    /**
     * Halaman detail ruangan (PUBLIK): galeri, deskripsi, informasi, dan jadwal hari ini.
     */
    public function show(Room $room)
    {
        $room->load(['photos', 'facilities']);

        $today = Carbon::today();

        $timeSlots = [];
        $slot = Carbon::createFromTime(7, 0, 0);
        $end = Carbon::createFromTime(16, 0, 0);
        while ($slot->lte($end)) {
            $timeSlots[] = $slot->format('H:i');
            $slot->addMinutes(30);
        }

        $todayBookings = Booking::where('room_id', $room->id)
            ->whereDate('start_time', $today)
            ->whereIn('status', [0, 1])
            ->orderBy('start_time')
            ->get();

        $schedule = [];
        foreach ($timeSlots as $time) {
            $slotStart = Carbon::parse($today->format('Y-m-d') . ' ' . $time);
            $booking = $todayBookings->first(function ($b) use ($slotStart) {
                return $slotStart->gte(Carbon::parse($b->start_time)) && $slotStart->lt(Carbon::parse($b->end_time));
            });
            $schedule[$time] = $booking;
        }

        $isAvailableNow = true;
        $now = Carbon::now();
        foreach ($todayBookings as $booking) {
            if ($now->between(Carbon::parse($booking->start_time), Carbon::parse($booking->end_time))) {
                $isAvailableNow = false;
                break;
            }
        }

        return view('pages.room-show', compact('room', 'timeSlots', 'schedule', 'isAvailableNow', 'today'));
    }

    /**
     * Live-refresh (polling) khusus untuk kartu "Jadwal Hari Ini" di
     * halaman detail ruangan (karyawan) — supaya kalau ada booking baru
     * disetujui admin, jadwalnya langsung kelihatan tanpa reload.
     */
    public function liveSchedule(Room $room)
    {
        $today = Carbon::today();

        $timeSlots = [];
        $slot = Carbon::createFromTime(7, 0, 0);
        $end = Carbon::createFromTime(16, 0, 0);
        while ($slot->lte($end)) {
            $timeSlots[] = $slot->format('H:i');
            $slot->addMinutes(30);
        }

        $todayBookings = Booking::where('room_id', $room->id)
            ->whereDate('start_time', $today)
            ->whereIn('status', [0, 1])
            ->orderBy('start_time')
            ->get();

        $schedule = [];
        foreach ($timeSlots as $time) {
            $slotStart = Carbon::parse($today->format('Y-m-d') . ' ' . $time);
            $booking = $todayBookings->first(function ($b) use ($slotStart) {
                return $slotStart->gte(Carbon::parse($b->start_time)) && $slotStart->lt(Carbon::parse($b->end_time));
            });
            $schedule[$time] = $booking;
        }

        return response()->json([
            'schedule' => view('pages.partials._room-today-schedule', compact('schedule', 'today'))->render(),
            'signature' => md5($todayBookings->pluck('id')->implode(',') . '|' . optional($todayBookings->max('updated_at'))->timestamp),
        ]);
    }

    /**
     * Halaman detail ruangan (SETTINGS/ADMIN): Untuk melihat detail, edit, dan hapus.
     */
    public function showSettings(Room $room)
    {
        $room->load(['photos', 'facilities']);
        
        $availableFacilities = Facility::whereNotIn(
            'id', 
            $room->facilities->pluck('id')
        )->orderBy('name')->get();

        if (request()->wantsJson()) {
            return response()->json([
                'info' => view('settings.rooms.partials._room-info-card', compact('room'))->render(),
                'facilities' => view('settings.rooms.partials._facilities-list', compact('room'))->render(),
                'signature' => md5($room->updated_at . '|' . $room->facilities->map(function ($f) {
                    return $f->id . ':' . $f->pivot->quantity;
                })->implode(',')),
            ]);
        }

        return view('settings.rooms.show', compact('room', 'availableFacilities'));
    }

    /**
     * Halaman index Manajemen Ruangan (Settings).
     */
    public function index()
    {
        $rooms = Room::with(['photos', 'facilities'])->orderBy('name')->get();
        $facilities = Facility::orderBy('name')->get();

        $roomsData = $rooms->map(function($room) {
            return [
                'id' => $room->id,
                'name' => $room->name,
                'capacity' => $room->capacity,
                'location' => $room->location,
                'status' => $room->status,
                'photos' => $room->photos->map(function($photo) {
                    return $photo->photo_url;
                })->toArray(),
                'facilities' => $room->facilities->map(function($f) {
                    return [
                        'name' => $f->name,
                        'quantity' => $f->pivot->quantity ?? 1
                    ];
                })->toArray(),
                'edit_url' => route('settings.rooms.edit', $room->id),
                'delete_url' => route('settings.rooms.destroy', $room->id),
            ];
        });

        if (request()->wantsJson()) {
            return response()->json([
                'grid' => view('settings.rooms.partials._room-grid', compact('rooms'))->render(),
                'signature' => md5($rooms->pluck('id')->implode(',') . '|' . optional($rooms->max('updated_at'))->timestamp),
            ]);
        }

        return view('settings.rooms.index', compact('rooms', 'roomsData', 'facilities'));
    }

    public function create()
    {
        return view('settings.rooms.create');
    }

    public function store(Request $request)
    {
        // Validasi: facilities di-set nullable agar tidak error 422 jika tidak ada yang dicentang
        $request->validate([
            'name' => 'required|string|max:255|unique:rooms,name',
            'capacity' => 'required|integer|min:1',
            'location' => 'required|string|max:255',
            'status' => 'required|in:0,1',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'facilities.*.active' => 'nullable',
            'facilities.*.quantity' => 'nullable|integer|min:1',
        ]);

        try {
            $room = Room::create([
                'name' => $request->name,
                'capacity' => $request->capacity,
                'location' => $request->location,
                'status' => $request->status,
            ]);

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $index => $photo) {
                    $path = $photo->store('rooms', 'public');
                    RoomPhoto::create([
                        'room_id' => $room->id,
                        'photo_path' => $path,
                        'order' => $index,
                    ]);
                }
            }

            // Attach fasilitas yang dicentang
            if ($request->has('facilities')) {
                $facilitiesData = [];
                foreach ($request->facilities as $facilityId => $data) {
                    if (isset($data['active'])) {
                        $facilitiesData[$facilityId] = ['quantity' => $data['quantity'] ?? 1];
                    }
                }
                if (!empty($facilitiesData)) {
                    $room->facilities()->attach($facilitiesData);
                }
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ruangan berhasil ditambahkan.'
                ]);
            }

            return redirect()->route('settings.rooms.index')
                ->with('success', 'Ruangan berhasil ditambahkan.');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit(Room $room)
    {
        $room->load('photos');
        return view('settings.rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:rooms,name,' . $room->id,
            'capacity' => 'required|integer|min:1',
            'location' => 'required|string|max:255',
            'status' => 'required|in:0,1',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ]);

        try {
            $room->update([
                'name' => $request->name,
                'capacity' => $request->capacity,
                'location' => $request->location,
                'status' => $request->status,
            ]);

            if ($request->filled('delete_photos')) {
                $deleteIds = explode(',', $request->delete_photos);
                $photosToDelete = RoomPhoto::whereIn('id', $deleteIds)->where('room_id', $room->id)->get();
                foreach ($photosToDelete as $photo) {
                    Storage::disk('public')->delete($photo->photo_path);
                    $photo->delete();
                }
            }

            if ($request->hasFile('photos')) {
                $maxOrder = RoomPhoto::where('room_id', $room->id)->max('order') ?? -1;
                foreach ($request->file('photos') as $index => $photo) {
                    $path = $photo->store('rooms', 'public');
                    RoomPhoto::create([
                        'room_id' => $room->id,
                        'photo_path' => $path,
                        'order' => $maxOrder + $index + 1,
                    ]);
                }
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ruangan berhasil diperbarui.'
                ]);
            }

            return redirect()->route('settings.rooms.show', $room->id)
                ->with('success', 'Ruangan berhasil diperbarui.');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(Room $room)
    {
        // FIX (bug krusial — potensi kehilangan data diam-diam): kolom
        // bookings.room_id memakai ON DELETE CASCADE — artinya menghapus
        // ruangan akan OTOMATIS DAN DIAM-DIAM menghapus SEMUA riwayat
        // booking ruangan itu juga (termasuk yang sudah Disetujui/selesai),
        // tanpa peringatan apa pun. Ini bertentangan langsung dengan aturan
        // yang sudah ditegakkan di seluruh aplikasi ini: riwayat booking
        // yang sudah diproses bersifat PERMANEN, tidak boleh
        // diubah/dihapus siapa pun. Sekarang penghapusan ruangan yang
        // masih punya riwayat booking (apa pun statusnya, kapan pun
        // waktunya) diblokir — admin diarahkan menonaktifkan ruangan
        // (kolom status, sudah ada fiturnya) sebagai gantinya, supaya
        // ruangan tidak bisa dipakai booking baru TANPA menghapus riwayat
        // lama.
        if (\App\Models\Booking::where('room_id', $room->id)->exists()) {
            return back()->with('error', 'Ruangan ini masih memiliki riwayat peminjaman dan tidak dapat dihapus (riwayat bersifat permanen). Nonaktifkan ruangan ini jika tidak ingin dipakai lagi.');
        }

        foreach ($room->photos as $photo) {
            Storage::disk('public')->delete($photo->photo_path);
            $photo->delete();
        }
        $room->facilities()->detach();
        $room->delete();

        return redirect()->route('settings.rooms.index')
            ->with('success', 'Ruangan berhasil dihapus.');
    }

    /**
     * Hapus foto ruangan individually (AJAX).
     */
    public function deletePhoto(Room $room, Request $request)
    {
        $request->validate(['photo_id' => 'required|exists:room_photos,id']);
        
        $photo = RoomPhoto::where('id', $request->photo_id)->where('room_id', $room->id)->first();
        
        if ($photo) {
            Storage::disk('public')->delete($photo->photo_path);
            $photo->delete();
            return response()->json(['success' => true, 'message' => 'Foto berhasil dihapus.']);
        }

        return response()->json(['success' => false, 'message' => 'Foto tidak ditemukan.'], 404);
    }
}