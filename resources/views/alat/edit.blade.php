@extends('layouts.app')

@section('title', 'Edit Alat Camping')
@section('page-title', 'Edit Katalog Alat Camping')
@section('page-subtitle', 'Perbarui data ' . $alat->nama_alat)

@section('page-actions')
    <a href="{{ route('alat.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
@endsection

@section('content')
<div class="card border-0 shadow-sm col-md-9">
    <div class="card-body p-4">
        <form action="{{ route('alat.update', $alat->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label for="kode_alat" class="form-label fw-semibold">Kode Alat <span class="text-danger">*</span></label>
                    <input type="text" name="kode_alat" id="kode_alat" 
                           class="form-control @error('kode_alat') is-invalid @enderror" 
                           value="{{ old('kode_alat', $alat->kode_alat) }}" required>
                    @error('kode_alat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-8">
                    <label for="kategori_alat_id" class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                    <select name="kategori_alat_id" id="kategori_alat_id" 
                            class="form-select @error('kategori_alat_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($kategoris as $kat)
                            <option value="{{ $kat->id }}" {{ old('kategori_alat_id', $alat->kategori_alat_id) == $kat->id ? 'selected' : '' }}>
                                {{ $kat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                    @error('kategori_alat_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-7">
                    <label for="nama_alat" class="form-label fw-semibold">Nama Alat Camping <span class="text-danger">*</span></label>
                    <input type="text" name="nama_alat" id="nama_alat" 
                           class="form-control @error('nama_alat') is-invalid @enderror" 
                           value="{{ old('nama_alat', $alat->nama_alat) }}" required>
                    @error('nama_alat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-5">
                    <label for="harga_sewa_per_hari" class="form-label fw-semibold">Tarif Sewa / Hari (Rp) <span class="text-danger">*</span></label>
                    <input type="number" name="harga_sewa_per_hari" id="harga_sewa_per_hari" 
                           class="form-control @error('harga_sewa_per_hari') is-invalid @enderror" 
                           value="{{ old('harga_sewa_per_hari', $alat->harga_sewa_per_hari) }}" min="1000" step="500" required>
                    @error('harga_sewa_per_hari')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="deskripsi" class="form-label fw-semibold">Spesifikasi / Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" rows="3" 
                          class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi', $alat->deskripsi) }}</textarea>
                @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Update Katalog Alat
                </button>
                <a href="{{ route('alat.index') }}" class="btn btn-light border">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection