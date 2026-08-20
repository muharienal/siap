<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Division;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['division', 'position'])->orderBy('full_name')->get();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'table' => view('settings.users.partials._user-table', compact('users'))->render(),
                'signature' => md5($users->pluck('id')->implode(',') . '|' . optional($users->max('updated_at'))->timestamp),
            ]);
        }

        // Divisi & Bidang sekarang dikelola langsung di halaman ini lewat
        // modal "Kelola Bidang & Divisi", jadi ikut dimuat di sini.
        $divisions = Division::orderBy('name')->get();
        $positions = Position::orderBy('name')->get();

        return view('settings.users.index', compact('users', 'divisions', 'positions'));
    }

    public function create()
    {
        $divisions = Division::orderBy('name')->get();
        $positions = Position::orderBy('name')->get();
        return view('settings.users.create', compact('divisions', 'positions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|string|max:50|unique:users,nip',
            'full_name' => 'required|string|max:255',
            'division_id' => 'required|exists:divisions,id',
            'position_id' => [
                'required',
                'exists:positions,id',
                function ($attribute, $value, $fail) use ($request) {
                    $position = Position::find($value);
                    if ($position && (int) $position->division_id !== (int) $request->division_id) {
                        $fail('Bidang yang dipilih tidak termasuk dalam Divisi yang dipilih.');
                    }
                },
            ],
            // FIX: role 3 (Sesdir) belum bisa dibuat sebelumnya padahal
            // sudah didukung penuh di alur booking (booking_type Priority).
            'role' => 'required|in:1,2,3',
            'password' => 'required|string|min:6',
            'is_active' => 'required|in:0,1',
        ]);

        DB::beginTransaction();
        try {
            User::create([
                'nip' => $request->nip,
                'full_name' => $request->full_name,
                'division_id' => $request->division_id,
                'position_id' => $request->position_id,
                'gender' => $request->gender ?? 'L',
                'employment_status' => $request->employment_status ?? 'Kontrak',
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'is_active' => $request->is_active,
            ]);

            DB::commit();
            return redirect()->route('settings.users.index')
                ->with('success', 'Pengguna berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User store error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit($id)
    {
        $user = User::with(['division', 'position'])->findOrFail($id);
        $divisions = Division::orderBy('name')->get();
        $positions = Position::orderBy('name')->get();
        return view('settings.users.edit', compact('user', 'divisions', 'positions'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nip' => 'required|string|max:50|unique:users,nip,' . $user->id,
            'full_name' => 'required|string|max:255',
            'division_id' => 'required|exists:divisions,id',
            'position_id' => [
                'required',
                'exists:positions,id',
                function ($attribute, $value, $fail) use ($request) {
                    $position = Position::find($value);
                    if ($position && (int) $position->division_id !== (int) $request->division_id) {
                        $fail('Bidang yang dipilih tidak termasuk dalam Divisi yang dipilih.');
                    }
                },
            ],
            // FIX: role 3 (Sesdir) belum bisa diberikan ke pengguna manapun sebelumnya.
            'role' => 'required|in:1,2,3',
            'password' => 'nullable|string|min:6',
            'is_active' => 'required|in:0,1',
        ]);

        DB::beginTransaction();
        try {
            $userData = [
                'nip' => $request->nip,
                'full_name' => $request->full_name,
                'division_id' => $request->division_id,
                'position_id' => $request->position_id,
                'role' => $request->role,
                'is_active' => $request->is_active,
            ];
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $user->update($userData);

            DB::commit();
            return redirect()->route('settings.users.index')
                ->with('success', 'Pengguna berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User update error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return redirect()->route('settings.users.index')
                ->with('success', 'Pengguna berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('User delete error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}