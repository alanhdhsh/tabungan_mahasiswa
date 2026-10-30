<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk - TabunganKu | Celengan &amp; Rencana Impian Mahasiswa</title>
    <meta name="description" content="Masuk ke TabunganKu untuk mengelola celengan digital dan mewujudkan target impianmu sebagai mahasiswa.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary-navy:    #071525;
            --primary-blue:    #123456;
            --accent-mint:     #00A982;
            --accent-mint-light: #e6f7f2;
            --accent-mint-glow:  rgba(0, 169, 130, 0.22);
            --bg-body:         #F3F6FA;
            --card-border:     #e2e8f0;
            --text-dark:       #0f172a;
            --text-muted:      #64748b;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, .brand-font {
            font-family: 'Outfit', 'Plus Jakarta Sans', sans-serif;
        }

        /* ─── LEFT PANEL ─── */
        .auth-left {
            flex: 0 0 45%;
            background: linear-gradient(160deg, var(--primary-navy) 0%, var(--primary-blue) 55%, #0d3060 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 3.5rem;
            position: relative;
            overflow: hidden;
            min-height: 100vh;
        }

        /* Dekorasi geometris latar */
        .auth-left::before {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 340px; height: 340px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0,169,130,0.18) 0%, transparent 70%);
            pointer-events: none;
        }
        .auth-left::after {
            content: '';
            position: absolute;
            bottom: -60px; left: -60px;
            width: 280px; height: 280px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0,169,130,0.12) 0%, transparent 70%);
            pointer-events: none;
        }

        .geo-deco {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }
        .geo-deco-1 {
            width: 90px; height: 90px;
            top: 18%; left: 8%;
            border: 2px solid rgba(0,169,130,0.25);
        }
        .geo-deco-2 {
            width: 50px; height: 50px;
            top: 72%; right: 10%;
            border: 2px solid rgba(255,255,255,0.12);
        }
        .geo-deco-3 {
            width: 30px; height: 30px;
            bottom: 20%; left: 12%;
            background: rgba(0,169,130,0.15);
        }
        .geo-deco-4 {
            width: 70px; height: 70px;
            top: 8%; right: 18%;
            border: 1.5px solid rgba(255,255,255,0.08);
        }

        .left-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 380px;
        }

        .brand-logo-box {
            width: 72px; height: 72px;
            background: linear-gradient(135deg, var(--accent-mint), #059669);
            border-radius: 22px;
            display: flex; align-items: center; justify-content: center;
            font-size: 2.2rem;
            color: #fff;
            box-shadow: 0 8px 28px rgba(0,169,130,0.35);
            margin: 0 auto 1.2rem;
            animation: logoBounce 3.5s ease-in-out infinite;
        }

        @keyframes logoBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        .brand-name {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 2rem;
            color: #fff;
            letter-spacing: -0.5px;
            line-height: 1.1;
        }

        .brand-sub {
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 2px;
            color: rgba(255,255,255,0.55);
            text-transform: uppercase;
            margin-top: 3px;
        }

        /* Ilustrasi celengan SVG */
        .piggy-illustration {
            margin: 2rem auto;
            position: relative;
        }
        .piggy-illustration svg {
            filter: drop-shadow(0 12px 24px rgba(0,169,130,0.25));
            animation: piggySway 4s ease-in-out infinite;
        }
        @keyframes piggySway {
            0%, 100% { transform: rotate(-2deg); }
            50% { transform: rotate(2deg); }
        }

        /* Coin animation */
        .coin-anim {
            position: absolute;
            top: 0; left: 50%;
            transform: translateX(-50%);
            width: 18px; height: 18px;
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            border-radius: 50%;
            animation: coinDrop 2.4s ease-in-out infinite;
            box-shadow: 0 2px 8px rgba(245,158,11,0.5);
        }
        @keyframes coinDrop {
            0%   { opacity: 0; top: -10px; }
            20%  { opacity: 1; }
            80%  { opacity: 1; top: 40px; }
            100% { opacity: 0; top: 50px; }
        }

        .left-tagline {
            font-size: 1.05rem;
            font-weight: 600;
            color: rgba(255,255,255,0.88);
            line-height: 1.6;
            margin-top: 0.5rem;
        }

        /* Stat pills */
        .stat-pills {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 1.8rem;
        }
        .stat-pill {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.14);
            border-radius: 30px;
            padding: 0.45rem 1rem;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            color: rgba(255,255,255,0.8);
            backdrop-filter: blur(8px);
            transition: background 0.2s ease;
        }
        .stat-pill .dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: var(--accent-mint);
            flex-shrink: 0;
        }

        /* ─── RIGHT PANEL ─── */
        .auth-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 2rem;
            background: var(--bg-body);
        }

        .login-card {
            background: #ffffff;
            border-radius: 28px;
            box-shadow: 0 8px 40px rgba(15,23,42,0.09), 0 2px 12px rgba(15,23,42,0.04);
            padding: 2.8rem 2.6rem;
            width: 100%;
            max-width: 440px;
            border: 1px solid var(--card-border);
        }

        .login-card h2 {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 1.8rem;
            color: var(--primary-navy);
            letter-spacing: -0.5px;
            margin-bottom: 0.35rem;
        }
        .login-card .card-desc {
            color: var(--text-muted);
            font-size: 0.94rem;
            margin-bottom: 2rem;
        }

        /* Form elements */
        .form-label {
            font-weight: 600;
            font-size: 0.875rem;
            color: #374151;
            margin-bottom: 0.45rem;
        }

        .form-control {
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.93rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            transition: all 0.2s ease;
            color: var(--text-dark);
        }
        .form-control:focus {
            border-color: var(--accent-mint);
            box-shadow: 0 0 0 3.5px rgba(0,169,130,0.15);
            background: #ffffff;
            outline: none;
        }
        .form-control.is-invalid {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239,68,68,0.12);
        }
        .form-control::placeholder { color: #9ca3af; }

        /* Password wrapper */
        .input-password-wrap {
            position: relative;
        }
        .input-password-wrap .form-control {
            padding-right: 3rem;
        }
        .btn-toggle-pw {
            position: absolute;
            right: 12px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 4px;
            line-height: 1;
            transition: color 0.2s ease;
            font-size: 1.05rem;
        }
        .btn-toggle-pw:hover { color: var(--accent-mint); }

        /* Error alert */
        .alert-login-error {
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            border: 1px solid #fecaca;
            border-radius: 12px;
            padding: 0.85rem 1rem;
            font-size: 0.875rem;
            color: #b91c1c;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
        }

        .alert-login-error i { flex-shrink: 0; font-size: 1rem; margin-top: 1px; }

        /* Remember + Forgot */
        .form-check-input:checked {
            background-color: var(--accent-mint);
            border-color: var(--accent-mint);
        }
        .form-check-input:focus { box-shadow: 0 0 0 3px rgba(0,169,130,0.2); }
        .form-check-label {
            font-size: 0.875rem;
            color: #374151;
            font-weight: 500;
        }
        .link-lupa {
            font-size: 0.875rem;
            color: var(--accent-mint);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }
        .link-lupa:hover { color: #007f62; text-decoration: underline; }

        /* Submit Button */
        .btn-login-submit {
            width: 100%;
            background: linear-gradient(135deg, var(--accent-mint) 0%, #059669 100%);
            color: #fff;
            border: none;
            border-radius: 14px;
            padding: 0.9rem 1.5rem;
            font-size: 1rem;
            font-weight: 700;
            font-family: 'Plus Jakarta Sans', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            transition: all 0.25s ease;
            box-shadow: 0 6px 20px rgba(0,169,130,0.32);
            position: relative;
            overflow: hidden;
            margin-top: 1.5rem;
        }
        .btn-login-submit::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,0);
            transition: background 0.2s ease;
        }
        .btn-login-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(0,169,130,0.4);
            filter: brightness(1.05);
        }
        .btn-login-submit:active { transform: translateY(0); }

        /* Loading spinner */
        .btn-spinner {
            display: none;
            width: 18px; height: 18px;
            border: 2.5px solid rgba(255,255,255,0.35);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.75s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Register link */
        .register-link-area {
            text-align: center;
            margin-top: 1.6rem;
            font-size: 0.895rem;
            color: var(--text-muted);
        }
        .register-link-area a {
            color: var(--accent-mint);
            font-weight: 700;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .register-link-area a:hover { color: #007f62; text-decoration: underline; }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1.4rem 0;
        }
        .divider hr {
            flex: 1;
            border: none;
            border-top: 1.5px solid #e2e8f0;
        }
        .divider span {
            font-size: 0.8rem;
            color: #94a3b8;
            font-weight: 500;
            white-space: nowrap;
        }

        /* Footer text */
        .auth-footer-note {
            text-align: center;
            font-size: 0.75rem;
            color: #94a3b8;
            margin-top: 1.4rem;
        }

        /* ─── RESPONSIVE ─── */
        @media (max-width: 900px) {
            body { flex-direction: column; }
            .auth-left {
                flex: none;
                min-height: auto;
                padding: 2.5rem 2rem;
            }
            .auth-right {
                padding: 2rem 1.2rem 3rem;
            }
            .piggy-illustration { display: none; }
            .stat-pills { display: none; }
        }
        @media (max-width: 480px) {
            .login-card { padding: 2rem 1.4rem; border-radius: 20px; }
        }
    </style>
</head>
<body>

    <!-- ═══ LEFT PANEL ═══ -->
    <div class="auth-left">
        <!-- Dekorasi geometris -->
        <div class="geo-deco geo-deco-1"></div>
        <div class="geo-deco geo-deco-2"></div>
        <div class="geo-deco geo-deco-3"></div>
        <div class="geo-deco geo-deco-4"></div>

        <div class="left-content">
            <!-- Logo -->
            <div class="brand-logo-box">
                <i class="bi bi-piggy-bank-fill"></i>
            </div>
            <div class="brand-name">TabunganKu</div>
            <div class="brand-sub">Celengan &amp; Rencana Impian Mahasiswa</div>

            <!-- Ilustrasi celengan SVG -->
            <div class="piggy-illustration" style="width:200px;">
                <div class="coin-anim"></div>
                <svg viewBox="0 0 200 180" xmlns="http://www.w3.org/2000/svg" width="200">
                    <!-- Body celengan -->
                    <ellipse cx="100" cy="110" rx="72" ry="58" fill="#00C49A" opacity="0.95"/>
                    <!-- Highlight body -->
                    <ellipse cx="80" cy="85" rx="30" ry="18" fill="rgba(255,255,255,0.15)"/>
                    <!-- Kepala -->
                    <ellipse cx="155" cy="88" rx="30" ry="26" fill="#00C49A" opacity="0.95"/>
                    <!-- Telinga -->
                    <ellipse cx="160" cy="65" rx="9" ry="7" fill="#059669"/>
                    <ellipse cx="160" cy="65" rx="5" ry="4" fill="#34d399"/>
                    <!-- Mata -->
                    <circle cx="163" cy="84" r="4" fill="#ffffff"/>
                    <circle cx="164" cy="84" r="2" fill="#0b192c"/>
                    <circle cx="165" cy="83" r="0.8" fill="#ffffff"/>
                    <!-- Hidung -->
                    <ellipse cx="178" cy="96" rx="9" ry="6" fill="#059669"/>
                    <circle cx="175" cy="96" r="1.5" fill="#047857"/>
                    <circle cx="181" cy="96" r="1.5" fill="#047857"/>
                    <!-- Slot koin -->
                    <rect x="78" y="56" width="32" height="5" rx="2.5" fill="#047857"/>
                    <!-- Koin di slot -->
                    <ellipse cx="94" cy="58.5" rx="10" ry="4" fill="#fbbf24"/>
                    <!-- Kaki kiri -->
                    <rect x="55" y="155" width="20" height="16" rx="8" fill="#059669"/>
                    <!-- Kaki kanan -->
                    <rect x="90" y="158" width="20" height="16" rx="8" fill="#059669"/>
                    <!-- Kaki belakang kiri -->
                    <rect x="115" y="156" width="18" height="14" rx="7" fill="#047857"/>
                    <!-- Ekor -->
                    <path d="M 30 110 Q 15 95 22 82 Q 28 70 18 60" stroke="#059669" stroke-width="4" fill="none" stroke-linecap="round"/>
                    <!-- Teks Rp -->
                    <text x="95" y="130" text-anchor="middle" font-family="Outfit, sans-serif" font-weight="800" font-size="22" fill="rgba(255,255,255,0.9)">Rp</text>
                </svg>
            </div>

            <!-- Tagline motivasi -->
            <p class="left-tagline">
                "Mulai dari tabungan kecil,<br>wujudkan impian besar."
            </p>

            <!-- Stat pills -->
            <div class="stat-pills">
                <div class="stat-pill">
                    <div class="dot"></div>
                    Kelola celengan digital
                </div>
                <div class="stat-pill">
                    <div class="dot"></div>
                    Hitung target otomatis
                </div>
                <div class="stat-pill">
                    <div class="dot"></div>
                    Riwayat transaksi lengkap
                </div>
            </div>
        </div>
    </div>

    <!-- ═══ RIGHT PANEL ═══ -->
    <div class="auth-right">
        <div class="login-card">

            <!-- Judul form -->
            <h2>Selamat Datang Kembali!</h2>
            <p class="card-desc">Masuk untuk melanjutkan perjalanan menabungmu.</p>

            <!-- Flash sukses (dari logout, dsb) -->
            @if(session('sukses'))
                <div class="alert-login-error" style="background:linear-gradient(135deg,#ecfdf5,#d1fae5);border-color:#6ee7b7;color:#065f46;">
                    <i class="bi bi-check-circle-fill" style="color:#059669;"></i>
                    <span>{{ session('sukses') }}</span>
                </div>
            @endif

            <!-- Pesan error login -->
            @if(session('login_error'))
                <div class="alert-login-error" id="alertLoginError">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <span>{{ session('login_error') }}</span>
                </div>
            @endif

            <!-- Error validasi -->
            @if($errors->any())
                <div class="alert-login-error" id="alertValidasi">
                    <i class="bi bi-x-circle-fill"></i>
                    <div>
                        @foreach($errors->all() as $err)
                            <div>{{ $err }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Form Login -->
            <form id="formLogin" method="POST" action="{{ route('auth.login.post') }}" novalidate>
                @csrf

                <!-- Email -->
                <div class="mb-3">
                    <label for="loginEmail" class="form-label">
                        <i class="bi bi-envelope me-1" style="color:var(--accent-mint);"></i> Alamat Email
                    </label>
                    <input
                        type="email"
                        id="loginEmail"
                        name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        placeholder="nama@email.com"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        required
                    >
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="loginPassword" class="form-label">
                        <i class="bi bi-lock me-1" style="color:var(--accent-mint);"></i> Kata Sandi
                    </label>
                    <div class="input-password-wrap">
                        <input
                            type="password"
                            id="loginPassword"
                            name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Masukkan kata sandi"
                            autocomplete="current-password"
                            required
                        >
                        <button type="button" class="btn-toggle-pw" id="btnTogglePw" aria-label="Tampilkan/sembunyikan kata sandi">
                            <i class="bi bi-eye" id="iconTogglePw"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Remember me + Forgot password -->
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="ingatSaya" name="ingat_saya" value="1" {{ old('ingat_saya') ? 'checked' : '' }}>
                        <label class="form-check-label" for="ingatSaya">Ingat saya</label>
                    </div>
                    <a href="{{ route('auth.lupa-password') }}" class="link-lupa" id="linkLupaPassword">
                        Lupa password?
                    </a>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-login-submit" id="btnLoginSubmit">
                    <div class="btn-spinner" id="loginSpinner"></div>
                    <i class="bi bi-box-arrow-in-right" id="btnLoginIcon"></i>
                    <span id="btnLoginText">Masuk ke TabunganKu</span>
                </button>
            </form>

            <div class="divider">
                <hr><span>atau</span><hr>
            </div>

            <!-- Register link -->
            <div class="register-link-area">
                Belum punya akun?
                <a href="{{ route('auth.register') }}" id="linkDaftar">Daftar sekarang</a>
            </div>

            <div class="auth-footer-note">
                <i class="bi bi-shield-check me-1" style="color:var(--accent-mint);"></i>
                Data kamu aman &amp; terenkripsi. 100% terpercaya.
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ─── Toggle show/hide password ───
        const btnTogglePw  = document.getElementById('btnTogglePw');
        const loginPassword = document.getElementById('loginPassword');
        const iconTogglePw  = document.getElementById('iconTogglePw');

        btnTogglePw.addEventListener('click', function () {
            const isHidden = loginPassword.type === 'password';
            loginPassword.type = isHidden ? 'text' : 'password';
            iconTogglePw.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
        });

        // ─── Loading indicator saat submit ───
        const formLogin     = document.getElementById('formLogin');
        const loginSpinner  = document.getElementById('loginSpinner');
        const btnLoginIcon  = document.getElementById('btnLoginIcon');
        const btnLoginText  = document.getElementById('btnLoginText');
        const btnSubmit     = document.getElementById('btnLoginSubmit');

        formLogin.addEventListener('submit', function (e) {
            const email    = document.getElementById('loginEmail').value.trim();
            const password = document.getElementById('loginPassword').value.trim();

            // Validasi sederhana sebelum submit
            let valid = true;

            if (!email) {
                showInputError('loginEmail', 'Alamat email wajib diisi.');
                valid = false;
            } else if (!isValidEmail(email)) {
                showInputError('loginEmail', 'Format email tidak valid.');
                valid = false;
            } else {
                clearInputError('loginEmail');
            }

            if (!password) {
                showInputError('loginPassword', 'Kata sandi wajib diisi.');
                valid = false;
            } else if (password.length < 6) {
                showInputError('loginPassword', 'Kata sandi minimal 6 karakter.');
                valid = false;
            } else {
                clearInputError('loginPassword');
            }

            if (!valid) {
                e.preventDefault();
                return;
            }

            // Tampilkan loading
            loginSpinner.style.display = 'block';
            btnLoginIcon.style.display = 'none';
            btnLoginText.textContent = 'Sedang masuk...';
            btnSubmit.disabled = true;
        });

        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }

        function showInputError(inputId, pesan) {
            const el = document.getElementById(inputId);
            el.classList.add('is-invalid');
            let feedbackEl = el.parentElement.querySelector('.invalid-feedback');
            if (!feedbackEl) {
                feedbackEl = document.createElement('div');
                feedbackEl.className = 'invalid-feedback d-block';
                el.parentElement.appendChild(feedbackEl);
            }
            feedbackEl.textContent = pesan;
        }

        function clearInputError(inputId) {
            const el = document.getElementById(inputId);
            el.classList.remove('is-invalid');
            const feedbackEl = el.parentElement.querySelector('.invalid-feedback');
            if (feedbackEl) feedbackEl.remove();
        }

        // Reset error saat user mengetik
        document.getElementById('loginEmail').addEventListener('input', function() {
            clearInputError('loginEmail');
        });
        document.getElementById('loginPassword').addEventListener('input', function() {
            clearInputError('loginPassword');
        });

        // Auto-dismiss alert setelah 8 detik
        setTimeout(function() {
            const alertEl = document.getElementById('alertLoginError');
            if (alertEl) {
                alertEl.style.transition = 'opacity 0.5s ease';
                alertEl.style.opacity = '0';
                setTimeout(() => alertEl.remove(), 500);
            }
        }, 8000);
    </script>
</body>
</html>
