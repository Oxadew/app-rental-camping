<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AlatCamping;
use App\Models\DetailSewa;
use App\Models\TransaksiSewa;
use App\Models\UnitAlat;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RentalApiController extends Controller
{
    /**
     * GET /api/alat
     * Menampilkan katalog alat camping beserta unit yang tersedia
     */
    public function indexAlat(Request $request)
    {
        $query = AlatCamping::with(['kategori', 'unitAlat' => function ($q) {
            $q->where('status', 'tersedia');
        }]);

        if ($request->filled('search')) {
            $query->where('nama_alat', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_alat_id', $request->kategori_id);
        }

        $alats = $query->paginate(10);

        return response()->json([
            'status'  => 'success',
            'message' => 'Daftar katalog alat camping berhasil diambil.',
            'data'    => $alats,
        ], 200);
    }

    /**
     * GET /api/alat/{id}
     * Detail 1 alat camping beserta seluruh unit fisiknya
     */
    public function showAlat($id)
    {
        $alat = AlatCamping::with(['kategori', 'unitAlat'])->find($id);

        if (!$alat) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data alat camping tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Detail alat camping ditemukan.',
            'data'    => $alat,
        ], 200);
    }

    /**
     * GET /api/transaksi
     * Menampilkan daftar transaksi penyewaan
     */
    public function indexTransaksi()
    {
        $transaksis = TransaksiSewa::with(['penyewa', 'detailSewa.unitAlat.alatCamping'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'status'  => 'success',
            'message' => 'Daftar transaksi penyewaan berhasil diambil.',
            'data'    => $transaksis,
        ], 200);
    }

    /**
     * POST /api/transaksi
     * Membuat transaksi sewa baru via API (otomatis potong stok & ubah status unit)
     */
    public function storeTransaksi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'penyewa_id'              => 'required|exists:penyewa,id',
            'tanggal_sewa'            => 'required|date',
            'tanggal_rencana_kembali' => 'required|date|after:tanggal_sewa',
            'unit_alat_ids'           => 'required|array|min:1',
            'unit_alat_ids.*'         => 'exists:unit_alat,id',
            'catatan'                 => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        try {
            $transaksi = DB::transaction(function () use ($validated) {
                $tglSewa = Carbon::parse($validated['tanggal_sewa']);
                $tglRencanaKembali = Carbon::parse($validated['tanggal_rencana_kembali']);
                $lamaSewaHari = max(1, $tglSewa->diffInDays($tglRencanaKembali));

                $units = UnitAlat::with('alatCamping')
                    ->whereIn('id', $validated['unit_alat_ids'])
                    ->lockForUpdate()
                    ->get();

                foreach ($units as $unit) {
                    if ($unit->status !== 'tersedia') {
                        throw new \Exception("Unit {$unit->nomor_seri} sedang tidak tersedia.");
                    }
                }

                $kodeTransaksi = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(4));
                $petugasId = User::first()->id;

                $transaksi = TransaksiSewa::create([
                    'kode_transaksi'          => $kodeTransaksi,
                    'penyewa_id'              => $validated['penyewa_id'],
                    'user_id'                 => $petugasId,
                    'tanggal_sewa'            => $tglSewa->toDateString(),
                    'tanggal_rencana_kembali' => $tglRencanaKembali->toDateString(),
                    'lama_sewa_hari'          => $lamaSewaHari,
                    'status'                  => 'berjalan',
                    'catatan'                 => $validated['catatan'] ?? 'Dibuat via REST API',
                ]);

                $totalBiayaSewa = 0;
                $affectedAlatIds = [];

                foreach ($units as $unit) {
                    $hargaHarian = $unit->alatCamping->harga_sewa_per_hari;
                    $subtotal = $hargaHarian * $lamaSewaHari;
                    $totalBiayaSewa += $subtotal;

                    DetailSewa::create([
                        'transaksi_sewa_id'     => $transaksi->id,
                        'unit_alat_id'          => $unit->id,
                        'harga_satuan_per_hari' => $hargaHarian,
                        'subtotal'              => $subtotal,
                    ]);

                    $unit->update(['status' => 'disewa']);
                    $affectedAlatIds[$unit->alat_camping_id] = true;
                }

                $transaksi->update([
                    'total_biaya_sewa' => $totalBiayaSewa,
                    'grand_total'      => $totalBiayaSewa,
                ]);

                foreach (array_keys($affectedAlatIds) as $alatId) {
                    $stokTersedia = UnitAlat::where('alat_camping_id', $alatId)
                        ->where('status', 'tersedia')
                        ->count();
                    AlatCamping::where('id', $alatId)->update(['stok_tersedia' => $stokTersedia]);
                }

                return $transaksi->load(['penyewa', 'detailSewa.unitAlat.alatCamping']);
            });

            return response()->json([
                'status'  => 'success',
                'message' => 'Transaksi penyewaan berhasil dibuat.',
                'data'    => $transaksi,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}