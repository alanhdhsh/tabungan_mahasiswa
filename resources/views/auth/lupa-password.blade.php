<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - TabunganKu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary-navy: #071525;
            --accent-mint:  #00A982;
            --bg-body:      #F3F6FA;
            --card-border:  #e2e8f0;
            --text-dark:    #0f172a;
            --text-muted:   #64748b;
        }
        * { box-sizing:border-box; margin:0; padding:0; }
        body {
            font-family:'Plus Jakarta Sans',sans-serif;
            background:var(--bg-body); min-height:100vh;
            display:flex; align-items:center; justify-content:center;
            padding:2rem 1rem;
        }
        h1,h2,.brand-font { font-family:'Outfit','Plus Jakarta Sans',sans-serif; }
        .card-lupa {
            background:#fff; border-radius:28px;
            box-shadow:0 8px 40px rgba(15,23,42,0.09);
            padding:2.8rem 2.6rem; width:100%; max-width:420px;
            border:1px solid var(--card-border); text-align:center;
        }
        .icon-box {
            width:72px; height:72px; margin:0 auto 1.4rem;
            background:linear-gradient(135deg,var(--accent-mint),#059669);
            border-radius:20px; display:flex; align-items:center; justify-content:center;
            font-size:2rem; color:#fff;
            box-shadow:0 8px 24px rgba(0,169,130,0.3);
        }
        h2 { font-weight:800; font-size:1.65rem; color:var(--primary-navy); margin-bottom:0.4rem; }
        .desc { color:var(--text-muted); font-size:0.92rem; margin-bottom:2rem; line-height:1.6; }
        .form-label { font-weight:600; font-size:0.875rem; color:#374151; margin-bottom:0.4rem; text-align:left; display:block; }
        .form-control {
            border:1.5px solid #e2e8f0; border-radius:12px;
            padding:0.75rem 1rem; font-size:0.93rem;
            background:#f8fafc; transition:all 0.2s ease; width:100%;
        }
        .form-control:focus { border-color:var(--accent-mint); box-shadow:0 0 0 3.5px rgba(0,169,130,0.15); background:#fff; outline:none; }
        .btn-submit {
            width:100%; background:linear-gradient(135deg,var(--accent-mint),#059669);
            color:#fff; border:none; border-radius:14px; padding:0.88rem;
            font-size:1rem; font-weight:700; cursor:pointer;
            box-shadow:0 6px 20px rgba(0,169,130,0.3); margin-top:1.3rem;
            transition:all 0.25s ease; display:flex; align-items:center; justify-content:center; gap:0.5rem;
        }
        .btn-submit:hover { transform:translateY(-2px); box-shadow:0 10px 28px rgba(0,169,130,0.4); }
        .notice-box {
            background:linear-gradient(135deg,#fffbeb,#fef3c7);
            border:1px solid #fde68a; border-radius:12px;
            padding:1rem 1.2rem; font-size:0.85rem; color:#92400e;
            margin-top:1.2rem; text-align:left;
        }
        .notice-box i { color:#d97706; }
        .back-link { display:block; margin-top:1.4rem; font-size:0.88rem; color:var(--text-muted); }
        .back-link a { color:var(--accent-mint); font-weight:700; text-decoration:none; }
        .back-link a:hover { text-decoration:underline; }
    </style>
</head>
<body>
    <div class="card-lupa">
        <div class="icon-box"><i class="bi bi-key-fill"></i></div>
        <h2>Lupa Password?</h2>
        <p class="desc">Masukkan email akunmu dan kami akan membantu kamu mengatur ulang kata sandi.</p>

        @if(session('sukses'))
            <div style="background:linear-gradient(135deg,#ecfdf5,#d1fae5);border:1px solid #6ee7b7;border-radius:12px;padding:0.85rem 1rem;font-size:0.875rem;color:#065f46;margin-bottom:1.2rem;display:flex;align-items:center;gap:0.6rem;">
                <i class="bi bi-check-circle-fill" style="color:#059669;"></i>
                <span>{{ session('sukses') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('auth.lupa-password.post') }}">
            @csrf
            <div style="text-align:left; margin-bottom:0.5rem;">
                <label for="fpEmail" class="form-label">
                    <i class="bi bi-envelope me-1" style="color:var(--accent-mint);"></i> Alamat Email Terdaftar
                </label>
                <input type="email" id="fpEmail" name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="email@kamu.com"
                    value="{{ old('email') }}" required>
                @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn-submit">
                <i class="bi bi-send-fill"></i> Kirim Tautan Reset Password
            </button>
        </form>

       

        <div class="back-link">
            Ingat password? <a href="{{ route('auth.login') }}">Kembali masuk</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
