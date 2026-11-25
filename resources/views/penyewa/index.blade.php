@extends('layouts.app')

@section('title', 'Data Penyewa')
@section('page-title', 'Data Penyewa / Pelanggan')
@section('page-subtitle', 'Kelola identitas penyewa sebagai jaminan transaksi')

@section('page-actions')
    <a href="{{ route('penyewa.create') }}" class="btn btn-success">
        <i class="bi bi-plus-lg me-1"></i> Tambah Penyewa
    </a>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('penyewa.index') }}" method="GET" class="row g-2 mb-4">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Cari nama lengkap, NIK, atau nomor HP..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i> Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('penyewa.index') }}" class="btn btn-light border">Reset</a>
                @endif
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>NIK</th>
                        <th>Nama Lengkap</th>
                        <th>No. HP / WhatsApp</th>
                        <th>Alamat</th>
                        <th class="text-center">Total Sewa</th>
                        <th width="140" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($penyewas as $index => $penyewa)
                        <tr>
                            <td>{{ $penyewas->firstItem() + $index }}</td>
                            <td class="font-monospace">{{ $penyewa->nik }}</td>
                            <td class="fw-semibold">{{ $penyewa->nama_lengkap }}</td>
                            <td>{{ $penyewa->no_hp }}</td>
                            <td class="text-muted small">{{ Str::limit($penyewa->alamat, 45) }}</td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ $penyewa->transaksi_sewa_count }}x</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('penyewa.edit', $penyewa->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('penyewa.destroy', $penyewa->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data penyewa ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada data penyewa terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $penyewas->links() }}
        </div>
    </div>
</div>
@endsection