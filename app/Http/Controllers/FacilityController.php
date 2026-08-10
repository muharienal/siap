<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Room;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    /**
     * Tidak lagi digunakan karena dialihkan ke halaman index ruangan,
     * tetapi tetap dipertahankan agar route tidak rusak.
     */
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

    /**
     * Menampilkan halaman master fasilitas (jika masih diperlukan terpisah).
     */
    public function master()
    {
        $facilities = Facility::orderBy('name')->get();
        return view('settings.facilities.master', compact('facilities'));
    }

    public function create()
    {
        return view('settings.facilities.create');
    }

    /**
     * AJAX Store: Menyimpan fasilitas baru via JSON response.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:facilities,name',
        ]);

        $facility = Facility::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fasilitas berhasil ditambahkan.',
            'data' => $facility
        ]);
    }

    /**
     * AJAX Update: Memperbarui fasilitas via JSON response.
     */
    public function update(Request $request, Facility $facility)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:facilities,name,' . $facility->id,
        ]);

        $facility->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fasilitas berhasil diperbarui.',
            'data' => $facility
        ]);
    }

    /**
     * AJAX Destroy: Menghapus fasilitas via JSON response.
     */
    public function destroy(Facility $facility)
    {
        // Detach dari semua ruangan terkait sebelum hapus
        $facility->rooms()->detach();
        $facility->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fasilitas berhasil dihapus.'
        ]);
    }

    // ====================================================================
    // METHOD UNTUK FASILITAS PER RUANGAN (ROOM FACILITIES)
    // ====================================================================

    /**
     * FIX: Endpoint ini sebelumnya mencoba render view
     * 'settings.facilities.room' yang TIDAK ADA di filesystem, sehingga
     * setiap kali diakses (termasuk secara tidak langsung lewat redirect
     * dari attach/updateQuantity/detach di bawah) akan melempar
     * InvalidArgumentException: "View [settings.facilities.room] not found".
     *
     * Kelola fasilitas sekarang sepenuhnya terintegrasi di halaman detail
     * ruangan (settings.rooms.show) via modal "Kelola Fasilitas", jadi
     * halaman standalone ini tidak lagi diperlukan. Daripada merender view
     * yang tidak ada, redirect ke halaman detail ruangan yang sebenarnya.
     */
    public function room(Room $room)
    {
        return redirect()->route('settings.rooms.show', $room->id);
    }

    /**
     * Menambahkan fasilitas master ke ruangan tertentu (Attach ke pivot).
     *
     * FIX (Critical): sebelumnya method ini SELALU mengembalikan
     * redirect() HTML meskipun dipanggil via fetch()/AJAX dengan
     * Accept: application/json. fetch() otomatis mengikuti redirect
     * tersebut (ke route yang bahkan rusak, lihat method room() di atas),
     * sehingga JS di frontend menerima response yang gagal di-parse
     * sebagai JSON / berstatus non-2xx, dan menampilkan toast "Gagal"
     * PADAHAL operasi database (syncWithoutDetaching) sudah berhasil.
     * Sekarang method ini mendeteksi request AJAX/JSON dan membalas JSON
     * secara eksplisit, konsisten dengan store()/update()/destroy() di atas.
     */
    public function attach(Request $request, Room $room)
    {
        $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $facility = Facility::findOrFail($request->facility_id);

        $room->facilities()->syncWithoutDetaching([
            $facility->id => ['quantity' => $request->quantity],
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Fasilitas berhasil ditambahkan ke ruangan.',
            ]);
        }

        return redirect()->route('settings.rooms.show', $room->id)
            ->with('success', 'Fasilitas berhasil ditambahkan ke ruangan.');
    }

    /**
     * Update kuantitas fasilitas di ruangan tertentu (Update Pivot).
     * FIX: sama seperti attach() — balas JSON untuk request AJAX.
     */
    public function updateQuantity(Request $request, Room $room, Facility $facility)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $room->facilities()->updateExistingPivot($facility->id, [
            'quantity' => $request->quantity,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Jumlah fasilitas berhasil diperbarui.',
            ]);
        }

        return redirect()->route('settings.rooms.show', $room->id)
            ->with('success', 'Jumlah fasilitas berhasil diperbarui.');
    }

    /**
     * Melepas fasilitas dari ruangan tertentu (Detach Pivot).
     * FIX: sama seperti attach() — balas JSON untuk request AJAX.
     */
    public function detach(Request $request, Room $room, Facility $facility)
    {
        $room->facilities()->detach($facility->id);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Fasilitas berhasil dihapus dari ruangan.',
            ]);
        }

        return redirect()->route('settings.rooms.show', $room->id)
            ->with('success', 'Fasilitas berhasil dihapus dari ruangan.');
    }
}