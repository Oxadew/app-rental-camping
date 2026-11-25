<aside class="sidebar p-3 d-flex flex-column">
    <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-2 text-white text-decoration-none px-2 py-2 mb-3 border-bottom border-secondary">
        <i class="bi bi-tree-fill fs-4 text-success"></i>
        <div>
            <span class="fs-5 fw-bold d-block lh-1">OutdoorGear</span>
            <small class="text-secondary" style="font-size: 0.75rem;">Camping Rental System</small>
        </div>
    </a>

    <ul class="nav flex-column mb-auto">
        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>

        <div class="sidebar-heading">Data Master</div>

        <li class="nav-item">
            <a href="{{ route('kategori.index') }}" class="nav-link {{ request()->routeIs('kategori.*') ? 'active' : '' }}">
                <i class="bi bi-tags"></i> Kategori Alat
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('alat.index') }}" class="nav-link {{ request()->routeIs('alat.*') ? 'active' : '' }}">
                <i class="bi bi-backpack2"></i> Katalog Alat
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('unit-alat.index') }}" class="nav-link {{ request()->routeIs('unit-alat.*') ? 'active' : '' }}">
                <i class="bi bi-upc-scan"></i> Unit Fisik & Seri
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('penyewa.index') }}" class="nav-link {{ request()->routeIs('penyewa.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Data Penyewa
            </a>
        </li>

        <div class="sidebar-heading">Operasional</div>

        <li class="nav-item">
            <a href="{{ route('transaksi.index') }}" class="nav-link {{ request()->routeIs('transaksi.*') ? 'active' : '' }}">
                <i class="bi bi-cart-check"></i> Transaksi Sewa
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('laporan.index') }}" class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-bar-graph"></i> Laporan & Denda
            </a>
        </li>
    </ul>

    <div class="border-top border-secondary pt-3 px-2 text-secondary small">
        &copy; {{ date('Y') }} OutdoorGear Rent
    </div>
</aside>