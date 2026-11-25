<?php

namespace App\Http\Controllers;

use App\Models\KategoriAlat;
use Illuminate\Http\Request;

class KategoriAlatController extends Controller
{
    public function index()
    {
        $kategoris = KategoriAlat::withCount('alatCamping')
            ->latest()
            ->paginate(10);

        return view('kategori.index', compact('kategoris'));
    }

    public function create()
    {
        return view('kategori.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori_alat,nama_kategori',
            'deskripsi'     => 'nullable|string|max:255',
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique'   => 'Nama kategori sudah terdaftar.',
        ]);

        KategoriAlat::create($validated);

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori alat berhasil ditambahkan.');
    }

    public function edit(KategoriAlat $kategori)
    {
        return view('kategori.edit', compact('kategori'));
    }

    public function update(Request $request, KategoriAlat $kategori)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori_alat,nama_kategori,' . $kategori->id,
            'deskripsi'     => 'nullable|string|max:255',
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique'   => 'Nama kategori sudah digunakan.',
        ]);

        $kategori->update($validated);

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori alat berhasil diperbarui.');
    }

    public function destroy(KategoriAlat $kategori)
    {
        if ($kategori->alatCamping()->exists()) {
            return redirect()->route('kategori.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih memiliki data alat camping.');
        }

        $kategori->delete();

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori alat berhasil dihapus.');
    }
}