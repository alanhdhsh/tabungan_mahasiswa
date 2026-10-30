
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - TabunganKu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: #F3F6FA;
            font-family: Arial, sans-serif;
        }
        .card-reset {
            width: 100%;
            max-width: 430px;
            padding: 35px;
            background: white;
            border-radius: 22px;
            box-shadow: 0 8px 35px rgba(15,23,42,.09);
        }
        h2 { color: #071525; font-weight: 700; }
        .form-control {
            padding: 12px;
            border-radius: 10px;
        }
        .btn-reset {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            color: white;
            background: #00A982;
            font-weight: 700;
        }
        .btn-reset:hover { background: #059669; }
    </style>
</head>
<body>
    <div class="card-reset">
        <h2 class="mb-2">Buat Password Baru</h2>
        <p class="text-secondary mb-4">
            Masukkan password baru untuk akun TabunganKu kamu.
        </p>

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-3">
                <label for="email" class="form-label">Alamat Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control"
                    value="{{ old('email', $email) }}"
                    required
                    autocomplete="email">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password Baru</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control"
                    required
                    minlength="8"
                    autocomplete="new-password">
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label">
                    Konfirmasi Password Baru
                </label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="form-control"
                    required
                    minlength="8"
                    autocomplete="new-password">
            </div>

            <button type="submit" class="btn-reset">
                Simpan Password Baru
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="{{ route('auth.login') }}"
               class="text-decoration-none">
                Kembali ke halaman login
            </a>
        </div>
    </div>
</body>
</html>
