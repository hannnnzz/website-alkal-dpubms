<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UjiType;

class AdminUjiTypeController extends Controller
{
    // Tampilkan semua jenis uji
    public function index()
    {
        $ujiTypes = UjiType::all();
        return view('admin.uji.index', compact('ujiTypes'));
    }

    // Form tambah jenis uji
    public function create()
    {
        return view('admin.uji.create');
    }

    // Simpan jenis uji baru
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|integer|min:0',
        ]);

        UjiType::create($request->only(['name', 'price']));
        return redirect()->route('admin.uji-types.index')->with('success', 'Jenis uji berhasil ditambahkan.');
    }

    // Tampilkan detail jenis uji
    public function show($id)
    {
        $ujiType = UjiType::findOrFail($id);
        return view('admin.uji.show', compact('ujiType'));
    }

    // Form edit jenis uji
    public function edit($id)
    {
        $ujiType = UjiType::findOrFail($id);
        return view('admin.uji.edit', compact('ujiType'));
    }

    // Update jenis uji
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|integer|min:0',
        ]);

        $ujiType = UjiType::findOrFail($id);
        $ujiType->update($request->only(['name', 'price']));
        return redirect()->route('admin.uji-types.index')->with('success', 'Jenis uji berhasil diupdate.');
    }

    // Hapus jenis uji
    public function destroy($id)
    {
        $ujiType = UjiType::findOrFail($id);
        $ujiType->delete();
        return redirect()->route('admin.uji-types.index')->with('success', 'Jenis uji berhasil dihapus.');
    }
}
