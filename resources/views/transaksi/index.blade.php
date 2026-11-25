@extends('layouts.app')

@section('title', 'Transaksi Sewa')
@section('page-title', 'Daftar Transaksi Penyewaan')
@section('page-subtitle', 'Kelola penyewaan aktif, pemantauan jatuh tempo, dan proses pengembalian')

@section('page-actions')
    <a href="{{ route('transaksi.create') }}" class="btn btn-success">
        <i class="bi bi-cart-plus me-1"></i> Buat Transaksi Sewa
    </a>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('transaksi.index') }}" method="GET" class="row g-2 mb-4">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Cari kode invoice atau nama penyewa..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">-- Semua Status --</option>
                    <option value="berjalan" {{ request('status') == 'berjalan' ? 'selected' : '' }}>Sedang Berjalan</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('transaksi.index') }}" class="btn btn-light border">Reset</a>
                @endif
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Kode Invoice</th>
                        <th>Penyewa</th>
                        <th>Tgl Sewa</th>
                        <th>Rencana Kembali</th>
                        <th class="text-center">Unit</th>
                        <th>Grand Total</th>
                        <th class="text-center">Status</th>
                        <th width="180" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transaksis as $trx)
                        @php
                            $isOverdue = $trx->status === 'berjalan' && now()->startOfDay()->gt($trx->tanggal_rencana_kembali);
                        @endphp
                        <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                            <td class="fw-bold font-monospace">{{ $trx->kode_transaksi }}</td>
                            <td>
                                <div class="fw-semibold">{{ $trx->penyewa->nama_lengkap }}</div>
                                <small class="text-muted">{{ $trx->penyewa->no_hp }}</small>
                            </td>
                            <td>{{ $trx->tanggal_sewa->format('d/m/Y') }}</td>
                            <td>
                                {{ $trx->tanggal_rencana_kembali->format('d/m/Y') }}
                                @if ($isOverdue)
                                    <span class="badge bg-danger d-block mt-1">
                                        Telat {{ $trx->tanggal_rencana_kembali->diffInDays(now()->startOfDay()) }} hari
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ $trx->detail_sewa_count }} unit</span>
                            </td>
                            <td class="fw-semibold">Rp {{ number_format($trx->grand_total, 0, ',', '.') }}</td>
                            <td class="text-center">
                                @if ($trx->status === 'selesai')
                                    <span class="badge bg-success">Selesai</span>
                                @else
                                    <span class="badge bg-warning text-dark">Berjalan</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('transaksi.show', $trx->id) }}" class="btn btn-sm btn-info text-white" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if ($trx->status !== 'selesai')
                                    <a href="{{ route('transaksi.pengembalian.form', $trx->id) }}" class="btn btn-sm btn-success" title="Proses Pengembalian">
                                        <i class="bi bi-box-arrow-in-left me-1"></i> Kembali
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Belum ada data transaksi penyewaan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $transaksis->links() }}
        </div>
    </div>
</div>
@endsection