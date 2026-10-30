<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TabunganMahasiswaController;
use App\Http\Controllers\AuthController;

// ═══════════════════════════════════════════════════════
//  ROUTE AUTENTIKASI (Tidak perlu login)
// ═══════════════════════════════════════════════════════

// Halaman Login
Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login.post');

// Halaman Daftar Akun
Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register.post');

// Halaman Lupa Password
Route::get('/lupa-password', [AuthController::class, 'showForgotPassword'])
    ->name('auth.lupa-password');

Route::post('/lupa-password', [AuthController::class, 'sendResetLink'])
    ->name('auth.lupa-password.post');

// Halaman formulir password baru
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])
    ->name('password.reset');

Route::post('/reset-password', [AuthController::class, 'resetPassword'])
    ->name('password.update');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

// ═══════════════════════════════════════════════════════
//  ROUTE APLIKASI (Wajib Login / auth middleware)
// ═══════════════════════════════════════════════════════

Route::middleware(['auth'])->group(function () {

    // Rute Halaman Utama langsung mengarah ke Dashboard Tabungan Mahasiswa
    Route::get('/', [TabunganMahasiswaController::class, 'index'])->name('home');

    // Fitur Cepat Menabung (Setor Tambahan ke Celengan Impian)
    Route::post('/tabungan/{id}/tambah-saldo', [TabunganMahasiswaController::class, 'tambahSaldo'])->name('tabungan.tambahSaldo');

    // Fitur Penarikan Dana dari Celengan
    Route::post('/tabungan/{id}/tarik-saldo', [TabunganMahasiswaController::class, 'tarikSaldo'])->name('tabungan.tarikSaldo');

    // Riwayat Transaksi Lengkap per Celengan
    Route::get('/tabungan/{id}/riwayat', [TabunganMahasiswaController::class, 'riwayat'])->name('tabungan.riwayat');

    // Resource Routes untuk seluruh aksi CRUD Tabungan Mahasiswa:
    Route::resource('tabungan', TabunganMahasiswaController::class);
});
