<?php

namespace App\Http\Controllers;

use App\Models\Penyewa;
use Illuminate\Http\Request;

class PenyewaController extends Controller
{
    public function index(Request $request)
    {
        $query = Penyewa::withCount('transaksiSewa');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->search . '%')
                  ->orWhere('nik', 'like', '%' . $request->search . '%')
                  ->orWhere('no_hp', 'like', '%' . $request->search . '%');
            });
        }

        $penyewas = $query->latest()->paginate(10)->withQueryString();

        return view('penyewa.index', compact('penyewas'));
    }

    public function create()
    {
        return view('penyewa.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik'          => 'required|digits:16|unique:penyewa,nik',
            'nama_lengkap' => 'required|string|max:150',
            'no_hp'        => 'required|string|max:20',
            'alamat'       => 'required|string',
        ], [
            'nik.required' => 'NIK KTP wajib diisi sebagai jaminan identitas.',
            'nik.digits'   => 'NIK harus terdiri dari 16 digit angka.',
            'nik.unique'   => 'NIK sudah terdaftar pada sistem.',
        ]);

        Penyewa::create($validated);

        return redirect()->route('penyewa.index')
            ->with('success', 'Data penyewa berhasil ditambahkan.');
    }

    public function edit(Penyewa $penyewa)
    {
        return view('penyewa.edit', compact('penyewa'));
    }

    public function update(Request $request, Penyewa $penyewa)
    {
        $validated = $request->validate([
            'nik'          => 'required|digits:16|unique:penyewa,nik,' . $penyewa->id,
            'nama_lengkap' => 'required|string|max:150',
            'no_hp'        => 'required|string|max:20',
            'alamat'       => 'required|string',
        ], [
            'nik.digits' => 'NIK harus terdiri dari 16 digit angka.',
            'nik.unique' => 'NIK sudah digunakan oleh penyewa lain.',
        ]);

        $penyewa->update($validated);

        return redirect()->route('penyewa.index')
            ->with('success', 'Data penyewa berhasil diperbarui.');
    }

    public function destroy(Penyewa $penyewa)
    {
        if ($penyewa->transaksiSewa()->exists()) {
            return redirect()->route('penyewa.index')
                ->with('error', 'Data penyewa tidak dapat dihapus karena memiliki riwayat transaksi sewa.');
        }

        $penyewa->delete();

        return redirect()->route('penyewa.index')
            ->with('success', 'Data penyewa berhasil dihapus.');
    }
}