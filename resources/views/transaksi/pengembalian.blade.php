@extends('layouts.app')

@section('title', 'Pengembalian Alat')
@section('page-title', 'Proses Pengembalian Alat #' . $transaksi->kode_transaksi)
@section('page-subtitle', 'Inspeksi kondisi fisik setiap unit dan kalkulasi denda keterlambatan')

@section('page-actions')
    <a href="{{ route('transaksi.show', $transaksi->id) }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
@endsection

@section('content')
<form action="{{ route('transaksi.pengembalian.proses', $transaksi->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-muted small mb-1">Penyewa</label>
                    <div class="fw-bold">{{ $transaksi->penyewa->nama_lengkap }}</div>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small mb-1">Tanggal Jatuh Tempo</label>
                    <div class="fw-bold text-primary">{{ $transaksi->tanggal_rencana_kembali->format('d F Y') }}</div>
                </div>
                <div class="col-md-3">
                    <label for="tanggal_aktual_kembali" class="form-label fw-semibold">Tanggal Aktual Kembali <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_aktual_kembali" id="tanggal_aktual_kembali"
                           class="form-control @error('tanggal_aktual_kembali') is-invalid @enderror"
                           value="{{ old('tanggal_aktual_kembali', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-3">
                    <div class="alert alert-warning mb-0 py-2">
                        <small class="d-block">Estimasi Denda Telat:</small>
                        <strong id="estimasi-denda-telat">Rp 0</strong>
                        <small class="text-muted d-block">(Rp {{ number_format($tarifDenda, 0, ',', '.') }}/unit/hari)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-bold py-3">Inspeksi Kondisi Unit Fisik</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No. Seri</th>
                            <th>Nama Alat</th>
                            <th width="180">Kondisi Kembali</th>
                            <th width="200">Biaya Ganti Rugi (Rp)</th>
                            <th>Catatan Kondisi Baru</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transaksi->detailSewa as $detail)
                            <tr>
                                <td><span class="badge bg-dark font-monospace">{{ $detail->unitAlat->nomor_seri }}</span></td>
                                <td class="fw-semibold">{{ $detail->unitAlat->alatCamping->nama_alat }}</td>
                                <td>
                                    <select name="kondisi[{{ $detail->id }}]" class="form-select kondisi-select" data-target="biaya-{{ $detail->id }}" required>
                                        <option value="baik">Baik (Kembali ke Stok)</option>
                                        <option value="rusak">Rusak (Masuk Maintenance)</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="biaya_kerusakan[{{ $detail->id }}]" id="biaya-{{ $detail->id }}"
                                           class="form-control" value="0" min="0" step="1000" readonly>
                                </td>
                                <td>
                                    <input type="text" name="catatan_unit[{{ $detail->id }}]" class="form-control"
                                           placeholder="Opsional (misal: frame patah, robek)">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                <label for="catatan" class="form-label fw-semibold">Catatan Pengembalian</label>
                <textarea name="catatan" id="catatan" rows="2" class="form-control" placeholder="Keterangan tambahan saat serah terima barang">{{ old('catatan', $transaksi->catatan) }}</textarea>
            </div>

            <div class="mt-4 d-flex justify-content-end gap-2">
                <a href="{{ route('transaksi.show', $transaksi->id) }}" class="btn btn-light border">Batal</a>
                <button type="submit" class="btn btn-success px-4">
                    <i class="bi bi-check2-all me-1"></i> Selesaikan Pengembalian
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tglRencana = new Date("{{ $transaksi->tanggal_rencana_kembali->toDateString() }}");
        const tglAktualInput = document.getElementById('tanggal_aktual_kembali');
        const jumlahUnit = {{ $transaksi->detailSewa->count() }};
        const tarifDenda = {{ $tarifDenda }};

        function updateDendaTelat() {
            const tglAktual = new Date(tglAktualInput.value);
            const diffTime = tglAktual - tglRencana;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            const hariTelat = diffDays > 0 ? diffDays : 0;
            const totalDenda = hariTelat * jumlahUnit * tarifDenda;

            document.getElementById('estimasi-denda-telat').innerText =
                'Rp ' + totalDenda.toLocaleString('id-ID') + (hariTelat > 0 ? ` (${hariTelat} hari)` : '');
        }

        tglAktualInput.addEventListener('change', updateDendaTelat);
        updateDendaTelat();

        // Aktifkan input biaya kerusakan hanya jika kondisi dipilih "rusak"
        document.querySelectorAll('.kondisi-select').forEach(select => {
            select.addEventListener('change', function () {
                const targetInput = document.getElementById(this.dataset.target);
                if (this.value === 'rusak') {
                    targetInput.removeAttribute('readonly');
                    targetInput.focus();
                } else {
                    targetInput.value = 0;
                    targetInput.setAttribute('readonly', true);
                }
            });
        });
    });
</script>
@endpush