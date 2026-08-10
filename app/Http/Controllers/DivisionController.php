<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\DivisionRequest;
use Illuminate\Http\Request;
use App\Models\Division;

class DivisionController extends Controller
{
    /**
     * Divisi dikelola via modal "Kelola Divisi" di halaman Pengguna.
     * Divisi adalah level tertinggi dalam hierarki (Divisi -> Bidang ->
     * Pengguna), jadi tidak boleh dihapus selama masih punya Bidang di
     * bawahnya. Semua method di sini merespons JSON untuk fetch() AJAX.
     */
    public function store(DivisionRequest $request)
    {
        $division = Division::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Divisi berhasil ditambahkan.',
            'data' => $division,
        ]);
    }

    public function update(DivisionRequest $request, Division $division)
    {
        $division->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Divisi berhasil diperbarui.',
            'data' => $division,
        ]);
    }

    public function destroy(Division $division)
    {
        if ($division->positions()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Divisi tidak dapat dihapus karena masih memiliki Bidang di dalamnya. Hapus atau pindahkan Bidang tersebut terlebih dahulu.',
            ], 422);
        }

        if ($division->employees()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Divisi tidak dapat dihapus karena masih digunakan oleh pengguna.',
            ], 422);
        }

        $division->delete();

        return response()->json([
            'success' => true,
            'message' => 'Divisi berhasil dihapus.',
        ]);
    }
}