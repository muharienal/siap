<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomPhoto;
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
        $rooms = Room::with('photos')->orderBy('name')->get();

        $now = Carbon::now();
        $todayBookings = Booking::whereDate('start_time', Carbon::today())
            ->whereIn('status', [0, 1])
            ->get();

        $roomStatus = [];
        foreach ($rooms as $room) {
            $busy = $todayBookings->contains(function ($b) use ($room, $now) {
                return $b->room_id === $room->id
                    && $now->between(Carbon::parse($b->start_time), Carbon::parse($b->end_time));
            });
            $roomStatus[$room->id] = $busy ? 'busy' : 'available';
        }

        return view('pages.room-list', compact('rooms', 'roomStatus'));
    }

    /**
     * Halaman detail ruangan: galeri, deskripsi, informasi, dan jadwal hari ini.
     */
    public function show(Room $room)
    {
        $room->load('photos');

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

    public function index()
    {
        $rooms = Room::with('photos')->orderBy('name')->get();

        $roomsData = $rooms->map(function($room) {
            $photos = $room->photos ?? collect();
            return [
                'id' => $room->id,
                'name' => $room->name,
                'photos' => $photos->map(function($photo) {
                    return $photo->photo_url;
                })->toArray(),
            ];
        });

        return view('settings.rooms.index', compact('rooms', 'roomsData'));
    }

    public function create()
    {
        return view('settings.rooms.create');
    }

    public function store(Request $request)
    {
        // Validasi
        $request->validate([
            'name' => 'required|string|max:255|unique:rooms,name',
            'capacity' => 'required|integer|min:1',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:0,1',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Simpan ruangan
        $room = Room::create([
            'name' => $request->name,
            'capacity' => $request->capacity,
            'location' => $request->location,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        // Upload foto
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

        return redirect()->route('settings.rooms.index')
            ->with('success', 'Ruangan berhasil ditambahkan.');
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
            'description' => 'nullable|string',
            'status' => 'required|in:0,1',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $room->update([
            'name' => $request->name,
            'capacity' => $request->capacity,
            'location' => $request->location,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        // Hapus foto yang ditandai
        if ($request->filled('delete_photos')) {
            $deleteIds = explode(',', $request->delete_photos);
            $photosToDelete = RoomPhoto::whereIn('id', $deleteIds)->where('room_id', $room->id)->get();
            foreach ($photosToDelete as $photo) {
                Storage::disk('public')->delete($photo->photo_path);
                $photo->delete();
            }
        }

        // Upload foto baru
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

        return redirect()->route('settings.rooms.index')
            ->with('success', 'Ruangan berhasil diperbarui.');
    }

    public function destroy(Room $room)
    {
        foreach ($room->photos as $photo) {
            Storage::disk('public')->delete($photo->photo_path);
            $photo->delete();
        }
        $room->delete();

        return redirect()->route('settings.rooms.index')
            ->with('success', 'Ruangan berhasil dihapus.');
    }
}