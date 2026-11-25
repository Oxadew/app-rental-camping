@extends('layouts.app')

@section('title', 'Edit Unit Fisik')
@section('page-title', 'Edit Unit Fisik Alat')
@section('page-subtitle', 'Perbarui nomor seri atau status kondisi unit ' . $unitAlat->nomor_seri)

@section('page-actions')
    <a href="{{ route('unit-alat.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
@endsection

@section('content')
<div class="card border-0 shadow-sm col-md-8">
    <div class="card-body p-4">
        <form action="{{ route('unit-alat.update', $unitAlat->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="alat_camping_id" class="form-label fw-semibold">Jenis Alat Camping <span class="text-danger">*</span></label>
                <select name="alat_camping_id" id="alat_camping_id" class="form-select @error('alat_camping_id') is-invalid @enderror" required>
                    @foreach ($alats as $alat)
                        <option value="{{ $alat->id }}" {{ old('alat_camping_id', $unitAlat->alat_camping_id) == $alat->id ? 'selected' : '' }}>
                            [{{ $alat->kode_alat }}] {{ $alat->nama_alat }}
                        </option>
                    @endforeach
                </select>
                @error('alat_camping_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="nomor_seri" class="form-label fw-semibold">Nomor Seri Unit <span class="text-danger">*</span></label>
                    <input type="text" name="nomor_seri" id="nomor_seri" 
                           class="form-control @error('nomor_seri') is-invalid @enderror" 
                           value="{{ old('nomor_seri', $unitAlat->nomor_seri) }}" required>
                    @error('nomor_seri')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="status" class="form-label fw-semibold">Status Kondisi <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="tersedia" {{ old('status', $unitAlat->status) == 'tersedia' ? 'selected' : '' }}>Tersedia (Siap Sewa)</option>
                        <option value="rusak" {{ old('status', $unitAlat->status) == 'rusak' ? 'selected' : '' }}>Rusak / Perlu Perbaikan</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="catatan_kondisi" class="form-label fw-semibold">Catatan Kondisi & Kelengkapan</label>
                <textarea name="catatan_kondisi" id="catatan_kondisi" rows="3" 
                          class="form-control @error('catatan_kondisi') is-invalid @enderror">{{ old('catatan_kondisi', $unitAlat->catatan_kondisi) }}</textarea>
                @error('catatan_kondisi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Update Unit
                </button>
                <a href="{{ route('unit-alat.index') }}" class="btn btn-light border">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection