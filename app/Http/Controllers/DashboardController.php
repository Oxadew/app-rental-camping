<?php

namespace App\Http\Controllers;

use App\Models\Penyewa;
use App\Models\TransaksiSewa;
use App\Models\UnitAlat;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Statistik Unit Fisik
        $unitTersedia = UnitAlat::where('status', 'tersedia')->count();
        $unitDisewa   = UnitAlat::where('status', 'disewa')->count();
        $unitRusak    = UnitAlat::where('status', 'rusak')->count();
        $totalPenyewa = Penyewa::count();

        // Transaksi berjalan yang jatuh tempo hari ini atau sudah lewat jatuh tempo (terlambat)
        $transaksiJatuhTempo = TransaksiSewa::with(['penyewa', 'detailSewa.unitAlat.alatCamping'])
            ->where('status', 'berjalan')
            ->whereDate('tanggal_rencana_kembali', '<=', $today)
            ->orderBy('tanggal_rencana_kembali', 'asc')
            ->get();

        // Hitung jumlah transaksi terlambat (lewat dari hari ini)
        $jumlahTerlambat = $transaksiJatuhTempo->filter(function ($trx) use ($today) {
            return $trx->tanggal_rencana_kembali->lt($today);
        })->count();

        // Pendapatan bulan berjalan (dari transaksi yang sudah selesai)
        $pendapatanBulanIni = TransaksiSewa::where('status', 'selesai')
            ->whereMonth('tanggal_aktual_kembali', $today->month)
            ->whereYear('tanggal_aktual_kembali', $today->year)
            ->sum('grand_total');

        return view('dashboard', compact(
            'unitTersedia',
            'unitDisewa',
            'unitRusak',
            'totalPenyewa',
            'transaksiJatuhTempo',
            'jumlahTerlambat',
            'pendapatanBulanIni'
        ));
    }
}