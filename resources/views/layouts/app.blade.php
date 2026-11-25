<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'OutdoorGear Rent') — Sistem Penyewaan Alat Camping</title>
    
    <!-- Bootstrap 5.3 CSS & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        .sidebar {
            min-height: 100vh;
            width: 260px;
            background-color: #1e293b;
            color: #f8fafc;
            transition: all 0.2s;
        }
        .sidebar .nav-link {
            color: #cbd5e1;
            border-radius: 0.5rem;
            margin-bottom: 0.25rem;
            padding: 0.65rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .sidebar .nav-link:hover {
            background-color: #334155;
            color: #ffffff;
        }
        .sidebar .nav-link.active {
            background-color: #10b981; /* Nuansa hijau outdoor */
            color: #ffffff;
            font-weight: 600;
        }
        .sidebar-heading {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94a3b8;
            padding: 0.75rem 1rem 0.35rem;
        }
        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Konten Utama -->
        <div class="main-wrapper">
            <!-- Navbar -->
            @include('layouts.navbar')

            <main class="p-4">
                <!-- Header Halaman & Breadcrumb/Tombol Aksi -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-0 fw-bold text-dark">@yield('page-title', 'Dashboard')</h4>
                        @hasSection('page-subtitle')
                            <small class="text-muted">@yield('page-subtitle')</small>
                        @endif
                    </div>
                    <div>
                        @yield('page-actions')
                    </div>
                </div>

                <!-- Komponen Alert Global -->
                @include('components.alert')

                <!-- Slot Konten Halaman -->
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>