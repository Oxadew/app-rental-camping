@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Operasional')
@section('page-subtitle', 'Ringkasan ketersediaan alat camping dan transaksi aktif')

@section('page-actions')
    <a href="{{ route('transaksi.create') }}" class="btn btn-success">
        <i class="bi bi-plus-lg me-1"></i> Transaksi Sewa Baru
    </a>
@endsection

@section('content')
    <div class="row g-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Unit Tersedia</div>
                    <h3 class="fw-bold text-success mb-0">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Sedang Disewa</div>
                    <h3 class="fw-bold text-primary mb-0">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Terlambat Kembali</div>
                    <h3 class="fw-bold text-danger mb-0">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Total Penyewa</div>
                    <h3 class="fw-bold text-dark mb-0">0</h3>
                </div>
            </div>
        </div>
    </div>
@endsection