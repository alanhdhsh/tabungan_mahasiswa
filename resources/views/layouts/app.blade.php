<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TabunganKu - Celengan & Rencana Tabungan Mahasiswa')</title>

    <!-- Google Fonts: Plus Jakarta Sans & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary-navy: #0b192c;
            --primary-dark: #050d18;
            --primary-blue: #1e3e62;
            --accent-mint: #00b488;
            --accent-mint-light: #e6f7f2;
            --accent-mint-glow: rgba(0, 180, 136, 0.25);
            --accent-coral: #ff6500;
            --accent-gold: #f59e0b;
            --bg-body: #f4f7fb;
            --card-border: #e2e8f0;
            --text-dark: #0f172a;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, .brand-font {
            font-family: 'Outfit', 'Plus Jakarta Sans', sans-serif;
        }

        /* Navbar & Brand */
        .navbar-custom {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-navy) 60%, var(--primary-blue) 100%);
            box-shadow: 0 4px 24px rgba(11, 25, 44, 0.2);
            padding: 0.9rem 0;
            position: sticky;
            top: 0;
            z-index: 1030;
            backdrop-filter: blur(10px);
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.35rem;
            color: #ffffff !important;
            display: flex;
            align-items: center;
            gap: 12px;
            letter-spacing: -0.5px;
        }

        .brand-icon-box {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--accent-mint) 0%, #34d399 100%);
            color: #ffffff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            box-shadow: 0 4px 14px var(--accent-mint-glow);
            transition: transform 0.3s ease;
        }

        .navbar-brand:hover .brand-icon-box {
            transform: rotate(-10deg) scale(1.08);
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.82) !important;
            font-weight: 600;
            font-size: 0.925rem;
            padding: 0.55rem 1.1rem !important;
            border-radius: 10px;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-link:hover {
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.12);
            transform: translateY(-1px);
        }

        .nav-link.active {
            color: #ffffff !important;
            background: rgba(0, 180, 136, 0.22);
            border: 1px solid rgba(0, 180, 136, 0.4);
        }

        /* Buttons Custom */
        .btn-mint {
            background: linear-gradient(135deg, var(--accent-mint) 0%, #059669 100%);
            color: #ffffff;
            font-weight: 600;
            border: none;
            padding: 0.6rem 1.4rem;
            border-radius: 50px;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px var(--accent-mint-glow);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-mint:hover {
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(0, 180, 136, 0.4);
            filter: brightness(1.05);
        }

        .btn-navy {
            background: linear-gradient(135deg, var(--primary-navy) 0%, var(--primary-blue) 100%);
            color: #ffffff;
            font-weight: 600;
            border: none;
            padding: 0.6rem 1.4rem;
            border-radius: 50px;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px rgba(11, 25, 44, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-navy:hover {
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(11, 25, 44, 0.35);
        }

        /* Cards Custom */
        .card-custom {
            border: 1px solid var(--card-border);
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
            background: #ffffff;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            overflow: hidden;
        }

        .card-custom:hover {
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        }

        .card-header-custom {
            background: #ffffff;
            border-bottom: 1px solid var(--card-border);
            padding: 1.25rem 1.6rem;
        }

        /* Virtual Digital Savings Card (FinTech Card) */
        .fintech-card {
            background: linear-gradient(135deg, #0b192c 0%, #1e3e62 50%, #002244 100%);
            border-radius: 24px;
            padding: 1.8rem;
            color: #ffffff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 14px 34px rgba(11, 25, 44, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .fintech-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, rgba(0, 180, 136, 0.25) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .fintech-card::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 260px;
            height: 260px;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.2) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .chip-icon {
            width: 44px;
            height: 32px;
            background: linear-gradient(135deg, #ffd700 0%, #e6a100 100%);
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0 1px 2px rgba(255,255,255,0.6), 0 2px 6px rgba(0,0,0,0.3);
            position: relative;
        }

        .chip-icon::after {
            content: '';
            width: 30px;
            height: 20px;
            border: 1px solid rgba(0,0,0,0.25);
            border-radius: 3px;
        }

        /* Celengan Goal Card */
        .goal-card {
            background: #ffffff;
            border-radius: 22px;
            border: 1px solid var(--card-border);
            padding: 1.5rem;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            box-shadow: 0 4px 18px rgba(0,0,0,0.03);
        }

        .goal-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 36px rgba(11, 25, 44, 0.1);
            border-color: #cbd5e1;
        }

        .goal-icon-badge {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }

        /* Category Color Badges */
        .badge-cat-gadget { background-color: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
        .badge-cat-edu { background-color: #f5f3ff; color: #7c3aed; border: 1px solid #ddd6fe; }
        .badge-cat-emergency { background-color: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .badge-cat-transport { background-color: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
        .badge-cat-holiday { background-color: #fdf2f8; color: #db2777; border: 1px solid #fbcfe8; }
        .badge-cat-general { background-color: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }

        /* Progress Bar Modern */
        .progress-goal {
            height: 12px;
            background-color: #e2e8f0;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.06);
        }

        .progress-goal-bar {
            background: linear-gradient(90deg, #10b981 0%, #34d399 50%, #00b488 100%);
            border-radius: 20px;
            transition: width 0.8s ease-in-out;
            box-shadow: 0 2px 8px var(--accent-mint-glow);
        }

        /* Stat Counter Box */
        .stat-mini-box {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 18px;
            padding: 1.25rem 1.4rem;
            display: flex;
            align-items: center;
            gap: 1.1rem;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
            transition: transform 0.25s ease;
        }

        .stat-mini-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        }

        .stat-mini-icon {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        /* Footer */
        footer {
            margin-top: auto;
            background-color: #ffffff;
            border-top: 1px solid var(--card-border);
            padding: 1.8rem 0;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        /* Print Style */
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #ffffff;
            }
            .card-custom, .fintech-card {
                box-shadow: none !important;
                border: 1px solid #ccc !important;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Header & Navigasi -->
    <header class="no-print">
        <nav class="navbar navbar-expand-lg navbar-custom">
            <div class="container">
                <a class="navbar-brand" href="{{ route('tabungan.index') }}">
                    <div class="brand-icon-box">
                        <i class="bi bi-piggy-bank-fill"></i>
                    </div>
                    <div>
                        <span class="d-block brand-font">TabunganKu</span>
                        <small class="d-block text-white-50" style="font-size: 0.72rem; font-weight: 500; letter-spacing: 0.5px;">
                            CELENGAN & RENCANA IMPIAN MAHASISWA
                        </small>
                    </div>
                </a>

                <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="bi bi-list fs-1 text-white"></i>
                </button>

                <div class="collapse navbar-collapse" id="navbarContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-2">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('tabungan.index') ? 'active' : '' }}" href="{{ route('tabungan.index') }}">
                                <i class="bi bi-grid-fill"></i> Celengan & Target
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('tabungan.create') ? 'active' : '' }}" href="{{ route('tabungan.create') }}">
                                <i class="bi bi-plus-circle-fill"></i> Buka Celengan Baru
                            </a>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link btn btn-link text-decoration-none border-0" data-bs-toggle="modal" data-bs-target="#modalKalkulatorGlobal">
                                <i class="bi bi-calculator-fill text-warning"></i> Kalkulator Target
                            </button>
                        </li>
                        <li class="nav-item ms-lg-2">
                            <a class="btn btn-mint btn-sm px-3 py-2" href="{{ route('tabungan.create') }}">
                                <i class="bi bi-stars"></i> Mulai Menabung
                            </a>
                        </li>

                        {{-- Info User yang Sedang Login --}}
                        @auth
                        <li class="nav-item ms-lg-1">
                            <div class="d-flex align-items-center gap-2" style="padding: 0.4rem 0.8rem; background: rgba(255,255,255,0.10); border-radius: 30px; border: 1px solid rgba(255,255,255,0.15);">
                                <div style="width:32px;height:32px;background:linear-gradient(135deg,#00b488,#059669);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.85rem;color:#fff;font-weight:700;flex-shrink:0;">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <span class="d-none d-lg-block text-white" style="font-size:0.85rem;font-weight:600;max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                    {{ Auth::user()->name }}
                                </span>
                                <form method="POST" action="{{ route('auth.logout') }}" class="d-inline m-0">
                                    @csrf
                                    <button type="submit" title="Keluar" style="background:none;border:none;padding:2px 4px;color:rgba(255,255,255,0.7);cursor:pointer;font-size:1rem;line-height:1;transition:color 0.2s ease;" onmouseover="this.style.color='#ff6b6b'" onmouseout="this.style.color='rgba(255,255,255,0.7)'">
                                        <i class="bi bi-box-arrow-right"></i>
                                    </button>
                                </form>
                            </div>
                        </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content Container -->
    <main class="py-4">
        <div class="container">
            <!-- Flash Message Sukses -->
            @if(session('sukses'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4 p-3" role="alert" style="background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); color: #166534;">
                    <div class="d-flex align-items-center">
                        <div class="me-3 fs-3">✨</div>
                        <div>
                            <strong class="fw-bold">Berhasil!</strong> {{ session('sukses') }}
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Flash Message Error -->
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4 p-3" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill fs-3 me-3 text-danger"></i>
                        <div>
                            <strong>Peringatan!</strong> {{ session('error') }}
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Error Validasi Global (jika ada) -->
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4 p-3" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-x-circle-fill fs-3 me-3 text-danger mt-1"></i>
                        <div>
                            <strong>Mohon periksa kembali isian formulir:</strong>
                            <ul class="mb-0 mt-2 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Konten Halaman Spesifik -->
            @yield('content')
        </div>
    </main>

    <!-- Modal Kalkulator Target Impian Global -->
    <div class="modal fade" id="modalKalkulatorGlobal" tabindex="-1" aria-labelledby="modalKalkulatorGlobalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-0 p-4" style="background: linear-gradient(135deg, var(--primary-navy) 0%, var(--primary-blue) 100%); color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="brand-icon-box" style="width: 44px; height: 44px; font-size: 1.4rem;">
                            <i class="bi bi-calculator"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0" id="modalKalkulatorGlobalLabel">Simulasi Menabung Impian</h5>
                            <small class="text-white-50">Hitung berapa yang harus disisihkan untuk capai targetmu</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="card border-0 shadow-sm rounded-4 p-3 mb-3 bg-white">
                        <label class="form-label fw-bold text-dark small">1. Target Impian yang Ingin Dicapai (Rp)</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-light border-0 fw-bold text-success">Rp</span>
                            <input type="number" id="calcTarget" class="form-control form-control-lg border-0 bg-light fw-bold" value="5000000" step="100000">
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-0 px-2" onclick="setCalcTarget(2000000)">Rp 2 Juta</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-0 px-2" onclick="setCalcTarget(5000000)">Rp 5 Juta</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-0 px-2" onclick="setCalcTarget(10000000)">Rp 10 Juta</button>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4 p-3 mb-3 bg-white">
                        <label class="form-label fw-bold text-dark small">2. Target Waktu Pencapaian</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="small text-muted">Durasi Bulan</label>
                                <select id="calcBulan" class="form-select bg-light border-0 fw-semibold">
                                    <option value="1">1 Bulan</option>
                                    <option value="3">3 Bulan</option>
                                    <option value="6" selected>6 Bulan (1 Semester)</option>
                                    <option value="12">12 Bulan (1 Tahun)</option>
                                    <option value="24">24 Bulan (2 Tahun)</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="small text-muted">Saldo Awal yang Ada</label>
                                <input type="number" id="calcSaldoAwal" class="form-control bg-light border-0 fw-semibold" value="500000" step="50000">
                            </div>
                        </div>
                    </div>

                    <!-- Hasil Rekomendasi Menabung -->
                    <div class="p-3 rounded-4" style="background: linear-gradient(135deg, #064e3b 0%, #047857 100%); color: #ffffff;">
                        <small class="text-white-50 d-block mb-1">Rekomendasi yang Perlu Kamu Sisihkan:</small>
                        <div class="d-flex justify-content-between align-items-baseline mb-2">
                            <span class="fs-6 fw-semibold">Per Hari:</span>
                            <span class="fs-4 fw-bold text-warning" id="calcPerHari">Rp 25.000</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-baseline mb-2">
                            <span class="fs-6 fw-semibold">Per Minggu:</span>
                            <span class="fs-5 fw-bold text-white" id="calcPerMinggu">Rp 175.000</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-baseline">
                            <span class="fs-6 fw-semibold">Per Bulan:</span>
                            <span class="fs-5 fw-bold text-white" id="calcPerBulan">Rp 750.000</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                    <a href="{{ route('tabungan.create') }}" class="btn btn-mint px-4">
                        <i class="bi bi-piggy-bank"></i> Buka Celengan Ini
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="no-print">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                    <span class="fw-bold text-dark">
                        <i class="bi bi-piggy-bank-fill text-success me-1"></i> TabunganKu
                    </span> &copy; {{ date('Y') }} • Platform Celengan & Rencana Impian Mahasiswa.
                    <br><small class="text-muted">Membantu mahasiswa mengelola uang saku dan mewujudkan target impian perkuliahan.</small>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                        <i class="bi bi-shield-check text-success me-1"></i> 100% Aman & Terkalkulasi Otomatis
                    </span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5.3 JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Helper JavaScript Umum -->
    <script>
        // Fungsi helper untuk memformat angka ke format Rupiah di browser
        function formatRupiahJs(angka) {
            let number = Math.round(angka);
            let minus = number < 0 ? '-' : '';
            number = Math.abs(number);
            let sNumber = number.toString();
            let sReg = /(\d+)(\d{3})/;
            while (sReg.test(sNumber)) {
                sNumber = sNumber.replace(sReg, '$1' + '.' + '$2');
            }
            return minus + 'Rp ' + sNumber;
        }

        // Kalkulator Modal Script
        function setCalcTarget(val) {
            document.getElementById('calcTarget').value = val;
            hitungSimulasiGlobal();
        }

        function hitungSimulasiGlobal() {
            let target = parseFloat(document.getElementById('calcTarget')?.value || 0);
            let saldoAwal = parseFloat(document.getElementById('calcSaldoAwal')?.value || 0);
            let bulan = parseInt(document.getElementById('calcBulan')?.value || 6);

            let sisa = target - saldoAwal;
            if (sisa < 0) sisa = 0;

            let perBulan = bulan > 0 ? (sisa / bulan) : 0;
            let perMinggu = bulan > 0 ? (sisa / (bulan * 4.33)) : 0;
            let perHari = bulan > 0 ? (sisa / (bulan * 30)) : 0;

            let elHari = document.getElementById('calcPerHari');
            let elMinggu = document.getElementById('calcPerMinggu');
            let elBulan = document.getElementById('calcPerBulan');

            if (elHari) elHari.innerText = formatRupiahJs(perHari);
            if (elMinggu) elMinggu.innerText = formatRupiahJs(perMinggu);
            if (elBulan) elBulan.innerText = formatRupiahJs(perBulan);
        }

        document.getElementById('calcTarget')?.addEventListener('input', hitungSimulasiGlobal);
        document.getElementById('calcSaldoAwal')?.addEventListener('input', hitungSimulasiGlobal);
        document.getElementById('calcBulan')?.addEventListener('change', hitungSimulasiGlobal);
        hitungSimulasiGlobal();
    </script>

    {{-- Script Global: Pesan Validasi Browser dalam Bahasa Indonesia --}}
    <script>
    /**
     * INTERSEPSI VALIDASI BROWSER (HTML5) KE BAHASA INDONESIA
     * 
     * Browser menampilkan pesan validasi bawaan dalam bahasa sistem (biasanya Inggris).
     * Script ini mengintersep event 'invalid' pada semua input, memvalidasi secara manual,
     * dan menampilkan pesan dalam Bahasa Indonesia yang sesuai konteks field.
     */
    (function() {
        'use strict';

        // Peta pesan validasi kustom per nama field
        const pesanKhusus = {
            'nama_mahasiswa':           { required: 'Nama pemilik tabungan wajib diisi.', maxlength: 'Nama pemilik tabungan maksimal 150 karakter.' },
            'tujuan_tabungan':          { required: 'Nama impian / celengan wajib diisi.' },
            'saldo_awal':               { required: 'Saldo awal tabungan wajib diisi.', min: 'Saldo awal tidak boleh bernilai negatif.', number: 'Saldo awal harus berupa angka.' },
            'pemasukan_per_periode':    { required: 'Pemasukan per periode wajib diisi.', min: 'Pemasukan tidak boleh bernilai negatif.', number: 'Pemasukan harus berupa angka.' },
            'periode_pemasukan':        { required: 'Silakan pilih jenis periode pemasukan.' },
            'pengeluaran_makan':        { required: 'Pengeluaran makan wajib diisi.', min: 'Pengeluaran makan tidak boleh negatif.', number: 'Pengeluaran makan harus berupa angka.' },
            'pengeluaran_transportasi': { required: 'Pengeluaran transportasi wajib diisi.', min: 'Pengeluaran transportasi tidak boleh negatif.', number: 'Pengeluaran transportasi harus berupa angka.' },
            'pengeluaran_lainnya':      { required: 'Pengeluaran lainnya wajib diisi.', min: 'Pengeluaran lainnya tidak boleh negatif.', number: 'Pengeluaran lainnya harus berupa angka.' },
            'target_tabungan':          { required: 'Target tabungan impian wajib diisi.', min: 'Target tabungan tidak boleh negatif.', number: 'Target tabungan harus berupa angka.' },
            'nominal_tabungan':         { required: 'Nominal setoran wajib diisi.', min: 'Nominal setoran minimal Rp1.000.', number: 'Nominal setoran harus berupa angka.' },
            'nominal_penarikan':        { required: 'Nominal penarikan wajib diisi.', min: 'Nominal penarikan minimal Rp1.000.', max: 'Nominal penarikan melebihi saldo yang tersedia.', number: 'Nominal penarikan harus berupa angka.' },
            'email':                    { required: 'Alamat email wajib diisi.', email: 'Format email tidak valid.' },
            'password':                 { required: 'Kata sandi wajib diisi.' },
        };

        // Pesan default jika field tidak terdaftar di atas
        function getPesan(input) {
            const nama = input.name || input.id || '';
            const khusus = pesanKhusus[nama] || {};
            const validity = input.validity;

            if (validity.valueMissing) {
                return khusus.required || 'Kolom ini wajib diisi.';
            }
            if (validity.typeMismatch) {
                if (input.type === 'email') return khusus.email || 'Format email tidak valid.';
                if (input.type === 'url')   return 'Format URL tidak valid.';
                return khusus.number || 'Format nilai tidak valid.';
            }
            if (validity.tooShort) {
                return khusus.minlength || ('Minimal ' + input.minLength + ' karakter.');
            }
            if (validity.tooLong) {
                return khusus.maxlength || ('Maksimal ' + input.maxLength + ' karakter.');
            }
            if (validity.rangeUnderflow) {
                const minVal = parseFloat(input.min);
                if (minVal >= 1000) {
                    // Format angka ke Rupiah
                    const fmt = new Intl.NumberFormat('id-ID').format(minVal);
                    return khusus.min || ('Nilai minimal adalah Rp' + fmt + '.');
                }
                return khusus.min || ('Nilai minimal adalah ' + input.min + '.');
            }
            if (validity.rangeOverflow) {
                const maxVal = parseFloat(input.max);
                if (maxVal >= 1000) {
                    const fmt = new Intl.NumberFormat('id-ID').format(maxVal);
                    return khusus.max || ('Nilai tidak boleh melebihi Rp' + fmt + '.');
                }
                return khusus.max || ('Nilai maksimal adalah ' + input.max + '.');
            }
            if (validity.stepMismatch) {
                return 'Nilai tidak sesuai kelipatan yang ditentukan.';
            }
            if (validity.patternMismatch) {
                return input.title || 'Format tidak sesuai.';
            }
            if (validity.badInput) {
                return khusus.number || 'Nilai yang dimasukkan tidak valid.';
            }
            return 'Harap isi kolom ini dengan benar.';
        }

        // Pasang event listener pada semua elemen form saat halaman dimuat
        // Gunakan readyState untuk memastikan berjalan meski DOM sudah siap
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', pasangValidasiBahasa);
        } else {
            // DOM sudah siap (script di akhir body), panggil langsung
            pasangValidasiBahasa();
        }

        // Juga pasang saat ada konten baru (misalnya modal Bootstrap yang dimuat)
        document.addEventListener('shown.bs.modal', function() {
            pasangValidasiBahasa();
        });

        function pasangValidasiBahasa() {
            const inputElements = document.querySelectorAll('input, select, textarea');
            inputElements.forEach(function(input) {
                if (!input.dataset.idValidasiDipasang) {
                    input.dataset.idValidasiDipasang = '1';
                    input.addEventListener('invalid', function(e) {
                        e.preventDefault();
                        const pesan = getPesan(this);
                        this.setCustomValidity(pesan);
                        // Reset agar re-validasi berikutnya bisa menampilkan pesan baru
                        this.addEventListener('input', function resetPesan() {
                            this.setCustomValidity('');
                            this.removeEventListener('input', resetPesan);
                        });
                        // Tampilkan pesan validasi secara native
                        this.reportValidity();
                    });
                }
            });
        }
    })();
    </script>

    @yield('scripts')
</body>
</html>
