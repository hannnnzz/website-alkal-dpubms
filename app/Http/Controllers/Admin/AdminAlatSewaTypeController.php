<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AlatSewaType;

class AdminAlatSewaTypeController extends Controller
{
    // Tampilkan semua alat sewa
    public function index()
    {
        $alats = AlatSewaType::all();
        return view('admin.alatsewa.index', compact('alats'));
    }

    // Form tambah alat sewa
    public function create()
    {
        return view('admin.alatsewa.create');
    }

    // Simpan alat sewa baru
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|integer|min:0',
        ]);

        AlatSewaType::create($request->only(['name', 'price']));

        return redirect()
            ->route('admin.alat-sewa-types.index')
            ->with('success', 'Alat sewa berhasil ditambahkan.');
    }

    // Tampilkan detail alat sewa (route model binding)
    public function show(AlatSewaType $alat_sewa_type)
    {
        return view('admin.alatsewa.show', ['alat' => $alat_sewa_type]);
    }

    // Form edit alat sewa
    public function edit(AlatSewaType $alat_sewa_type)
    {
        return view('admin.alatsewa.edit', ['alat' => $alat_sewa_type]);
    }

    // Update alat sewa
    public function update(Request $request, AlatSewaType $alat_sewa_type)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|integer|min:0',
        ]);

        $alat_sewa_type->update($request->only(['name', 'price']));

        return redirect()
            ->route('admin.alat-sewa-types.index')
            ->with('success', 'Alat sewa berhasil diupdate.');
    }

    // Hapus alat sewa
    public function destroy(AlatSewaType $alat_sewa_type)
    {
        $alat_sewa_type->delete();

        return redirect()
            ->route('admin.alat-sewa-types.index')
            ->with('success', 'Alat sewa berhasil dihapus.');
    }
    
    // Toggle lock status
    public function toggleLock(AlatSewaType $alat_sewa_type)
    {
        $alat_sewa_type->is_locked = !$alat_sewa_type->is_locked;
        $alat_sewa_type->save();

        return redirect()
            ->route('admin.alat-sewa-types.index')
            ->with('success', 'Status ketersediaan alat berhasil diubah.');
    }
}
