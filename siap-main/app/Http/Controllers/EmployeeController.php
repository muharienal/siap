<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use App\Models\Division;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::with(['user', 'division', 'position'])->paginate(10);
        return view('employee.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::whereDoesntHave('employee')->get();
        $divisions = Division::all();
        $positions = Position::all();
        return view('employee.add', compact('users', 'divisions', 'positions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'position_id' => 'required|exists:positions,id',
            'full_name' => 'required|string|max:255',
            'nip' => 'required|string|max:50|unique:employees,nip',
            'gender' => 'required|in:P,L',
            'birth_date' => 'required|date|before:today',
            'phone_number' => 'required|string|max:15',
            'address' => 'required|string|max:500',
            'employment_status' => 'required|in:active,inactive,terminated',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            
            // Create unique filename
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Store file
            $path = $file->storeAs('employees', $filename, 'public');
            $validated['photo_path'] = $path;
        }

        Employee::create($validated);

        return redirect()->route('employees.index')
            ->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        $employee->load(['user', 'division', 'position']);
        return view('employee.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        $users = User::whereDoesntHave('employee')
            ->orWhere('id', $employee->user_id)
            ->get();
        $divisions = Division::all();
        $positions = Position::all();
        return view('employee.edit', compact('employee', 'users', 'divisions', 'positions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'position_id' => 'required|exists:positions,id',
            'full_name' => 'required|string|max:255',
            'nip' => 'required|string|max:50|unique:employees,nip,' . $employee->id,
            'gender' => 'required|in:P,L',
            'birth_date' => 'required|date|before:today',
            'phone_number' => 'required|string|max:15',
            'address' => 'required|string|max:500',
            'employment_status' => 'required|in:active,inactive,terminated',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($employee->photo_path) {
                Storage::disk('public')->delete($employee->photo_path);
            }
            
            $file = $request->file('photo');
            
            // Create unique filename
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Store file
            $path = $file->storeAs('employees', $filename, 'public');
            $validated['photo_path'] = $path;
        }

        $employee->update($validated);

        return redirect()->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        // Delete photo if exists
        if ($employee->photo_path) {
            Storage::disk('public')->delete($employee->photo_path);
        }

        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'Employee deleted successfully.');
    }
}
