<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tabungin - Pengelolaan Tabungan Mahasiswa')</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --sidebar-bg: #111111;
            --sidebar-text: rgba(255,255,255,0.82);
            --sidebar-active: #16A34A;
            --sidebar-hover: #222222;
            --sidebar-width: 250px;
            --bg-body: #F5F5F5;
            --card-bg: #FFFFFF;
            --border-color: #E5E5E5;
            --text-main: #111111;
            --text-muted: #737373;
            --green: #16A34A;
            --red: #DC2626;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            margin: 0;
            padding: 0;
        }

        /* ========================
           SIDEBAR
        ======================== */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            min-height: 100vh;
            position: fixed;
            top: 0; left: 0;
            overflow-y: auto;
            z-index: 1000;
            padding: 1.5rem 0;
            transition: transform 0.3s ease;
        }

        .sidebar-brand {
            padding: 0 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 1rem;
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            text-decoration: none;
        }

        .sidebar-brand:hover { color: white; }

        .sidebar-brand-icon {
            width: 36px;
            height: 36px;
            background: var(--sidebar-active);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .sidebar-section-label {
            padding: 0.5rem 1.25rem 0.25rem;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: rgba(255,255,255,0.35);
            margin-top: 0.5rem;
        }

        .sidebar-nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 1.25rem;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: background 0.2s;
            border-radius: 0;
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }

        .sidebar-nav-link:hover {
            background: var(--sidebar-hover);
            color: white;
        }

        .sidebar-nav-link.active {
            background: var(--sidebar-active);
            color: white;
        }

        .sidebar-nav-link.disabled {
            opacity: 0.4;
            cursor: not-allowed;
            pointer-events: none;
        }

        .sidebar-nav-icon {
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        /* ========================
           CONTENT WRAPPER
        ======================== */
        .content-wrapper {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            background: var(--bg-body);
            min-height: 100vh;
        }

        /* ========================
           MOBILE TOPBAR
        ======================== */
        .mobile-topbar {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 56px;
            background: var(--sidebar-bg);
            z-index: 999;
            align-items: center;
            padding: 0 1rem;
            gap: 0.75rem;
        }

        .mobile-topbar .brand-text {
            color: white;
            font-weight: 700;
            font-size: 1rem;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }

        /* ========================
           STAT CARDS
        ======================== */
        .card-stat {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.25rem;
        }

        .card-stat .stat-label {
            font-size: 0.78rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
        }

        .card-stat .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            margin-top: 0.25rem;
        }

        .card-stat .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        /* ========================
           GENERAL CARDS
        ======================== */
        .card-custom {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
        }

        .card-custom .card-header {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            font-weight: 600;
            font-size: 0.9rem;
            padding: 1rem 1.25rem;
        }

        /* ========================
           PAGE HEADER
        ======================== */
        .page-header {
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .page-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text-main);
            margin: 0;
        }

        .page-subtitle {
            font-size: 0.875rem;
            color: var(--text-muted);
            margin: 0.2rem 0 0;
        }

        /* ========================
           PROGRESS BAR
        ======================== */
        .progress-slim {
            height: 8px;
            border-radius: 20px;
            background: #E5E5E5;
            overflow: hidden;
        }

        .progress-slim .progress-bar {
            border-radius: 20px;
            transition: width 0.6s ease;
        }

        /* ========================
           TABLE
        ======================== */
        .table-custom {
            font-size: 0.875rem;
        }

        .table-custom thead th {
            font-weight: 600;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-muted);
            border-bottom: 2px solid var(--border-color);
            background: #FAFAFA;
            padding: 0.75rem 1rem;
        }

        .table-custom tbody td {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid #F0F0F0;
            vertical-align: middle;
        }

        .table-custom tbody tr:last-child td {
            border-bottom: none;
        }

        .table-custom tbody tr:hover {
            background: #FAFAFA;
        }

        /* ========================
           BADGE SUMBER DANA
        ======================== */
        .badge-tabungan {
            background: #FEE2E2;
            color: var(--red);
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.65rem;
            border-radius: 20px;
        }

        .badge-luar {
            background: #DCFCE7;
            color: var(--green);
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.65rem;
            border-radius: 20px;
        }

        /* ========================
           BUTTONS
        ======================== */
        .btn-green {
            background: var(--green);
            color: #fff;
            font-weight: 600;
            font-size: 0.875rem;
            border: none;
            padding: 0.5rem 1.1rem;
            border-radius: 8px;
            transition: background 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            text-decoration: none;
        }

        .btn-green:hover {
            background: #15803d;
            color: #fff;
        }

        .btn-red {
            background: var(--red);
            color: #fff;
            font-weight: 600;
            font-size: 0.875rem;
            border: none;
            padding: 0.5rem 1.1rem;
            border-radius: 8px;
            transition: background 0.2s;
        }

        .btn-red:hover {
            background: #b91c1c;
            color: #fff;
        }

        /* ========================
           FORM STYLING
        ======================== */
        .form-section {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.75rem;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--text-main);
            margin-bottom: 0.35rem;
        }

        .form-control, .form-select {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.875rem;
            padding: 0.55rem 0.85rem;
            color: var(--text-main);
            transition: border-color 0.2s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--green);
            box-shadow: 0 0 0 3px rgba(22,163,74,0.15);
        }

        /* ========================
           FLASH MESSAGES
        ======================== */
        .flash-success {
            background: #DCFCE7;
            border: 1px solid #BBF7D0;
            color: #166534;
            border-radius: 10px;
            padding: 0.85rem 1.1rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.875rem;
        }

        .flash-error {
            background: #FEE2E2;
            border: 1px solid #FECACA;
            color: #991B1B;
            border-radius: 10px;
            padding: 0.85rem 1.1rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
            font-size: 0.875rem;
        }

        /* ========================
           MOBILE RESPONSIVE
        ======================== */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .sidebar-overlay.open {
                display: block;
            }

            .mobile-topbar {
                display: flex;
            }

            .content-wrapper {
                margin-left: 0;
                padding: 1rem;
                padding-top: calc(56px + 1rem);
            }
        }

        /* ========================
           PRINT
        ======================== */
        @media print {
            .sidebar, .mobile-topbar, .no-print { display: none !important; }
            .content-wrapper { margin-left: 0 !important; padding: 0 !important; }
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Mobile Top Bar -->
    <div class="mobile-topbar no-print">
        <button class="btn btn-link text-white p-0 border-0" id="sidebarToggle">
            <i class="bi bi-list fs-4"></i>
        </button>
        <span class="brand-text">
            <i class="bi bi-piggy-bank-fill text-success me-1"></i> Tabungin
        </span>
    </div>

    <!-- Sidebar Overlay (mobile) -->
    <div class="sidebar-overlay no-print" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <nav class="sidebar no-print" id="mainSidebar">
        <a href="{{ route('tabungan.index') }}" class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <i class="bi bi-piggy-bank-fill text-white"></i>
            </div>
            <span>Tabungin</span>
        </a>

        <!-- Main Nav -->
        <div class="sidebar-section-label">Utama</div>

        <a href="{{ url('/dashboard') }}"
           class="sidebar-nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
            <span class="sidebar-nav-icon"><i class="bi bi-house-fill"></i></span>
            Dashboard
        </a>

        <a href="{{ route('tabungan.index') }}"
           class="sidebar-nav-link {{ request()->routeIs('tabungan.index') ? 'active' : '' }}">
            <span class="sidebar-nav-icon"><i class="bi bi-bank"></i></span>
            Celengan Saya
        </a>

        <!-- Tabungan Section -->
        <div class="sidebar-section-label">Transaksi</div>

        <a href="{{ route('tabungan.index') }}"
           class="sidebar-nav-link {{ request()->is('tabungan/*/setoran*') ? 'active' : '' }}">
            <span class="sidebar-nav-icon"><i class="bi bi-plus-circle-fill"></i></span>
            Setoran
        </a>

        <a href="{{ route('tabungan.index') }}"
           class="sidebar-nav-link {{ request()->is('tabungan/*/penarikan*') ? 'active' : '' }}">
            <span class="sidebar-nav-icon"><i class="bi bi-dash-circle-fill"></i></span>
            Penarikan
        </a>

        <a href="{{ url('/pemasukan') }}"
           class="sidebar-nav-link {{ request()->is('pemasukan*') ? 'active' : '' }}">
            <span class="sidebar-nav-icon"><i class="bi bi-currency-dollar"></i></span>
            Pemasukan
        </a>

        <a href="{{ url('/pengeluaran') }}"
           class="sidebar-nav-link {{ request()->is('pengeluaran*') ? 'active' : '' }}">
            <span class="sidebar-nav-icon"><i class="bi bi-exclamation-triangle-fill"></i></span>
            Pengeluaran Tak Terduga
        </a>

        <!-- Planning Section -->
        <div class="sidebar-section-label">Perencanaan</div>

        <a href="{{ url('/anggaran') }}"
           class="sidebar-nav-link {{ request()->is('anggaran*') ? 'active' : '' }}">
            <span class="sidebar-nav-icon"><i class="bi bi-journal-check"></i></span>
            Anggaran Bulanan
        </a>

        <a href="{{ url('/target') }}"
           class="sidebar-nav-link {{ request()->is('target*') ? 'active' : '' }}">
            <span class="sidebar-nav-icon"><i class="bi bi-bullseye"></i></span>
            Target Tabungan
        </a>

        <!-- Report Section -->
        <div class="sidebar-section-label">Laporan</div>

        <a href="{{ url('/riwayat-transaksi') }}"
           class="sidebar-nav-link {{ request()->is('riwayat-transaksi*') ? 'active' : '' }}">
            <span class="sidebar-nav-icon"><i class="bi bi-clock-history"></i></span>
            Riwayat Transaksi
        </a>

        <a href="{{ url('/laporan') }}"
           class="sidebar-nav-link {{ request()->is('laporan*') ? 'active' : '' }}">
            <span class="sidebar-nav-icon"><i class="bi bi-file-earmark-bar-graph-fill"></i></span>
            Laporan Keuangan
        </a>

        <!-- Account Section -->
        <div class="sidebar-section-label">Akun</div>

        <span class="sidebar-nav-link disabled">
            <span class="sidebar-nav-icon"><i class="bi bi-gear-fill"></i></span>
            Pengaturan Akun
        </span>

        @auth
        <form method="POST" action="{{ route('auth.logout') }}" class="d-block">
            @csrf
            <button type="submit" class="sidebar-nav-link" style="color: #FCA5A5;">
                <span class="sidebar-nav-icon"><i class="bi bi-box-arrow-right"></i></span>
                Logout
            </button>
        </form>
        @endauth
    </nav>

    <!-- Main Content -->
    <div class="content-wrapper">

        {{-- Flash Messages --}}
        @if(session('sukses'))
            <div class="flash-success">
                <i class="bi bi-check-circle-fill"></i>
                <span><strong>Berhasil!</strong> {{ session('sukses') }}</span>
                <button class="btn btn-link p-0 ms-auto text-success" onclick="this.parentElement.remove()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="flash-error">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span><strong>Perhatian!</strong> {{ session('error') }}</span>
                <button class="btn btn-link p-0 ms-auto" style="color:#991B1B;" onclick="this.parentElement.remove()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div class="flash-error">
                <i class="bi bi-x-circle-fill mt-1"></i>
                <div>
                    <strong>Mohon periksa kembali:</strong>
                    <ul class="mb-0 mt-1 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sidebar Toggle Script -->
    <script>
        const sidebar = document.getElementById('mainSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('sidebarToggle');

        function openSidebar() {
            sidebar.classList.add('open');
            overlay.classList.add('open');
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('open');
        }

        if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
        if (overlay) overlay.addEventListener('click', closeSidebar);
    </script>

    @yield('scripts')
</body>
</html>
