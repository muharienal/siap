<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    /**
     * Bidang dikelola via modal "Kelola Bidang" di halaman Pengguna.
     * Setiap Bidang wajib terhubung ke satu Divisi (hierarki
     * Divisi -> Bidang -> Pengguna). Semua method di sini merespons JSON
     * untuk dikonsumsi via fetch() AJAX.
     */
    public function store(Request $request)
    {
        $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'name' => 'required|string|max:255|unique:positions,name',
        ], [
            'division_id.required' => 'Divisi harus dipilih.',
            'division_id.exists' => 'Divisi yang dipilih tidak valid.',
        ]);

        $position = Position::create([
            'division_id' => $request->division_id,
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bidang berhasil ditambahkan.',
            'data' => $position->load('division'),
        ]);
    }

    public function update(Request $request, Position $position)
    {
        $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'name' => 'required|string|max:255|unique:positions,name,' . $position->id,
        ], [
            'division_id.required' => 'Divisi harus dipilih.',
            'division_id.exists' => 'Divisi yang dipilih tidak valid.',
        ]);

        $position->update([
            'division_id' => $request->division_id,
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bidang berhasil diperbarui.',
            'data' => $position->load('division'),
        ]);
    }

    public function destroy(Position $position)
    {
        if ($position->employees()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Bidang tidak dapat dihapus karena masih digunakan oleh pengguna.',
            ], 422);
        }

        $position->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bidang berhasil dihapus.',
        ]);
    }
}