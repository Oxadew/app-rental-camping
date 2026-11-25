@extends('layouts.app')

@section('title', 'Katalog Alat Camping')
@section('page-title', 'Katalog Alat Camping')
@section('page-subtitle', 'Daftar jenis peralatan camping, tarif sewa harian, dan ketersediaan stok')

@section('page-actions')
    <a href="{{ route('alat.create') }}" class="btn btn-success">
        <i class="bi bi-plus-lg me-1"></i> Tambah Alat
    </a>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <!-- Filter & Pencarian -->
        <form action="{{ route('alat.index') }}" method="GET" class="row g-2 mb-4">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Cari kode atau nama alat..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="kategori_id" class="form-select">
                    <option value="">-- Semua Kategori --</option>
                    @foreach ($kategoris as $kat)
                        <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>
                            {{ $kat->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'kategori_id']))
                    <a href="{{ route('alat.index') }}" class="btn btn-light border">Reset</a>
                @endif
            </div>
        </form>

        <!-- Tabel Katalog -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>Kode</th>
                        <th>Nama Alat</th>
                        <th>Kategori</th>
                        <th>Harga Sewa / Hari</th>
                        <th class="text-center">Stok (Tersedia / Total)</th>
                        <th width="140" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($alats as $index => $alat)
                        <tr>
                            <td>{{ $alats->firstItem() + $index }}</td>
                            <td><span class="badge bg-secondary">{{ $alat->kode_alat }}</span></td>
                            <td>
                                <div class="fw-semibold">{{ $alat->nama_alat }}</div>
                                <small class="text-muted">{{ Str::limit($alat->deskripsi, 50) }}</small>
                            </td>
                            <td>{{ $alat->kategori->nama_kategori ?? '-' }}</td>
                            <td class="fw-semibold text-success">
                                Rp {{ number_format($alat->harga_sewa_per_hari, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $alat->stok_tersedia > 0 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $alat->stok_tersedia }} / {{ $alat->stok_total }} Unit
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('alat.edit', $alat->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('alat.destroy', $alat->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus katalog alat ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Data alat camping tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $alats->links() }}
        </div>
    </div>
</div>
@endsection