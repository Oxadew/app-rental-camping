@extends('layouts.app')

@section('title', 'Tambah Penyewa')
@section('page-title', 'Registrasi Penyewa Baru')
@section('page-subtitle', 'Input identitas penyewa sesuai KTP/SIM')

@section('page-actions')
    <a href="{{ route('penyewa.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
@endsection

@section('content')
<div class="card border-0 shadow-sm col-md-8">
    <div class="card-body p-4">
        <form action="{{ route('penyewa.store') }}" method="POST">
            @csrf

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="nik" class="form-label fw-semibold">NIK (16 Digit) <span class="text-danger">*</span></label>
                    <input type="text" name="nik" id="nik" maxlength="16"
                           class="form-control @error('nik') is-invalid @enderror" 
                           value="{{ old('nik') }}" placeholder="3578xxxxxxxxxxxx" required>
                    @error('nik')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="no_hp" class="form-label fw-semibold">No. HP / WhatsApp <span class="text-danger">*</span></label>
                    <input type="text" name="no_hp" id="no_hp" 
                           class="form-control @error('no_hp') is-invalid @enderror" 
                           value="{{ old('no_hp') }}" placeholder="0812xxxxxxxx" required>
                    @error('no_hp')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="nama_lengkap" class="form-label fw-semibold">Nama Lengkap (Sesuai KTP) <span class="text-danger">*</span></label>
                <input type="text" name="nama_lengkap" id="nama_lengkap" 
                       class="form-control @error('nama_lengkap') is-invalid @enderror" 
                       value="{{ old('nama_lengkap') }}" placeholder="Nama lengkap penyewa" required>
                @error('nama_lengkap')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="alamat" class="form-label fw-semibold">Alamat Domisili <span class="text-danger">*</span></label>
                <textarea name="alamat" id="alamat" rows="3" 
                          class="form-control @error('alamat') is-invalid @enderror" 
                          placeholder="Alamat lengkap tempat tinggal" required>{{ old('alamat') }}</textarea>
                @error('alamat')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save me-1"></i> Simpan Penyewa
                </button>
                <a href="{{ route('penyewa.index') }}" class="btn btn-light border">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection