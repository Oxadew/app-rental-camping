@extends('layouts.app')

@section('title', 'Kategori Alat')
@section('page-title', 'Data Kategori Alat')
@section('page-subtitle', 'Kelola pengelompokan peralatan campingc')

@section('page-actions')
    <a href="{{ route('kategori.create') }}" class="btn btn-success">
        <i class="bi bi-plus-lg me-1"></i> Tambah Kategori
    </a>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="60">No</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th class="text-center">Jumlah Jenis Alat</th>
                        <th width="160" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kategoris as $index => $kategori)
                        <tr>
                            <td>{{ $kategoris->firstItem() + $index }}</td>
                            <td class="fw-semibold">{{ $kategori->nama_kategori }}</td>
                            <td class="text-muted">{{ $kategori->deskripsi ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge bg-info text-dark">{{ $kategori->alat_camping_count }} item</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('kategori.edit', $kategori->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('kategori.destroy', $kategori->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
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
                            <td colspan="5" class="text-center text-muted py-4">Belum ada data kategori alat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $kategoris->links() }}
        </div>
    </div>
</div>
@endsection