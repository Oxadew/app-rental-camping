<?php

namespace App\Http\Controllers;

use App\Models\AlatCamping;
use App\Models\UnitAlat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnitAlatController extends Controller
{
    public function index(Request $request)
    {
        $query = UnitAlat::with('alatCamping.kategori');

        if ($request->filled('search')) {
            $query->where('nomor_seri', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('alat_camping_id')) {
            $query->where('alat_camping_id', $request->alat_camping_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $units = $query->latest()->paginate(10)->withQueryString();
        $alats = AlatCamping::orderBy('nama_alat')->get();

        return view('unit-alat.index', compact('units', 'alats'));
    }

    public function create()
    {
        $alats = AlatCamping::orderBy('nama_alat')->get();
        return view('unit-alat.create', compact('alats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'alat_camping_id' => 'required|exists:alat_camping,id',
            'nomor_seri'      => 'required|string|max:50|unique:unit_alat,nomor_seri',
            'status'          => 'required|in:tersedia,rusak',
            'catatan_kondisi' => 'nullable|string|max:255',
        ], [
            'alat_camping_id.required' => 'Jenis alat camping wajib dipilih.',
            'nomor_seri.unique'        => 'Nomor seri unit sudah terdaftar.',
        ]);

        DB::transaction(function () use ($validated) {
            $unit = UnitAlat::create($validated);
            $this->syncStokAlat($unit->alat_camping_id);
        });

        return redirect()->route('unit-alat.index')
            ->with('success', 'Unit fisik berhasil ditambahkan dan stok katalog otomatis diperbarui.');
    }

    public function edit(UnitAlat $unitAlat)
    {
        $alats = AlatCamping::orderBy('nama_alat')->get();
        return view('unit-alat.edit', compact('unitAlat', 'alats'));
    }

    public function update(Request $request, UnitAlat $unitAlat)
    {
        if ($unitAlat->status === 'disewa') {
            return redirect()->route('unit-alat.index')
                ->with('error', 'Unit yang sedang disewa tidak dapat diubah datanya hingga transaksi pengembalian selesai.');
        }

        $validated = $request->validate([
            'alat_camping_id' => 'required|exists:alat_camping,id',
            'nomor_seri'      => 'required|string|max:50|unique:unit_alat,nomor_seri,' . $unitAlat->id,
            'status'          => 'required|in:tersedia,rusak',
            'catatan_kondisi' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($unitAlat, $validated) {
            $oldAlatId = $unitAlat->alat_camping_id;
            $unitAlat->update($validated);

            // Sinkronkan stok alat lama dan alat baru (jika jenis alat dipindah)
            $this->syncStokAlat($oldAlatId);
            if ($oldAlatId != $unitAlat->alat_camping_id) {
                $this->syncStokAlat($unitAlat->alat_camping_id);
            }
        });

        return redirect()->route('unit-alat.index')
            ->with('success', 'Data unit fisik berhasil diperbarui dan stok telah disinkronkan.');
    }

    public function destroy(UnitAlat $unitAlat)
    {
        if ($unitAlat->status === 'disewa') {
            return redirect()->route('unit-alat.index')
                ->with('error', 'Unit sedang disewa, tidak dapat dihapus.');
        }

        if ($unitAlat->detailSewa()->exists()) {
            return redirect()->route('unit-alat.index')
                ->with('error', 'Unit memiliki riwayat transaksi sewa. Ubah status menjadi "rusak" jika sudah tidak layak pakai.');
        }

        DB::transaction(function () use ($unitAlat) {
            $alatId = $unitAlat->alat_camping_id;
            $unitAlat->delete();
            $this->syncStokAlat($alatId);
        });

        return redirect()->route('unit-alat.index')
            ->with('success', 'Unit fisik berhasil dihapus dan stok katalog telah disesuaikan.');
    }

    /**
     * Sinkronisasi otomatis stok_total dan stok_tersedia pada tabel alat_camping
     */
    private function syncStokAlat(int $alatCampingId): void
    {
        $alat = AlatCamping::findOrFail($alatCampingId);

        $stokTotal = UnitAlat::where('alat_camping_id', $alatCampingId)->count();
        $stokTersedia = UnitAlat::where('alat_camping_id', $alatCampingId)
            ->where('status', 'tersedia')
            ->count();

        $alat->update([
            'stok_total'    => $stokTotal,
            'stok_tersedia' => $stokTersedia,
        ]);
    }
}