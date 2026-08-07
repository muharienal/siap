<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\DivisionRequest;
use Illuminate\Http\Request;
use App\Models\Division;

class DivisionController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $divisions = Division::all();
        return view('settings.divisions.index', compact('divisions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('settings.divisions.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DivisionRequest $request)
    {
        try {


            Division::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return redirect('/settings/divisions')->with('success', 'Divisi berhasil ditambahkan.');
        } catch (\Throwable $th) {

            redirect('/settings/divisions')->withInput()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data divisi. Silakan coba lagi.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Division $division)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Division $division)
    {
        return view('settings.divisions.edit', compact('division'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DivisionRequest $request, Division $division)
    {
        try {

            $division->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return redirect('/settings/divisions')->with('success', 'Divisi berhasil diupdate.');

        } catch (\Throwable $th) {

            redirect('/settings/divisions')->withInput()->withErrors(['error' => 'Terjadi kesalahan saat mengupdate data divisi. Silakan coba lagi.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Division $division)
    {
        try {
            $division->delete();
            return redirect('/settings/divisions')->with('success', 'Divisi berhasil dihapus.');
        } catch (\Throwable $th) {
            return redirect('/settings/divisions')->withErrors(['error' => 'Terjadi kesalahan saat menghapus data divisi. Silakan coba lagi.']);
        }
    }


}
