<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function index()
    {
        $facilities = Facility::orderBy('name')->get();
        return view('settings.facilities.index', compact('facilities'));
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

        return redirect()->route('settings.facilities.index')
            ->with('success', 'Fasilitas berhasil ditambahkan.');
    }

    public function edit(Facility $facility)
    {
        return view('settings.facilities.edit', compact('facility'));
    }

    public function update(Request $request, Facility $facility)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:facilities,name,' . $facility->id,
            'description' => 'nullable|string',
            'storage_location' => 'required|string|max:255',
        ]);

        $facility->update([
            'name' => $request->name,
            'description' => $request->description,
            'storage_location' => $request->storage_location,
        ]);

        return redirect()->route('settings.facilities.index')
            ->with('success', 'Fasilitas berhasil diperbarui.');
    }

    public function destroy(Facility $facility)
    {
        $facility->delete();

        return redirect()->route('settings.facilities.index')
            ->with('success', 'Fasilitas berhasil dihapus.');
    }
}