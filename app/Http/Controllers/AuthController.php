<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

/**
 * AuthController
 *
 * Mengelola proses autentikasi pengguna:
 * - Login (tampilkan form & proses login)
 * - Register (tampilkan form & proses pendaftaran)
 * - Logout
 */
class AuthController extends Controller
{
    /**
     * Tampilkan halaman login.
     * Jika sudah login, alihkan ke dashboard.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('tabungan.index');
        }
        return view('auth.login');
    }

    /**
     * Proses login pengguna.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ], [
            'email.required'    => 'Alamat email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min'      => 'Kata sandi minimal 6 karakter.',
        ]);

        $kredensial = $request->only('email', 'password');
        $ingat = $request->boolean('ingat_saya');

        if (Auth::attempt($kredensial, $ingat)) {
            $request->session()->regenerate();
            return redirect()->intended(route('tabungan.index'))
                ->with('sukses', 'Selamat datang kembali, ' . Auth::user()->name . '! ??');
        }

        return back()
            ->withInput($request->only('email'))
            ->with('login_error', 'Email atau kata sandi yang kamu masukkan salah. Silakan coba lagi.');
    }

    /**
     * Tampilkan halaman pendaftaran.
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('tabungan.index');
        }
        return view('auth.register');
    }

    /**
     * Proses pendaftaran pengguna baru.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:150',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
        ], [
            'name.required'                  => 'Nama lengkap wajib diisi.',
            'name.max'                       => 'Nama maksimal 150 karakter.',
            'email.required'                 => 'Alamat email wajib diisi.',
            'email.email'                    => 'Format email tidak valid.',
            'email.unique'                   => 'Email ini sudah terdaftar. Gunakan email lain atau masuk.',
            'password.required'              => 'Kata sandi wajib diisi.',
            'password.min'                   => 'Kata sandi minimal 8 karakter.',
            'password.confirmed'             => 'Konfirmasi kata sandi tidak cocok.',
            'password_confirmation.required' => 'Konfirmasi kata sandi wajib diisi.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('tabungan.index')
            ->with('sukses', 'Akun berhasil dibuat! Selamat datang di TabunganKu, ' . $user->name . '! ??');
    }

    /**
     * Logout pengguna.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login')
            ->with('sukses', 'Kamu telah berhasil keluar. Sampai jumpa! ??');
 
            }

    /**
     * Tampilkan halaman Lupa Password.
     */
    public function showForgotPassword()
    {
        return view('auth.lupa-password');
    }

    /**
     * Kirim tautan reset password melalui email.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with(
                'sukses',
                'Tautan reset password telah dikirim. Periksa kotak masuk emailmu.'
            );
        }

        return back()
            ->withErrors([
                'email' => __($status),
            ])
            ->withInput($request->only('email'));
    }

    /**
     * Tampilkan formulir password baru.
     */
    public function showResetPassword(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    /**
     * Simpan password baru setelah token diverifikasi.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'token.required' => 'Token reset tidak ditemukan.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('auth.login')
                ->with('sukses', 'Password berhasil diubah. Silakan login dengan password baru.');
        }

        return back()
            ->withErrors(['email' => __($status)])
            ->withInput($request->only('email'));
    }

}
