<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::orderBy('name')->get();
        return view('settings.positions.index', compact('positions'));
    }

    public function create()
    {
        return view('settings.positions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:positions,name',
            'description' => 'nullable|string',
        ]);

        Position::create($request->all());

        return redirect()->route('settings.positions.index')
            ->with('success', 'Bidang berhasil ditambahkan.');
    }

    public function edit(Position $position)
    {
        return view('settings.positions.edit', compact('position'));
    }

    public function update(Request $request, Position $position)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:positions,name,' . $position->id,
            'description' => 'nullable|string',
        ]);

        $position->update($request->all());

        return redirect()->route('settings.positions.index')
            ->with('success', 'Bidang berhasil diperbarui.');
    }

    public function destroy(Position $position)
    {
        $position->delete();

        return redirect()->route('settings.positions.index')
            ->with('success', 'Bidang berhasil dihapus.');
    }
}