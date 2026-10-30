<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Akun - TabunganKu | Celengan &amp; Rencana Impian Mahasiswa</title>
    <meta name="description" content="Daftar akun TabunganKu gratis dan mulai kelola celengan digital untuk mewujudkan impianmu.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary-navy:      #071525;
            --primary-blue:      #123456;
            --accent-mint:       #00A982;
            --accent-mint-light: #e6f7f2;
            --accent-mint-glow:  rgba(0,169,130,0.22);
            --bg-body:           #F3F6FA;
            --card-border:       #e2e8f0;
            --text-dark:         #0f172a;
            --text-muted:        #64748b;
        }
        * { box-sizing: border-box; margin:0; padding:0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
        }
        h1,h2,h3,h4,.brand-font { font-family: 'Outfit','Plus Jakarta Sans',sans-serif; }

        /* Left panel */
        .auth-left {
            flex: 0 0 42%;
            background: linear-gradient(160deg, var(--primary-navy) 0%, var(--primary-blue) 55%, #0d3060 100%);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 3rem 3rem;
            position: relative; overflow: hidden; min-height: 100vh;
        }
        .auth-left::before {
            content:''; position:absolute; top:-80px; right:-80px;
            width:320px; height:320px; border-radius:50%;
            background:radial-gradient(circle,rgba(0,169,130,0.18) 0%,transparent 70%);
        }
        .auth-left::after {
            content:''; position:absolute; bottom:-60px; left:-60px;
            width:260px; height:260px; border-radius:50%;
            background:radial-gradient(circle,rgba(0,169,130,0.12) 0%,transparent 70%);
        }
        .geo-deco { position:absolute; border-radius:50%; pointer-events:none; }
        .geo-1 { width:80px;height:80px; top:15%;left:8%; border:2px solid rgba(0,169,130,0.25); }
        .geo-2 { width:45px;height:45px; bottom:20%;right:10%; border:2px solid rgba(255,255,255,0.12); }
        .geo-3 { width:28px;height:28px; bottom:30%;left:14%; background:rgba(0,169,130,0.15); }

        .left-content { position:relative; z-index:2; text-align:center; max-width:360px; }

        .brand-logo-box {
            width:68px; height:68px;
            background:linear-gradient(135deg,var(--accent-mint),#059669);
            border-radius:20px; display:flex; align-items:center; justify-content:center;
            font-size:2rem; color:#fff;
            box-shadow:0 8px 28px rgba(0,169,130,0.35);
            margin: 0 auto 1rem;
            animation: logoBounce 3.5s ease-in-out infinite;
        }
        @keyframes logoBounce { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }

        .brand-name { font-weight:800; font-size:1.9rem; color:#fff; letter-spacing:-0.5px; }
        .brand-sub  { font-size:0.72rem; font-weight:600; letter-spacing:2px; color:rgba(255,255,255,0.55); text-transform:uppercase; margin-top:3px; }

        .steps-list {
            margin-top: 2rem; text-align:left;
            display:flex; flex-direction:column; gap:1rem;
        }
        .step-item {
            display:flex; align-items:flex-start; gap:0.8rem;
        }
        .step-num {
            width:32px; height:32px; flex-shrink:0;
            background:rgba(0,169,130,0.25);
            border:1.5px solid rgba(0,169,130,0.5);
            border-radius:50%;
            display:flex; align-items:center; justify-content:center;
            font-size:0.8rem; font-weight:700; color:var(--accent-mint);
        }
        .step-text strong { display:block; font-size:0.875rem; color:#fff; font-weight:600; }
        .step-text span { font-size:0.8rem; color:rgba(255,255,255,0.6); }

        .left-tagline {
            margin-top:2rem; font-size:0.95rem; font-weight:600;
            color:rgba(255,255,255,0.85); line-height:1.6;
            padding: 1rem 1.2rem;
            background:rgba(255,255,255,0.06);
            border-radius:14px;
            border-left: 3px solid var(--accent-mint);
        }

        /* Right panel */
        .auth-right {
            flex:1; display:flex; align-items:center; justify-content:center;
            padding: 2.5rem 2rem; background:var(--bg-body);
        }
        .register-card {
            background:#fff; border-radius:28px;
            box-shadow:0 8px 40px rgba(15,23,42,0.09),0 2px 12px rgba(15,23,42,0.04);
            padding:2.5rem 2.4rem; width:100%; max-width:460px;
            border:1px solid var(--card-border);
        }
        .register-card h2 {
            font-family:'Outfit',sans-serif; font-weight:800;
            font-size:1.7rem; color:var(--primary-navy);
            letter-spacing:-0.5px; margin-bottom:0.3rem;
        }
        .register-card .card-desc { color:var(--text-muted); font-size:0.9rem; margin-bottom:1.8rem; }

        .form-label { font-weight:600; font-size:0.875rem; color:#374151; margin-bottom:0.4rem; }
        .form-control {
            border:1.5px solid #e2e8f0; border-radius:12px;
            padding:0.72rem 1rem; font-size:0.91rem;
            font-family:'Plus Jakarta Sans',sans-serif;
            background:#f8fafc; transition:all 0.2s ease;
        }
        .form-control:focus {
            border-color:var(--accent-mint);
            box-shadow:0 0 0 3.5px rgba(0,169,130,0.15);
            background:#fff; outline:none;
        }
        .form-control.is-invalid { border-color:#ef4444; box-shadow:0 0 0 3px rgba(239,68,68,0.12); }
        .form-control::placeholder { color:#9ca3af; }

        .input-password-wrap { position:relative; }
        .input-password-wrap .form-control { padding-right:3rem; }
        .btn-toggle-pw {
            position:absolute; right:12px; top:50%; transform:translateY(-50%);
            background:none; border:none; color:#9ca3af; cursor:pointer;
            padding:4px; font-size:1.05rem; transition:color 0.2s ease;
        }
        .btn-toggle-pw:hover { color:var(--accent-mint); }

        /* Password strength */
        .pw-strength-bar {
            height: 4px; border-radius: 4px;
            background: #e2e8f0; margin-top: 6px;
            overflow: hidden;
        }
        .pw-strength-fill {
            height: 100%; border-radius: 4px;
            transition: all 0.3s ease;
            width: 0%;
        }
        .pw-strength-label { font-size:0.75rem; margin-top:3px; font-weight:600; }

        .btn-register-submit {
            width:100%; background:linear-gradient(135deg,var(--accent-mint) 0%,#059669 100%);
            color:#fff; border:none; border-radius:14px;
            padding:0.88rem 1.5rem; font-size:1rem; font-weight:700;
            font-family:'Plus Jakarta Sans',sans-serif; cursor:pointer;
            display:flex; align-items:center; justify-content:center; gap:0.6rem;
            transition:all 0.25s ease;
            box-shadow:0 6px 20px rgba(0,169,130,0.32); margin-top:1.4rem;
        }
        .btn-register-submit:hover { transform:translateY(-2px); box-shadow:0 10px 28px rgba(0,169,130,0.4); filter:brightness(1.05); }
        .btn-register-submit:active { transform:translateY(0); }

        .btn-spinner {
            display:none; width:18px; height:18px;
            border:2.5px solid rgba(255,255,255,0.35); border-top-color:#fff;
            border-radius:50%; animation:spin 0.75s linear infinite;
        }
        @keyframes spin { to { transform:rotate(360deg); } }

        .login-link-area { text-align:center; margin-top:1.4rem; font-size:0.9rem; color:var(--text-muted); }
        .login-link-area a { color:var(--accent-mint); font-weight:700; text-decoration:none; }
        .login-link-area a:hover { color:#007f62; text-decoration:underline; }

        .auth-footer-note { text-align:center; font-size:0.75rem; color:#94a3b8; margin-top:1.2rem; }

        .alert-err {
            background:linear-gradient(135deg,#fef2f2,#fee2e2); border:1px solid #fecaca;
            border-radius:12px; padding:0.85rem 1rem; font-size:0.875rem;
            color:#b91c1c; margin-bottom:1.3rem;
            display:flex; align-items:flex-start; gap:0.6rem;
        }
        .alert-err i { flex-shrink:0; font-size:1rem; margin-top:1px; }

        @media (max-width:900px) {
            body { flex-direction:column; }
            .auth-left { flex:none; min-height:auto; padding:2rem 1.5rem; }
            .steps-list { display:none; }
        }
        @media (max-width:480px) {
            .register-card { padding:1.8rem 1.2rem; border-radius:20px; }
        }
    </style>
</head>
<body>

    <!-- Left Panel -->
    <div class="auth-left">
        <div class="geo-deco geo-1"></div>
        <div class="geo-deco geo-2"></div>
        <div class="geo-deco geo-3"></div>

        <div class="left-content">
            <div class="brand-logo-box">
                <i class="bi bi-piggy-bank-fill"></i>
            </div>
            <div class="brand-name">TabunganKu</div>
            <div class="brand-sub">Celengan &amp; Rencana Impian Mahasiswa</div>

            <div class="steps-list">
                <div class="step-item">
                    <div class="step-num">1</div>
                    <div class="step-text">
                        <strong>Buat Akun Gratis</strong>
                        <span>Daftar dengan email kampus atau pribadimu</span>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-num">2</div>
                    <div class="step-text">
                        <strong>Buka Celengan Impian</strong>
                        <span>Buat target tabungan sesuai keinginanmu</span>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-num">3</div>
                    <div class="step-text">
                        <strong>Pantau Progres Harian</strong>
                        <span>Lihat saldo dan riwayat transaksi real-time</span>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-num">4</div>
                    <div class="step-text">
                        <strong>Wujudkan Impianmu</strong>
                        <span>Capai target dan nikmati hasilnya!</span>
                    </div>
                </div>
            </div>

            <div class="left-tagline">
                💡 Mahasiswa yang mencatat tabungannya 3x lebih cepat mencapai target impian mereka.
            </div>
        </div>
    </div>

    <!-- Right Panel -->
    <div class="auth-right">
        <div class="register-card">
            <h2>Buat Akun Baru</h2>
            <p class="card-desc">Bergabunglah dan mulai perjalanan menabungmu hari ini.</p>

            @if($errors->any())
                <div class="alert-err">
                    <i class="bi bi-x-circle-fill"></i>
                    <div>
                        @foreach($errors->all() as $err)
                            <div>{{ $err }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form id="formRegister" method="POST" action="{{ route('auth.register.post') }}" novalidate>
                @csrf

                <!-- Nama Lengkap -->
                <div class="mb-3">
                    <label for="regName" class="form-label">
                        <i class="bi bi-person me-1" style="color:var(--accent-mint);"></i> Nama Lengkap
                    </label>
                    <input type="text" id="regName" name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        placeholder="Nama lengkapmu"
                        value="{{ old('name') }}" required autocomplete="name">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="regEmail" class="form-label">
                        <i class="bi bi-envelope me-1" style="color:var(--accent-mint);"></i> Alamat Email
                    </label>
                    <input type="email" id="regEmail" name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        placeholder="nama@email.com"
                        value="{{ old('email') }}" required autocomplete="email">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <!-- Password -->
                <div class="mb-2">
                    <label for="regPassword" class="form-label">
                        <i class="bi bi-lock me-1" style="color:var(--accent-mint);"></i> Kata Sandi
                    </label>
                    <div class="input-password-wrap">
                        <input type="password" id="regPassword" name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Minimal 8 karakter"
                            required autocomplete="new-password">
                        <button type="button" class="btn-toggle-pw" id="btnTogglePw1">
                            <i class="bi bi-eye" id="iconPw1"></i>
                        </button>
                    </div>
                    <!-- Password strength bar -->
                    <div class="pw-strength-bar">
                        <div class="pw-strength-fill" id="pwStrengthFill"></div>
                    </div>
                    <div class="pw-strength-label" id="pwStrengthLabel" style="color:#94a3b8;"></div>
                    @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <!-- Konfirmasi Password -->
                <div class="mb-3">
                    <label for="regPasswordConfirm" class="form-label">
                        <i class="bi bi-lock-fill me-1" style="color:var(--accent-mint);"></i> Konfirmasi Kata Sandi
                    </label>
                    <div class="input-password-wrap">
                        <input type="password" id="regPasswordConfirm" name="password_confirmation"
                            class="form-control @error('password_confirmation') is-invalid @enderror"
                            placeholder="Ulangi kata sandi"
                            required autocomplete="new-password">
                        <button type="button" class="btn-toggle-pw" id="btnTogglePw2">
                            <i class="bi bi-eye" id="iconPw2"></i>
                        </button>
                    </div>
                    @error('password_confirmation')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="btn-register-submit" id="btnRegisterSubmit">
                    <div class="btn-spinner" id="regSpinner"></div>
                    <i class="bi bi-person-plus-fill" id="btnRegIcon"></i>
                    <span id="btnRegText">Buat Akun TabunganKu</span>
                </button>
            </form>

            <div class="login-link-area">
                Sudah punya akun? <a href="{{ route('auth.login') }}" id="linkMasuk">Masuk sekarang</a>
            </div>

            <div class="auth-footer-note">
                <i class="bi bi-shield-check me-1" style="color:var(--accent-mint);"></i>
                Akunmu aman. Password dienkripsi dengan bcrypt.
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        function togglePw(btnId, iconId, inputId) {
            document.getElementById(btnId).addEventListener('click', function() {
                const input = document.getElementById(inputId);
                const icon  = document.getElementById(iconId);
                const hide  = input.type === 'password';
                input.type  = hide ? 'text' : 'password';
                icon.className = hide ? 'bi bi-eye-slash' : 'bi bi-eye';
            });
        }
        togglePw('btnTogglePw1', 'iconPw1', 'regPassword');
        togglePw('btnTogglePw2', 'iconPw2', 'regPasswordConfirm');

        // Password strength meter
        const pwInput   = document.getElementById('regPassword');
        const fillEl    = document.getElementById('pwStrengthFill');
        const labelEl   = document.getElementById('pwStrengthLabel');

        pwInput.addEventListener('input', function() {
            const val = this.value;
            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const configs = [
                { w:'0%', c:'#ef4444', lbl:'' },
                { w:'25%', c:'#ef4444', lbl:'Sangat lemah' },
                { w:'50%', c:'#f59e0b', lbl:'Lemah' },
                { w:'75%', c:'#3b82f6', lbl:'Cukup kuat' },
                { w:'100%', c:'#10b981', lbl:'Kuat 💪' },
            ];
            const cfg = configs[val.length === 0 ? 0 : Math.min(score, 4)];
            fillEl.style.width = cfg.w;
            fillEl.style.background = cfg.c;
            labelEl.textContent = cfg.lbl;
            labelEl.style.color = cfg.c;
        });

        // Loading on submit
        const formReg = document.getElementById('formRegister');
        formReg.addEventListener('submit', function(e) {
            const name  = document.getElementById('regName').value.trim();
            const email = document.getElementById('regEmail').value.trim();
            const pw    = document.getElementById('regPassword').value;
            const pwc   = document.getElementById('regPasswordConfirm').value;

            let valid = true;

            if (!name) { showErr('regName', 'Nama lengkap wajib diisi.'); valid=false; } else clearErr('regName');
            if (!email) { showErr('regEmail', 'Email wajib diisi.'); valid=false; }
            else if (!isEmail(email)) { showErr('regEmail', 'Format email tidak valid.'); valid=false; }
            else clearErr('regEmail');
            if (!pw || pw.length < 8) { showErr('regPassword', 'Kata sandi minimal 8 karakter.'); valid=false; } else clearErr('regPassword');
            if (!pwc) { showErr('regPasswordConfirm', 'Konfirmasi kata sandi wajib diisi.'); valid=false; }
            else if (pw !== pwc) { showErr('regPasswordConfirm', 'Konfirmasi kata sandi tidak cocok.'); valid=false; }
            else clearErr('regPasswordConfirm');

            if (!valid) { e.preventDefault(); return; }

            document.getElementById('regSpinner').style.display = 'block';
            document.getElementById('btnRegIcon').style.display = 'none';
            document.getElementById('btnRegText').textContent = 'Membuat akun...';
            document.getElementById('btnRegisterSubmit').disabled = true;
        });

        function isEmail(v) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v); }

        function showErr(id, msg) {
            const el = document.getElementById(id);
            el.classList.add('is-invalid');
            let fb = el.parentElement.querySelector('.invalid-feedback');
            if (!fb) { fb = document.createElement('div'); fb.className='invalid-feedback d-block'; el.parentElement.appendChild(fb); }
            fb.textContent = msg;
        }
        function clearErr(id) {
            const el = document.getElementById(id);
            el.classList.remove('is-invalid');
            const fb = el.parentElement.querySelector('.invalid-feedback');
            if (fb) fb.remove();
        }

        ['regName','regEmail','regPassword','regPasswordConfirm'].forEach(id => {
            document.getElementById(id).addEventListener('input', function() { clearErr(id); });
        });
    </script>
</body>
</html>
