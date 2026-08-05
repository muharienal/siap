<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Room;
use Illuminate\Http\Request;

class FacilityController extends Controller
{

    public function index(Request $request)
    {
        $rooms = Room::with(['photos', 'facilities'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->orderBy('name')
            ->get();

        return view('settings.facilities.index', compact('rooms'));
    }

    public function master()
    {
        $facilities = Facility::orderBy('name')->get();
        return view('settings.facilities.master', compact('facilities'));
    }

    public function create()
    {
        return view('settings.facilities.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:facilities,name',
            'description' => 'nullable|string',
            'storage_location' => 'required|string|max:255',
        ]);

        Facility::create([
            'name' => $request->name,
            'description' => $request->description,
            'storage_location' => $request->storage_location,
        ]);

        return redirect()->route('settings.facilities.master')
            ->with('success', 'Fasilitas berhasil ditambahkan.');
    }

    public function edit(Request $request, Facility $facility)
    {
        $roomId = $request->query('room_id');
        $roomQuantity = null;

        if ($roomId) {
            $pivotRoom = $facility->rooms()->where('rooms.id', $roomId)->first();
            $roomQuantity = $pivotRoom?->pivot->quantity;
        }

        return view('settings.facilities.edit', compact('facility', 'roomId', 'roomQuantity'));
    }

    public function update(Request $request, Facility $facility)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:facilities,name,' . $facility->id,
            'description' => 'nullable|string',
            'storage_location' => 'required|string|max:255',
            'room_id' => 'nullable|exists:rooms,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $facility->update([
            'name' => $request->name,
            'description' => $request->description,
            'storage_location' => $request->storage_location,
        ]);

        if ($request->filled('room_id') && $request->filled('quantity')) {
            $facility->rooms()->updateExistingPivot($request->room_id, [
                'quantity' => $request->quantity,
            ]);

            return redirect()->route('settings.facilities.room', $request->room_id)
                ->with('success', 'Fasilitas berhasil diperbarui.');
        }

        return redirect()->route('settings.facilities.master')
            ->with('success', 'Fasilitas berhasil diperbarui.');
    }

    public function destroy(Facility $facility)
    {
        $facility->delete();

        return redirect()->route('settings.facilities.master')
            ->with('success', 'Fasilitas berhasil dihapus.');
    }

    public function room(Room $room)
    {
        $room->load('facilities');

        // Fasilitas master yang belum ditambahkan ke ruangan ini
        $availableFacilities = Facility::whereNotIn(
            'id',
            $room->facilities->pluck('id')
        )->orderBy('name')->get();

        return view('settings.facilities.room', compact('room', 'availableFacilities'));
    }

    public function attach(Request $request, Room $room)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        if ($request->facility_id === 'new') {
            $request->validate([
                'new_facility_name' => 'required|string|max:255|unique:facilities,name',
                'new_facility_location' => 'required|string|max:255',
            ], [
                'new_facility_name.required' => 'Nama fasilitas baru wajib diisi.',
                'new_facility_name.unique' => 'Nama fasilitas sudah ada, pilih dari daftar yang tersedia.',
                'new_facility_location.required' => 'Lokasi penyimpanan wajib diisi.',
            ]);

            $facility = Facility::create([
                'name' => $request->new_facility_name,
                'storage_location' => $request->new_facility_location,
            ]);
        } else {
            $request->validate([
                'facility_id' => 'required|exists:facilities,id',
            ]);

            $facility = Facility::findOrFail($request->facility_id);
        }

        $room->facilities()->syncWithoutDetaching([
            $facility->id => ['quantity' => $request->quantity],
        ]);

        return redirect()->route('settings.facilities.room', $room->id)
            ->with('success', 'Fasilitas berhasil ditambahkan ke ruangan.');
    }

    
    public function updateQuantity(Request $request, Room $room, Facility $facility)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $room->facilities()->updateExistingPivot($facility->id, [
            'quantity' => $request->quantity,
        ]);

        return redirect()->route('settings.facilities.room', $room->id)
            ->with('success', 'Jumlah fasilitas berhasil diperbarui.');
    }

    
    public function detach(Room $room, Facility $facility)
    {
        $room->facilities()->detach($facility->id);

        return redirect()->route('settings.facilities.room', $room->id)
            ->with('success', 'Fasilitas berhasil dihapus dari ruangan.');
    }
}