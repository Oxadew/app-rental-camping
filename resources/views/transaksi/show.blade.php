@extends('layouts.app')

@section('title', 'Detail Invoice ' . $transaksi->kode_transaksi)
@section('page-title', 'Detail Transaksi #' . $transaksi->kode_transaksi)
@section('page-subtitle', 'Rincian penyewaan alat, status pengembalian, dan rekap biaya')

@section('page-actions')
    <div class="d-flex gap-2">
        @if ($transaksi->status !== 'selesai')
            <a href="{{ route('transaksi.pengembalian.form', $transaksi->id) }}" class="btn btn-success">
                <i class="bi bi-box-arrow-in-left me-1"></i> Proses Pengembalian
            </a>
        @endif
        <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">Informasi Penyewa & Waktu</div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            @if ($transaksi->status === 'selesai')
                                <span class="badge bg-success">Selesai</span>
                            @else
                                <span class="badge bg-warning text-dark">Sedang Berjalan</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Penyewa</td>
                        <td class="fw-semibold">{{ $transaksi->penyewa->nama_lengkap }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">NIK</td>
                        <td class="font-monospace">{{ $transaksi->penyewa->nik }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">No. HP</td>
                        <td>{{ $transaksi->penyewa->no_hp }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tgl Sewa</td>
                        <td>{{ $transaksi->tanggal_sewa->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jatuh Tempo</td>
                        <td>{{ $transaksi->tanggal_rencana_kembali->format('d M Y') }} ({{ $transaksi->lama_sewa_hari }} hari)</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tgl Kembali</td>
                        <td>{{ $transaksi->tanggal_aktual_kembali ? $transaksi->tanggal_aktual_kembali->format('d M Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Petugas</td>
                        <td>{{ $transaksi->petugas->name ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">Rincian Unit Alat Disewa</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No. Seri</th>
                                <th>Nama Alat</th>
                                <th>Tarif/Hari</th>
                                <th>Subtotal ({{ $transaksi->lama_sewa_hari }} hr)</th>
                                <th>Kondisi Kembali</th>
                                <th>Denda Rusak</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transaksi->detailSewa as $detail)
                                <tr>
                                    <td><span class="badge bg-dark font-monospace">{{ $detail->unitAlat->nomor_seri }}</span></td>
                                    <td>{{ $detail->unitAlat->alatCamping->nama_alat }}</td>
                                    <td>Rp {{ number_format($detail->harga_satuan_per_hari, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                    <td>
                                        @if (!$detail->kondisi_kembali)
                                            <span class="text-muted small">Belum kembali</span>
                                        @elseif ($detail->kondisi_kembali === 'baik')
                                            <span class="badge bg-success">Baik</span>
                                        @else
                                            <span class="badge bg-danger">Rusak</span>
                                        @endif
                                    </td>
                                    <td>Rp {{ number_format($detail->biaya_kerusakan_unit, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-end">Total Biaya Sewa</th>
                                <th>Rp {{ number_format($transaksi->total_biaya_sewa, 0, ',', '.') }}</th>
                            </tr>
                            <tr>
                                <th colspan="5" class="text-end text-danger">Denda Keterlambatan</th>
                                <th class="text-danger">Rp {{ number_format($transaksi->denda_keterlambatan, 0, ',', '.') }}</th>
                            </tr>
                            <tr>
                                <th colspan="5" class="text-end text-danger">Total Denda Kerusakan</th>
                                <th class="text-danger">Rp {{ number_format($transaksi->denda_kerusakan, 0, ',', '.') }}</th>
                            </tr>
                            <tr class="table-success">
                                <th colspan="5" class="text-end fs-6">GRAND TOTAL</th>
                                <th class="fs-6">Rp {{ number_format($transaksi->grand_total, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection