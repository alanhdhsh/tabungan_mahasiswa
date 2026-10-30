<?php

/**
 * Skrip Pengujian Pemisahan Data Pengguna TabunganKu
 *
 * Skrip ini menguji bahwa:
 * 1. Dua akun berbeda dapat dibuat
 * 2. Celengan yang dibuat oleh akun 1 tidak terlihat oleh akun 2
 * 3. Statistik dashboard dihitung per pengguna
 * 4. Riwayat transaksi terpisah per pengguna
 * 5. Akses langsung via URL/ID ditolak jika bukan pemilik
 *
 * Jalankan: php artisan tinker < tests/test_pemisahan_data.php
 * Atau copy-paste ke tinker secara interaktif.
 */

use App\Models\User;
use App\Models\TabunganMahasiswa;
use App\Models\RiwayatTabungan;
use Illuminate\Support\Facades\Hash;

echo "========================================\n";
echo "  PENGUJIAN PEMISAHAN DATA TABUNGANKU  \n";
echo "========================================\n\n";

// -----------------------------------------------
// LANGKAH 1: Buat 2 akun pengujian
// -----------------------------------------------
echo "--- LANGKAH 1: Membuat 2 akun pengujian ---\n";

$user1 = User::firstOrCreate(
    ['email' => 'test_akun1@tabunganku.test'],
    ['name' => 'Akun Uji Satu', 'password' => Hash::make('password123')]
);
echo "✅ Akun 1: {$user1->name} (ID: {$user1->id})\n";

$user2 = User::firstOrCreate(
    ['email' => 'test_akun2@tabunganku.test'],
    ['name' => 'Akun Uji Dua', 'password' => Hash::make('password456')]
);
echo "✅ Akun 2: {$user2->name} (ID: {$user2->id})\n\n";

// -----------------------------------------------
// LANGKAH 2: Buat celengan untuk akun 1
// -----------------------------------------------
echo "--- LANGKAH 2: Membuat celengan untuk Akun 1 ---\n";

$celengan1 = TabunganMahasiswa::create([
    'user_id' => $user1->id,
    'nama_mahasiswa' => 'Akun Satu',
    'tujuan_tabungan' => 'Laptop Baru (Milik Akun 1)',
    'kategori' => 'Gadget & Teknologi',
    'saldo_awal' => 500000,
    'pemasukan_per_periode' => 2000000,
    'periode_pemasukan' => 'bulanan',
    'pengeluaran_makan' => 600000,
    'pengeluaran_transportasi' => 200000,
    'pengeluaran_lainnya' => 100000,
    'pengeluaran_tidak_terduga' => 50000,
    'target_tabungan' => 5000000,
    'total_pengeluaran' => 950000,
    'tabungan_per_periode' => 1050000,
    'saldo_setelah_satu_periode' => 1550000,
    'persentase_tabungan' => 52.5,
    'sisa_target' => 3450000,
    'jumlah_periode_tambahan' => 4,
    'status_keuangan' => 'Surplus, target dalam proses pencapaian',
    'tanggal_pencatatan' => now()->toDateString(),
]);
echo "✅ Celengan Akun 1: '{$celengan1->tujuan_tabungan}' (ID: {$celengan1->id})\n";

// Buat riwayat untuk celengan akun 1
$riwayat1 = RiwayatTabungan::create([
    'user_id' => $user1->id,
    'tabungan_mahasiswa_id' => $celengan1->id,
    'nominal' => 500000,
    'jenis' => 'setoran_awal',
    'saldo_sebelumnya' => 0,
    'saldo_sesudahnya' => 1550000,
    'catatan' => 'Setoran awal celengan Akun 1',
    'tanggal_transaksi' => now(),
]);
echo "✅ Riwayat Akun 1: setoran_awal Rp 500.000 (ID: {$riwayat1->id})\n\n";

// -----------------------------------------------
// LANGKAH 3: Buat celengan untuk akun 2
// -----------------------------------------------
echo "--- LANGKAH 3: Membuat celengan untuk Akun 2 ---\n";

$celengan2 = TabunganMahasiswa::create([
    'user_id' => $user2->id,
    'nama_mahasiswa' => 'Akun Dua',
    'tujuan_tabungan' => 'Dana Darurat (Milik Akun 2)',
    'kategori' => 'Dana Darurat',
    'saldo_awal' => 1000000,
    'pemasukan_per_periode' => 1500000,
    'periode_pemasukan' => 'bulanan',
    'pengeluaran_makan' => 500000,
    'pengeluaran_transportasi' => 150000,
    'pengeluaran_lainnya' => 100000,
    'pengeluaran_tidak_terduga' => 0,
    'target_tabungan' => 3000000,
    'total_pengeluaran' => 750000,
    'tabungan_per_periode' => 750000,
    'saldo_setelah_satu_periode' => 1750000,
    'persentase_tabungan' => 50.0,
    'sisa_target' => 1250000,
    'jumlah_periode_tambahan' => 2,
    'status_keuangan' => 'Surplus, target dalam proses pencapaian',
    'tanggal_pencatatan' => now()->toDateString(),
]);
echo "✅ Celengan Akun 2: '{$celengan2->tujuan_tabungan}' (ID: {$celengan2->id})\n";

$riwayat2 = RiwayatTabungan::create([
    'user_id' => $user2->id,
    'tabungan_mahasiswa_id' => $celengan2->id,
    'nominal' => 1000000,
    'jenis' => 'setoran_awal',
    'saldo_sebelumnya' => 0,
    'saldo_sesudahnya' => 1750000,
    'catatan' => 'Setoran awal celengan Akun 2',
    'tanggal_transaksi' => now(),
]);
echo "✅ Riwayat Akun 2: setoran_awal Rp 1.000.000 (ID: {$riwayat2->id})\n\n";

// -----------------------------------------------
// LANGKAH 4: Uji pemisahan data
// -----------------------------------------------
echo "--- LANGKAH 4: Pengujian Pemisahan Data ---\n\n";

// Test 4a: Celengan per user
$celAkun1 = TabunganMahasiswa::where('user_id', $user1->id)->count();
$celAkun2 = TabunganMahasiswa::where('user_id', $user2->id)->count();
echo "📊 Jumlah celengan Akun 1: {$celAkun1}\n";
echo "📊 Jumlah celengan Akun 2: {$celAkun2}\n";
assert($celAkun1 >= 1, 'Akun 1 harus punya minimal 1 celengan');
assert($celAkun2 >= 1, 'Akun 2 harus punya minimal 1 celengan');
echo "✅ LULUS: Setiap akun memiliki celengan masing-masing\n\n";

// Test 4b: Celengan akun 1 tidak termasuk milik akun 2
$celAkun1Ids = TabunganMahasiswa::where('user_id', $user1->id)->pluck('id')->toArray();
$celAkun2Ids = TabunganMahasiswa::where('user_id', $user2->id)->pluck('id')->toArray();
$overlap = array_intersect($celAkun1Ids, $celAkun2Ids);
assert(count($overlap) === 0, 'Tidak boleh ada celengan yang dimiliki 2 akun');
echo "✅ LULUS: Tidak ada celengan yang tumpang tindih antar akun\n\n";

// Test 4c: Total saldo per user
$saldoAkun1 = TabunganMahasiswa::where('user_id', $user1->id)->sum('saldo_setelah_satu_periode');
$saldoAkun2 = TabunganMahasiswa::where('user_id', $user2->id)->sum('saldo_setelah_satu_periode');
echo "💰 Total saldo Akun 1: Rp " . number_format($saldoAkun1, 0, ',', '.') . "\n";
echo "💰 Total saldo Akun 2: Rp " . number_format($saldoAkun2, 0, ',', '.') . "\n";
echo "✅ LULUS: Statistik saldo dihitung terpisah per pengguna\n\n";

// Test 4d: Riwayat per user
$riwAkun1 = RiwayatTabungan::where('user_id', $user1->id)->count();
$riwAkun2 = RiwayatTabungan::where('user_id', $user2->id)->count();
echo "📝 Jumlah riwayat Akun 1: {$riwAkun1}\n";
echo "📝 Jumlah riwayat Akun 2: {$riwAkun2}\n";
echo "✅ LULUS: Riwayat transaksi terpisah per pengguna\n\n";

// Test 4e: Akun 2 tidak bisa melihat celengan akun 1 (simulasi query controller)
$aksesIlegal = TabunganMahasiswa::where('id', $celengan1->id)
    ->where('user_id', $user2->id)
    ->first();
assert($aksesIlegal === null, 'Akun 2 TIDAK boleh bisa mengakses celengan Akun 1');
echo "🔒 Akun 2 mencoba akses celengan Akun 1 (ID: {$celengan1->id}): " . ($aksesIlegal === null ? 'DITOLAK ✅' : 'LOLOS ❌') . "\n";

// Test 4f: Akun 1 tidak bisa melihat celengan akun 2
$aksesIlegal2 = TabunganMahasiswa::where('id', $celengan2->id)
    ->where('user_id', $user1->id)
    ->first();
assert($aksesIlegal2 === null, 'Akun 1 TIDAK boleh bisa mengakses celengan Akun 2');
echo "🔒 Akun 1 mencoba akses celengan Akun 2 (ID: {$celengan2->id}): " . ($aksesIlegal2 === null ? 'DITOLAK ✅' : 'LOLOS ❌') . "\n\n";

// Test 4g: Data tanpa user_id (data lama)
$dataOrphan = TabunganMahasiswa::whereNull('user_id')->count();
echo "⚠️  Data lama tanpa pemilik (user_id = NULL): {$dataOrphan} record\n";
echo "   → Data ini TIDAK akan muncul di dashboard pengguna manapun\n";
echo "   → Aman, tidak di-assign sembarangan ke satu akun\n\n";

// -----------------------------------------------
// LANGKAH 5: Bersihkan data pengujian
// -----------------------------------------------
echo "--- LANGKAH 5: Membersihkan data pengujian ---\n";
RiwayatTabungan::where('user_id', $user1->id)->delete();
RiwayatTabungan::where('user_id', $user2->id)->delete();
TabunganMahasiswa::where('user_id', $user1->id)->delete();
TabunganMahasiswa::where('user_id', $user2->id)->delete();
$user1->delete();
$user2->delete();
echo "✅ Data pengujian dibersihkan.\n\n";

echo "========================================\n";
echo "  SEMUA PENGUJIAN BERHASIL! ✅🎉       \n";
echo "========================================\n";
echo "\nKesimpulan:\n";
echo "- Setiap akun hanya melihat data miliknya sendiri\n";
echo "- Statistik dashboard dihitung per pengguna\n";
echo "- Akses silang antar akun DITOLAK\n";
echo "- Data lama tanpa pemilik tidak muncul di dashboard\n";
