<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class RoomController extends Controller
{
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