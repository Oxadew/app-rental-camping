@extends('layouts.app')

@section('title', 'Transaksi Sewa Baru')
@section('page-title', 'Buat Transaksi Penyewaan')
@section('page-subtitle', 'Pilih penyewa, durasi sewa, dan unit fisik yang tersedia')

@section('page-actions')
    <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
@endsection

@section('content')
<form action="{{ route('transaksi.store') }}" method="POST">
    @csrf
    <div class="row g-4">
        <!-- Kolom Kiri: Data Penyewa & Jadwal -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold py-3">1. Informasi Penyewa & Jadwal</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="penyewa_id" class="form-label fw-semibold">Penyewa <span class="text-danger">*</span></label>
                        <select name="penyewa_id" id="penyewa_id" class="form-select @error('penyewa_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Penyewa --</option>
                            @foreach ($penyewas as $p)
                                <option value="{{ $p->id }}" {{ old('penyewa_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_lengkap }} ({{ $p->nik }})
                                </option>
                            @endforeach
                        </select>
                        @error('penyewa_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label for="tanggal_sewa" class="form-label fw-semibold">Tanggal Sewa <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_sewa" id="tanggal_sewa"
                                   class="form-control @error('tanggal_sewa') is-invalid @enderror"
                                   value="{{ old('tanggal_sewa', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="tanggal_rencana_kembali" class="form-label fw-semibold">Rencana Kembali <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_rencana_kembali" id="tanggal_rencana_kembali"
                                   class="form-control @error('tanggal_rencana_kembali') is-invalid @enderror"
                                   value="{{ old('tanggal_rencana_kembali', date('Y-m-d', strtotime('+2 days'))) }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="catatan" class="form-label fw-semibold">Catatan Transaksi</label>
                        <textarea name="catatan" id="catatan" rows="2" class="form-control" placeholder="Jaminan KTP asli ditahan, dll.">{{ old('catatan') }}</textarea>
                    </div>

                    <!-- Box Estimasi Biaya Real-time -->
                    <div class="alert alert-light border mb-0">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Durasi Sewa:</span>
                            <span class="fw-bold" id="display-durasi">2 Hari</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Unit Dipilih:</span>
                            <span class="fw-bold" id="display-jumlah-unit">0 Unit</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Estimasi Total:</span>
                            <span class="fs-5 fw-bold text-success" id="display-total">Rp 0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Pilih Unit Tersedia -->
        <div class="col-md-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
                    <span>2. Pilih Unit Fisik Tersedia</span>
                    <span class="badge bg-success">{{ $unitsTersedia->count() }} unit siap sewa</span>
                </div>
                <div class="card-body">
                    @error('unit_alat_ids')
                        <div class="alert alert-danger py-2">{{ $message }}</div>
                    @enderror

                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover align-middle">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th width="40">Pilih</th>
                                    <th>No. Seri</th>
                                    <th>Nama Alat</th>
                                    <th>Tarif / Hari</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($unitsTersedia as $unit)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="unit_alat_ids[]" value="{{ $unit->id }}"
                                                   class="form-check-input unit-checkbox"
                                                   data-harga="{{ $unit->alatCamping->harga_sewa_per_hari }}"
                                                   {{ in_array($unit->id, old('unit_alat_ids', [])) ? 'checked' : '' }}>
                                        </td>
                                        <td><span class="badge bg-dark font-monospace">{{ $unit->nomor_seri }}</span></td>
                                        <td>
                                            <div class="fw-semibold">{{ $unit->alatCamping->nama_alat }}</div>
                                            <small class="text-muted">{{ $unit->catatan_kondisi ?? 'Kondisi baik' }}</small>
                                        </td>
                                        <td class="text-success fw-semibold">
                                            Rp {{ number_format($unit->alatCamping->harga_sewa_per_hari, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Tidak ada unit alat yang tersedia saat ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <a href="{{ route('transaksi.index') }}" class="btn btn-light border">Batal</a>
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-check-circle me-1"></i> Proses Transaksi Sewa
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tglSewaInput = document.getElementById('tanggal_sewa');
        const tglKembaliInput = document.getElementById('tanggal_rencana_kembali');
        const checkboxes = document.querySelectorAll('.unit-checkbox');

        function hitungEstimasi() {
            const start = new Date(tglSewaInput.value);
            const end = new Date(tglKembaliInput.value);
            let diffDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
            if (isNaN(diffDays) || diffDays < 1) diffDays = 1;

            let totalHarian = 0;
            let countUnit = 0;

            checkboxes.forEach(cb => {
                if (cb.checked) {
                    totalHarian += parseFloat(cb.dataset.harga);
                    countUnit++;
                }
            });

            const grandTotal = totalHarian * diffDays;

            document.getElementById('display-durasi').innerText = diffDays + ' Hari';
            document.getElementById('display-jumlah-unit').innerText = countUnit + ' Unit';
            document.getElementById('display-total').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');
        }

        tglSewaInput.addEventListener('change', hitungEstimasi);
        tglKembaliInput.addEventListener('change', hitungEstimasi);
        checkboxes.forEach(cb => cb.addEventListener('change', hitungEstimasi));

        hitungEstimasi();
    });
</script>
@endpush