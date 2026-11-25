@extends('layouts.app')

@section('title', 'Edit Kategori')
@section('page-title', 'Edit Kategori Alat')
@section('page-subtitle', 'Perbarui informasi kategori ' . $kategori->nama_kategori)

@section('page-actions')
    <a href="{{ route('kategori.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
@endsection

@section('content')
<div class="card border-0 shadow-sm col-md-8">
    <div class="card-body p-4">
        <form action="{{ route('kategori.update', $kategori->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="nama_kategori" class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                <input type="text" name="nama_kategori" id="nama_kategori" 
                       class="form-control @error('nama_kategori') is-invalid @enderror" 
                       value="{{ old('nama_kategori', $kategori->nama_kategori) }}" required>
                @error('nama_kategori')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="deskripsi" class="form-label fw-semibold">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" rows="3" 
                          class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi', $kategori->deskripsi) }}</textarea>
                @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Update Kategori
                </button>
                <a href="{{ route('kategori.index') }}" class="btn btn-light border">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection