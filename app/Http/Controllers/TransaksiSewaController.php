<?php

namespace App\Http\Controllers;

use App\Models\AlatCamping;
use App\Models\DetailSewa;
use App\Models\Penyewa;
use App\Models\TransaksiSewa;
use App\Models\UnitAlat;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransaksiSewaController extends Controller
{
    // Tarif denda keterlambatan per unit per hari
    private const TARIF_DENDA_PER_UNIT_PER_HARI = 15000;

    public function index(Request $request)
    {
        $query = TransaksiSewa::with(['penyewa', 'petugas'])
            ->withCount('detailSewa');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('kode_transaksi', 'like', '%' . $request->search . '%')
                  ->orWhereHas('penyewa', function ($qp) use ($request) {
                      $qp->where('nama_lengkap', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transaksis = $query->latest()->paginate(10)->withQueryString();

        return view('transaksi.index', compact('transaksis'));
    }

    public function create()
    {
        $penyewas = Penyewa::orderBy('nama_lengkap')->get();
        $unitsTersedia = UnitAlat::with('alatCamping.kategori')
            ->where('status', 'tersedia')
            ->orderBy('nomor_seri')
            ->get();

        return view('transaksi.create', compact('penyewas', 'unitsTersedia'));
    }

    /**
     * PROSES PENYEWAAN (TRANSAKSI UTAMA 1)
     * Efek samping: Insert header & detail, ubah status unit -> 'disewa', kurangi stok_tersedia alat_camping
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'penyewa_id'              => 'required|exists:penyewa,id',
            'tanggal_sewa'            => 'required|date',
            'tanggal_rencana_kembali' => 'required|date|after:tanggal_sewa',
            'unit_alat_ids'           => 'required|array|min:1',
            'unit_alat_ids.*'         => 'exists:unit_alat,id',
            'catatan'                 => 'nullable|string|max:500',
        ], [
            'penyewa_id.required'           => 'Penyewa wajib dipilih.',
            'tanggal_rencana_kembali.after' => 'Tanggal rencana kembali harus minimal 1 hari setelah tanggal sewa.',
            'unit_alat_ids.required'        => 'Pilih minimal 1 unit alat camping untuk disewa.',
        ]);

        try {
            $transaksi = DB::transaction(function () use ($validated) {
                $tglSewa = Carbon::parse($validated['tanggal_sewa']);
                $tglRencanaKembali = Carbon::parse($validated['tanggal_rencana_kembali']);
                $lamaSewaHari = max(1, $tglSewa->diffInDays($tglRencanaKembali));

                // Kunci baris unit yang dipilih agar tidak disewa ganda secara bersamaan
                $units = UnitAlat::with('alatCamping')
                    ->whereIn('id', $validated['unit_alat_ids'])
                    ->lockForUpdate()
                    ->get();

                if ($units->count() !== count($validated['unit_alat_ids'])) {
                    throw new \Exception('Sebagian unit yang dipilih tidak ditemukan.');
                }

                // Pastikan seluruh unit benar-benar berstatus tersedia
                foreach ($units as $unit) {
                    if ($unit->status !== 'tersedia') {
                        throw new \Exception("Unit {$unit->nomor_seri} ({$unit->alatCamping->nama_alat}) sudah tidak tersedia.");
                    }
                }

                // Generate kode invoice unik
                $kodeTransaksi = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(4));
                $petugasId = Auth::id() ?? User::first()->id;

                // 1. Buat Header Transaksi
                $transaksi = TransaksiSewa::create([
                    'kode_transaksi'          => $kodeTransaksi,
                    'penyewa_id'              => $validated['penyewa_id'],
                    'user_id'                 => $petugasId,
                    'tanggal_sewa'            => $tglSewa->toDateString(),
                    'tanggal_rencana_kembali' => $tglRencanaKembali->toDateString(),
                    'lama_sewa_hari'          => $lamaSewaHari,
                    'status'                  => 'berjalan',
                    'catatan'                 => $validated['catatan'] ?? null,
                ]);

                $totalBiayaSewa = 0;
                $affectedAlatIds = [];

                // 2. Simpan Detail Sewa & Update Status Unit Fisik
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

                    // Efek Samping A: Ubah status unit menjadi disewa
                    $unit->update(['status' => 'disewa']);

                    $affectedAlatIds[$unit->alat_camping_id] = true;
                }

                // 3. Update Total Biaya Header
                $transaksi->update([
                    'total_biaya_sewa' => $totalBiayaSewa,
                    'grand_total'      => $totalBiayaSewa,
                ]);

                // Efek Samping B: Sinkronisasi ulang stok_tersedia pada tabel master alat_camping
                foreach (array_keys($affectedAlatIds) as $alatId) {
                    $this->syncStokTersedia($alatId);
                }

                return $transaksi;
            });

            return redirect()->route('transaksi.show', $transaksi->id)
                ->with('success', "Transaksi {$transaksi->kode_transaksi} berhasil dibuat. Status unit & stok katalog telah diperbarui.");

        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(TransaksiSewa $transaksi)
    {
        $transaksi->load(['penyewa', 'petugas', 'detailSewa.unitAlat.alatCamping']);
        return view('transaksi.show', compact('transaksi'));
    }

    public function formPengembalian(TransaksiSewa $transaksi)
    {
        if ($transaksi->status === 'selesai') {
            return redirect()->route('transaksi.show', $transaksi->id)
                ->with('error', 'Transaksi ini sudah diselesaikan sebelumnya.');
        }

        $transaksi->load(['penyewa', 'detailSewa.unitAlat.alatCamping']);
        $tarifDenda = self::TARIF_DENDA_PER_UNIT_PER_HARI;

        return view('transaksi.pengembalian', compact('transaksi', 'tarifDenda'));
    }

    /**
     * PROSES PENGEMBALIAN ALAT (TRANSAKSI UTAMA 2)
     * Efek samping: Hitung denda telat otomatis, cek kondisi tiap unit (baik -> tersedia, rusak -> rusak),
     *               akumulasi denda kerusakan, dan pulihkan stok_tersedia hanya untuk unit yang baik.
     */
    public function prosesPengembalian(Request $request, TransaksiSewa $transaksi)
    {
        if ($transaksi->status === 'selesai') {
            return redirect()->route('transaksi.show', $transaksi->id)
                ->with('error', 'Transaksi ini sudah diselesaikan.');
        }

        $validated = $request->validate([
            'tanggal_aktual_kembali'    => 'required|date|after_or_equal:' . $transaksi->tanggal_sewa->toDateString(),
            'kondisi'                   => 'required|array',
            'kondisi.*'                 => 'required|in:baik,rusak',
            'biaya_kerusakan'           => 'nullable|array',
            'biaya_kerusakan.*'         => 'nullable|numeric|min:0',
            'catatan_unit'              => 'nullable|array',
            'catatan_unit.*'            => 'nullable|string|max:255',
            'catatan'                   => 'nullable|string|max:500',
        ]);

        try {
            DB::transaction(function () use ($validated, $transaksi) {
                $transaksi->load('detailSewa.unitAlat');

                $tglRencana = Carbon::parse($transaksi->tanggal_rencana_kembali)->startOfDay();
                $tglAktual = Carbon::parse($validated['tanggal_aktual_kembali'])->startOfDay();

                // Hitung selisih hari keterlambatan
                $hariTerlambat = $tglAktual->greaterThan($tglRencana)
                    ? $tglRencana->diffInDays($tglAktual)
                    : 0;

                $jumlahUnit = $transaksi->detailSewa->count();
                $dendaKeterlambatan = $hariTerlambat * $jumlahUnit * self::TARIF_DENDA_PER_UNIT_PER_HARI;

                $totalDendaKerusakan = 0;
                $affectedAlatIds = [];

                foreach ($transaksi->detailSewa as $detail) {
                    $detailId = $detail->id;
                    $kondisi = $validated['kondisi'][$detailId] ?? 'baik';
                    $biayaRusak = ($kondisi === 'rusak')
                        ? (float) ($validated['biaya_kerusakan'][$detailId] ?? 0)
                        : 0;

                    $totalDendaKerusakan += $biayaRusak;

                    // 1. Update detail sewa
                    $detail->update([
                        'kondisi_kembali'      => $kondisi,
                        'biaya_kerusakan_unit' => $biayaRusak,
                    ]);

                    // 2. Efek Samping pada Unit Fisik: baik -> tersedia, rusak -> rusak
                    $unit = $detail->unitAlat;
                    $catatanBaru = $validated['catatan_unit'][$detailId] ?? null;

                    $unit->update([
                        'status'          => ($kondisi === 'baik') ? 'tersedia' : 'rusak',
                        'catatan_kondisi' => $catatanBaru ?: $unit->catatan_kondisi,
                    ]);

                    $affectedAlatIds[$unit->alat_camping_id] = true;
                }

                // 3. Finalisasi Header Transaksi
                $grandTotal = $transaksi->total_biaya_sewa + $dendaKeterlambatan + $totalDendaKerusakan;

                $transaksi->update([
                    'tanggal_aktual_kembali' => $tglAktual->toDateString(),
                    'denda_keterlambatan'    => $dendaKeterlambatan,
                    'denda_kerusakan'        => $totalDendaKerusakan,
                    'grand_total'            => $grandTotal,
                    'status'                 => 'selesai',
                    'catatan'                => $validated['catatan'] ?? $transaksi->catatan,
                ]);

                // 4. Efek Samping: Hitung ulang stok_tersedia (unit yang 'rusak' otomatis tidak dihitung sebagai stok tersedia)
                foreach (array_keys($affectedAlatIds) as $alatId) {
                    $this->syncStokTersedia($alatId);
                }
            });

            return redirect()->route('transaksi.show', $transaksi->id)
                ->with('success', 'Pengembalian berhasil diproses. Denda dan status unit fisik telah diperbarui.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function laporan(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        $transaksis = TransaksiSewa::with(['penyewa', 'detailSewa'])
            ->whereBetween('tanggal_sewa', [$startDate, $endDate])
            ->latest()
            ->get();

        $ringkasan = [
            'total_transaksi'   => $transaksis->count(),
            'pendapatan_sewa'   => $transaksis->sum('total_biaya_sewa'),
            'pendapatan_denda'  => $transaksis->sum('denda_keterlambatan') + $transaksis->sum('denda_kerusakan'),
            'total_keseluruhan' => $transaksis->sum('grand_total'),
        ];

        return view('transaksi.laporan', compact('transaksis', 'ringkasan', 'startDate', 'endDate'));
    }

    private function syncStokTersedia(int $alatCampingId): void
    {
        $stokTersedia = UnitAlat::where('alat_camping_id', $alatCampingId)
            ->where('status', 'tersedia')
            ->count();

        AlatCamping::where('id', $alatCampingId)->update([
            'stok_tersedia' => $stokTersedia,
        ]);
    }
}