<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-4 py-3">
    <div class="container-fluid px-0">
        <span class="text-muted small">
            <i class="bi bi-calendar3 me-1"></i> {{ now()->translatedFormat('l, d F Y') }}
        </span>

        <div class="ms-auto d-flex align-items-center gap-3">
            <div class="dropdown">
                <button class="btn btn-light btn-sm dropdown-toggle d-flex align-items-center gap-2 border" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle fs-5 text-success"></i>
                    <span>{{ Auth::user()->name ?? 'Admin Petugas' }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><span class="dropdown-item-text small text-muted">{{ Auth::user()->email ?? 'admin@outdoorgear.test' }}</span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i> Keluar
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>