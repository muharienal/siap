<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee;
use App\Models\Division;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display the user profile.
     */
    public function index()
    {
        $user = Auth::user();
        $employee = $user->employee;
        $divisions = Division::all();
        $positions = Position::all();
        
        return view('profile.index', compact('user', 'employee', 'divisions', 'positions'));
    }

    /**
     * Show the form for editing the profile.
     */
    public function edit()
    {
        $user = Auth::user();
        $employee = $user->employee;
        $divisions = Division::all();
        $positions = Position::all();
        
        return view('profile.edit', compact('user', 'employee', 'divisions', 'positions'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        // Validation rules
        $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'full_name' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'gender' => 'required|in:P,L',
            'birth_date' => 'nullable|date',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'division_id' => 'nullable|exists:divisions,id',
            'position_id' => 'nullable|exists:positions,id',
            'employment_status' => 'required|in:active,inactive,terminated',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        try {
            // Update user data
            $user->update([
                'email' => $request->email,
            ]);
            
            // Update or create employee data
            $employeeData = [
                'full_name' => $request->full_name,
                'nip' => $request->nip,
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'division_id' => $request->division_id,
                'position_id' => $request->position_id,
                'employment_status' => $request->employment_status,
            ];
            
            // Handle photo upload
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $photoPath = $photo->store('profile-photos', 'public');
                
                // Delete old photo if exists
                if ($user->employee && $user->employee->photo_path) {
                    Storage::disk('public')->delete($user->employee->photo_path);
                }
                
                $employeeData['photo_path'] = $photoPath;
            }
            
            if ($user->employee) {
                $user->employee->update($employeeData);
            } else {
                Employee::create($employeeData);
            }
            
            return redirect()->route('profile')->with('success', 'Profile berhasil diperbarui!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui profile: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);
        
        $user = Auth::user();
        
        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Password lama tidak benar!');
        }
        
        // Update password
        $user->update([
            'password' => Hash::make($request->password)
        ]);
        
        return redirect()->route('profile')->with('success', 'Password berhasil diperbarui!');
    }

    /**
     * Remove the user's photo.
     */
    public function removePhoto()
    {
        $user = Auth::user();
        
        if ($user->employee && $user->employee->photo_path) {
            Storage::disk('public')->delete($user->employee->photo_path);
            $user->employee->update(['photo_path' => null]);
        }
        
        return redirect()->back()->with('success', 'Foto profile berhasil dihapus!');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
