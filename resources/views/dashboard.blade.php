@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Operasional')
@section('page-subtitle', 'Ringkasan ketersediaan alat camping, pendapatan, dan pemantauan jatuh tempo')

@section('page-actions')
    <a href="{{ route('transaksi.create') }}" class="btn btn-success">
        <i class="bi bi-cart-plus me-1"></i> Transaksi Sewa Baru
    </a>
@endsection

@section('content')
<!-- Kartu Statistik Utama -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold">Unit Siap Sewa</span>
                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                </div>
                <h3 class="fw-bold text-success mb-0">{{ $unitTersedia }} <small class="fs-6 fw-normal text-muted">unit</small></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold">Sedang Disewa</span>
                    <i class="bi bi-backpack2-fill text-primary fs-5"></i>
                </div>
                <h3 class="fw-bold text-primary mb-0">{{ $unitDisewa }} <small class="fs-6 fw-normal text-muted">unit</small></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold">Transaksi Terlambat</span>
                    <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                </div>
                <h3 class="fw-bold text-danger mb-0">{{ $jumlahTerlambat }} <small class="fs-6 fw-normal text-muted">invoice</small></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold">Pendapatan Bulan Ini</span>
                    <i class="bi bi-cash-stack text-dark fs-5"></i>
                </div>
                <h4 class="fw-bold text-dark mb-0">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
</div>

<!-- Info Tambahan Unit Rusak & Total Penyewa -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="alert alert-light border shadow-sm d-flex justify-content-between align-items-center mb-0">
            <div>
                <i class="bi bi-wrench-adjustable text-warning me-2"></i>
                <strong>Unit Maintenance / Rusak:</strong> {{ $unitRusak }} unit sedang tidak disewakan
            </div>
            <a href="{{ route('unit-alat.index', ['status' => 'rusak']) }}" class="btn btn-sm btn-outline-secondary">Cek Unit</a>
        </div>
    </div>
    <div class="col-md-6">
        <div class="alert alert-light border shadow-sm d-flex justify-content-between align-items-center mb-0">
            <div>
                <i class="bi bi-people-fill text-info me-2"></i>
                <strong>Total Pelanggan Terdaftar:</strong> {{ $totalPenyewa }} penyewa
            </div>
            <a href="{{ route('penyewa.index') }}" class="btn btn-sm btn-outline-secondary">Lihat Data</a>
        </div>
    </div>
</div>

<!-- Tabel Peringatan Jatuh Tempo Hari Ini & Terlambat -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <span class="fw-bold text-danger">
            <i class="bi bi-clock-history me-1"></i> Peringatan Pengembalian (Jatuh Tempo Hari Ini & Terlambat)
        </span>
        <span class="badge bg-danger">{{ $transaksiJatuhTempo->count() }} Transaksi</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kode Invoice</th>
                        <th>Penyewa & Kontak</th>
                        <th>Alat Disewa</th>
                        <th>Jatuh Tempo</th>
                        <th>Keterangan Waktu</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transaksiJatuhTempo as $trx)
                        @php
                            $today = \Carbon\Carbon::today();
                            $isLate = $trx->tanggal_rencana_kembali->lt($today);
                            $hariTelat = $isLate ? $trx->tanggal_rencana_kembali->diffInDays($today) : 0;
                        @endphp
                        <tr class="{{ $isLate ? 'table-danger' : 'table-warning' }}">
                            <td class="fw-bold font-monospace">{{ $trx->kode_transaksi }}</td>
                            <td>
                                <div class="fw-semibold">{{ $trx->penyewa->nama_lengkap }}</div>
                                <small class="text-muted"><i class="bi bi-whatsapp"></i> {{ $trx->penyewa->no_hp }}</small>
                            </td>
                            <td>
                                @foreach ($trx->detailSewa as $detail)
                                    <span class="badge bg-dark font-monospace">{{ $detail->unitAlat->nomor_seri }}</span>
                                @endforeach
                            </td>
                            <td>{{ $trx->tanggal_rencana_kembali->format('d M Y') }}</td>
                            <td>
                                @if ($isLate)
                                    <span class="badge bg-danger">Terlambat {{ $hariTelat }} Hari</span>
                                @else
                                    <span class="badge bg-warning text-dark">Jatuh Tempo Hari Ini</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('transaksi.pengembalian.form', $trx->id) }}" class="btn btn-sm btn-success">
                                    <i class="bi bi-box-arrow-in-left me-1"></i> Proses Kembali
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-emoji-smile text-success fs-4 d-block mb-1"></i>
                                Tidak ada transaksi yang jatuh tempo hari ini atau terlambat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection