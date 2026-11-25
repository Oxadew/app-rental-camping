@extends('layouts.app')

@section('title', 'Laporan Transaksi & Denda')
@section('page-title', 'Laporan Pendapatan & Denda')
@section('page-subtitle', 'Rekapitulasi transaksi penyewaan berdasarkan rentang tanggal')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('laporan.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i> Tampilkan Laporan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Kartu Ringkasan -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Total Transaksi</div>
                <h4 class="fw-bold mb-0">{{ $ringkasan['total_transaksi'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Pendapatan Sewa Pokok</div>
                <h4 class="fw-bold text-primary mb-0">Rp {{ number_format($ringkasan['pendapatan_sewa'], 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Pendapatan Denda</div>
                <h4 class="fw-bold text-danger mb-0">Rp {{ number_format($ringkasan['pendapatan_denda'], 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body">
                <div class="small opacity-75">Total Pendapatan Bersih</div>
                <h4 class="fw-bold mb-0">Rp {{ number_format($ringkasan['total_keseluruhan'], 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Detail Laporan -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Kode Invoice</th>
                        <th>Tanggal Sewa</th>
                        <th>Penyewa</th>
                        <th>Biaya Sewa</th>
                        <th>Denda Telat</th>
                        <th>Denda Rusak</th>
                        <th>Grand Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transaksis as $trx)
                        <tr>
                            <td class="font-monospace">{{ $trx->kode_transaksi }}</td>
                            <td>{{ $trx->tanggal_sewa->format('d/m/Y') }}</td>
                            <td>{{ $trx->penyewa->nama_lengkap }}</td>
                            <td>Rp {{ number_format($trx->total_biaya_sewa, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($trx->denda_keterlambatan, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($trx->denda_kerusakan, 0, ',', '.') }}</td>
                            <td class="fw-bold">Rp {{ number_format($trx->grand_total, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge {{ $trx->status === 'selesai' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ ucfirst($trx->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Tidak ada transaksi pada periode tanggal tersebut.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection