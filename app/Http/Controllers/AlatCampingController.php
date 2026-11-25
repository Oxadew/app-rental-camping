<?php

namespace App\Http\Controllers;

use App\Models\AlatCamping;
use App\Models\KategoriAlat;
use Illuminate\Http\Request;

class AlatCampingController extends Controller
{
    public function index(Request $request)
    {
        $query = AlatCamping::with('kategori');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_alat', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_alat', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_alat_id', $request->kategori_id);
        }

        $alats = $query->latest()->paginate(10)->withQueryString();
        $kategoris = KategoriAlat::orderBy('nama_kategori')->get();

        return view('alat.index', compact('alats', 'kategoris'));
    }

    public function create()
    {
        $kategoris = KategoriAlat::orderBy('nama_kategori')->get();
        return view('alat.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_alat_id'    => 'required|exists:kategori_alat,id',
            'kode_alat'           => 'required|string|max:20|unique:alat_camping,kode_alat',
            'nama_alat'           => 'required|string|max:150',
            'harga_sewa_per_hari' => 'required|numeric|min:1000',
            'deskripsi'           => 'nullable|string',
        ], [
            'kategori_alat_id.required' => 'Kategori alat wajib dipilih.',
            'kode_alat.unique'          => 'Kode alat sudah digunakan.',
            'harga_sewa_per_hari.min'   => 'Harga sewa minimal Rp 1.000.',
        ]);

        // Stok awal 0 karena unit fisik didaftarkan pada menu Unit Fisik & Seri
        $validated['stok_total'] = 0;
        $validated['stok_tersedia'] = 0;

        AlatCamping::create($validated);

        return redirect()->route('alat.index')
            ->with('success', 'Katalog alat camping berhasil ditambahkan. Silakan daftarkan unit fisiknya.');
    }

    public function edit(AlatCamping $alat)
    {
        $kategoris = KategoriAlat::orderBy('nama_kategori')->get();
        return view('alat.edit', compact('alat', 'kategoris'));
    }

    public function update(Request $request, AlatCamping $alat)
    {
        $validated = $request->validate([
            'kategori_alat_id'    => 'required|exists:kategori_alat,id',
            'kode_alat'           => 'required|string|max:20|unique:alat_camping,kode_alat,' . $alat->id,
            'nama_alat'           => 'required|string|max:150',
            'harga_sewa_per_hari' => 'required|numeric|min:1000',
            'deskripsi'           => 'nullable|string',
        ]);

        $alat->update($validated);

        return redirect()->route('alat.index')
            ->with('success', 'Data alat camping berhasil diperbarui.');
    }

    public function destroy(AlatCamping $alat)
    {
        if ($alat->unitAlat()->exists()) {
            return redirect()->route('alat.index')
                ->with('error', 'Alat tidak dapat dihapus karena masih memiliki unit fisik terdaftar.');
        }

        $alat->delete();

        return redirect()->route('alat.index')
            ->with('success', 'Katalog alat berhasil dihapus.');
    }
}