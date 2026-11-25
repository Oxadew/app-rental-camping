@extends('layouts.app')

@section('title', 'Tambah Kategori')
@section('page-title', 'Tambah Kategori Alat')
@section('page-subtitle', 'Buat kategori baru untuk katalog peralatan camping')

@section('page-actions')
    <a href="{{ route('kategori.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
@endsection

@section('content')
<div class="card border-0 shadow-sm col-md-8">
    <div class="card-body p-4">
        <form action="{{ route('kategori.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="nama_kategori" class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                <input type="text" name="nama_kategori" id="nama_kategori" 
                       class="form-control @error('nama_kategori') is-invalid @enderror" 
                       value="{{ old('nama_kategori') }}" placeholder="Contoh: Tenda Dome, Tas Carrier" required>
                @error('nama_kategori')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="deskripsi" class="form-label fw-semibold">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" rows="3" 
                          class="form-control @error('deskripsi') is-invalid @enderror" 
                          placeholder="Keterangan singkat kategori (opsional)">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save me-1"></i> Simpan Kategori
                </button>
                <a href="{{ route('kategori.index') }}" class="btn btn-light border">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection