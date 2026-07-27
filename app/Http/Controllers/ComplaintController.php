<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ComplaintController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Complaint::with(['user', 'booking.room'])->orderBy('created_at', 'desc');
        
        // Filter by search
        if (request('search')) {
            $query->where('description', 'like', '%' . request('search') . '%');
        }
        
        // Filter by category
        if (request('category')) {
            $query->where('category', request('category'));
        }
        
        // Filter by status
        if (request('status')) {
            $query->where('status', request('status'));
        }
        
        $complaints = $query->paginate(10);
        
        return view('complaints.index', compact('complaints'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $bookings = Booking::where('user_id', Auth::id())
                          ->with('room')
                          ->orderBy('created_at', 'desc')
                          ->get();
        
        return view('complaints.add', compact('bookings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'nullable|exists:bookings,id',
            'category' => 'required|in:peminjaman,peralatan,karyawan,lainnya',
            'description' => 'required|string|min:10',
            'evidence' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120'
        ]);
        
        $validated['user_id'] = Auth::id();
        $validated['status'] = 0;
        
        // Handle evidence upload
        if ($request->hasFile('evidence')) {
            $file = $request->file('evidence');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('complaints', $filename, 'public');
            $validated['evidence_path'] = $path;
        }
        
        Complaint::create($validated);
        
        return redirect()->route('complaints.index')
                        ->with('success', 'Complaint berhasil disubmit. Tim admin akan meresponnya segera.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Complaint $complaint)
    {
        $complaint->load(['user', 'booking.room']);
        return view('complaints.show', compact('complaint'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Complaint $complaint)
    {
        // Only allow editing if user owns the complaint and it's still pending
        if ($complaint->user_id !== Auth::id() || $complaint->status !== 0) {
            return redirect()->route('complaints.index')
                           ->with('error', 'Anda tidak dapat mengedit complaint ini.');
        }
        
        $bookings = Booking::where('user_id', Auth::id())
                          ->with('room')
                          ->orderBy('created_at', 'desc')
                          ->get();
        
        return view('complaints.edit', compact('complaint', 'bookings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Complaint $complaint)
    {
        // Only allow updating if user owns the complaint and it's still pending
        if ($complaint->user_id !== Auth::id() || $complaint->status !== 0) {
            return redirect()->route('complaints.index')
                           ->with('error', 'Anda tidak dapat mengedit complaint ini.');
        }
        
        $validated = $request->validate([
            'booking_id' => 'nullable|exists:bookings,id',
            'category' => 'required|in:peminjaman,peralatan,karyawan,lainnya',
            'description' => 'required|string|min:10',
            'evidence' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120'
        ]);
        
        // Handle evidence upload
        if ($request->hasFile('evidence')) {
            // Delete old evidence if exists
            if ($complaint->evidence_path) {
                Storage::disk('public')->delete($complaint->evidence_path);
            }
            
            $file = $request->file('evidence');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('complaints', $filename, 'public');
            $validated['evidence_path'] = $path;
        }
        
        $complaint->update($validated);
        
        return redirect()->route('complaints.index')
                        ->with('success', 'Complaint berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Complaint $complaint)
    {
        // Boleh dihapus oleh pemilik complaint atau admin, terlepas dari statusnya
        if ($complaint->user_id !== Auth::id() && Auth::user()->role != 1) {
            return redirect()->route('complaints.index')
                           ->with('error', 'Anda tidak dapat menghapus complaint ini.');
        }
        
        // Delete evidence file if exists
        if ($complaint->evidence_path) {
            Storage::disk('public')->delete($complaint->evidence_path);
        }
        
        $complaint->delete();
        
        return redirect()->route('complaints.index')
                        ->with('success', 'Complaint berhasil dihapus.');
    }
    
    /**
     * Respond to complaint (Admin only)
     */
    public function respond(Request $request, Complaint $complaint)
    {
        // Only admin can respond
        if (Auth::user()->role !== 1) {
            return redirect()->route('complaints.index')
                           ->with('error', 'Anda tidak memiliki akses untuk merespon complaint.');
        }
        
        $validated = $request->validate([
            'admin_response' => 'required|string|min:10',
            'status' => 'required|in:1,2,0'
        ]);
        
        $complaint->update($validated);
        
        return redirect()->route('complaints.show', $complaint)
                        ->with('success', 'Response berhasil dikirim.');
    }
}