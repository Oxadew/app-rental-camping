@extends('layouts.app')

@section('title', 'Unit Fisik & Nomor Seri')
@section('page-title', 'Data Unit Fisik & Nomor Seri')
@section('page-subtitle', 'Pelacakan unit spesifik berdasarkan nomor seri dan kondisi fisik')

@section('page-actions')
    <a href="{{ route('unit-alat.create') }}" class="btn btn-success">
        <i class="bi bi-plus-lg me-1"></i> Tambah Unit Fisik
    </a>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <!-- Filter -->
        <form action="{{ route('unit-alat.index') }}" method="GET" class="row g-2 mb-4">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Cari nomor seri..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="alat_camping_id" class="form-select">
                    <option value="">-- Semua Jenis Alat --</option>
                    @foreach ($alats as $alat)
                        <option value="{{ $alat->id }}" {{ request('alat_camping_id') == $alat->id ? 'selected' : '' }}>
                            [{{ $alat->kode_alat }}] {{ $alat->nama_alat }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">-- Semua Status --</option>
                    <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="disewa" {{ request('status') == 'disewa' ? 'selected' : '' }}>Sedang Disewa</option>
                    <option value="rusak" {{ request('status') == 'rusak' ? 'selected' : '' }}>Rusak / Maintenance</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
                @if(request()->hasAny(['search', 'alat_camping_id', 'status']))
                    <a href="{{ route('unit-alat.index') }}" class="btn btn-light border">Reset</a>
                @endif
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>Nomor Seri</th>
                        <th>Nama Alat</th>
                        <th>Kategori</th>
                        <th class="text-center">Status</th>
                        <th>Catatan Kondisi</th>
                        <th width="140" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($units as $index => $unit)
                        <tr>
                            <td>{{ $units->firstItem() + $index }}</td>
                            <td><span class="badge bg-dark font-monospace">{{ $unit->nomor_seri }}</span></td>
                            <td class="fw-semibold">{{ $unit->alatCamping->nama_alat ?? '-' }}</td>
                            <td>{{ $unit->alatCamping->kategori->nama_kategori ?? '-' }}</td>
                            <td class="text-center">
                                @if ($unit->status === 'tersedia')
                                    <span class="badge bg-success">Tersedia</span>
                                @elseif ($unit->status === 'disewa')
                                    <span class="badge bg-primary">Sedang Disewa</span>
                                @else
                                    <span class="badge bg-danger">Rusak</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $unit->catatan_kondisi ?? '-' }}</td>
                            <td class="text-center">
                                @if ($unit->status !== 'disewa')
                                    <a href="{{ route('unit-alat.edit', $unit->id) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('unit-alat.destroy', $unit->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus unit fisik ini? Stok katalog akan otomatis berkurang.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="badge bg-light text-secondary border">Terkunci Sewa</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada data unit fisik terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $units->links() }}
        </div>
    </div>
</div>
@endsection