<?php

namespace Tests\Feature;

use App\Models\TabunganMahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TabunganMahasiswaTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /**
     * Uji 1: Halaman dashboard dan riwayat dapat diakses dengan sukses.
     */
    public function test_halaman_dashboard_dapat_diakses(): void
    {
        $response = $this->get('/tabungan');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Tabungan Mahasiswa');
    }

    /**
     * Uji 2: Halaman formulir input baru dapat diakses.
     */
    public function test_halaman_create_dapat_diakses(): void
    {
        $response = $this->get('/tabungan/create');
        $response->assertStatus(200);
        $response->assertSee('Form Perhitungan Tabungan Mahasiswa');
    }

    /**
     * Uji 3: Perhitungan skenario Surplus (Pemasukan > Pengeluaran, target belum tercapai).
     */
    public function test_perhitungan_skenario_surplus(): void
    {
        $data = [
            'nama_mahasiswa' => 'Testing Surplus',
            'saldo_awal' => 1000000,
            'pemasukan_per_periode' => 2000000,
            'periode_pemasukan' => 'bulanan',
            'pengeluaran_makan' => 600000,
            'pengeluaran_transportasi' => 200000,
            'pengeluaran_lainnya' => 200000,
            'target_tabungan' => 5000000,
            'tanggal_pencatatan' => '2025-01-01',
        ];

        // Total pengeluaran = 600k + 200k + 200k = 1.000.000
        // Tabungan per periode = 2.000.000 - 1.000.000 = 1.000.000
        // Saldo setelah 1 periode = 1.000.000 + 1.000.000 = 2.000.000
        // Persentase tabungan = (1.000.000 / 2.000.000) * 100 = 50%
        // Sisa target = 5.000.000 - 2.000.000 = 3.000.000
        // Jumlah periode tambahan = ceil(3.000.000 / 1.000.000) = 3 periode

        $response = $this->post('/tabungan', $data);
        $response->assertRedirect();

        $this->assertDatabaseHas('tabungan_mahasiswa', [
            'nama_mahasiswa' => 'Testing Surplus',
            'total_pengeluaran' => 1000000,
            'tabungan_per_periode' => 1000000,
            'saldo_setelah_satu_periode' => 2000000,
            'persentase_tabungan' => 50,
            'sisa_target' => 3000000,
            'jumlah_periode_tambahan' => 3,
            'status_keuangan' => 'Surplus, target dalam proses pencapaian',
        ]);
    }

    /**
     * Uji 4: Perhitungan skenario Target Sudah Tercapai.
     */
    public function test_perhitungan_skenario_target_tercapai(): void
    {
        $data = [
            'nama_mahasiswa' => 'Testing Tercapai',
            'saldo_awal' => 4500000,
            'pemasukan_per_periode' => 1500000,
            'periode_pemasukan' => 'bulanan',
            'pengeluaran_makan' => 500000,
            'pengeluaran_transportasi' => 200000,
            'pengeluaran_lainnya' => 300000,
            'target_tabungan' => 5000000,
            'tanggal_pencatatan' => '2025-01-01',
        ];

        // Total pengeluaran = 1.000.000
        // Tabungan per periode = 500.000
        // Saldo setelah 1 periode = 5.000.000 >= target (5.000.000)
        // Status harus "Target tabungan telah tercapai", periode tambahan = 0

        $response = $this->post('/tabungan', $data);
        $response->assertRedirect();

        $this->assertDatabaseHas('tabungan_mahasiswa', [
            'nama_mahasiswa' => 'Testing Tercapai',
            'saldo_setelah_satu_periode' => 5000000,
            'sisa_target' => 0,
            'jumlah_periode_tambahan' => 0,
            'status_keuangan' => 'Target tabungan telah tercapai',
        ]);
    }

    /**
     * Uji 5: Perhitungan skenario Impas (Pemasukan sama dengan pengeluaran).
     */
    public function test_perhitungan_skenario_impas(): void
    {
        $data = [
            'nama_mahasiswa' => 'Testing Impas',
            'saldo_awal' => 500000,
            'pemasukan_per_periode' => 1000000,
            'periode_pemasukan' => 'bulanan',
            'pengeluaran_makan' => 500000,
            'pengeluaran_transportasi' => 300000,
            'pengeluaran_lainnya' => 200000,
            'target_tabungan' => 4000000,
            'tanggal_pencatatan' => '2025-01-01',
        ];

        // Total pengeluaran = 1.000.000
        // Tabungan = 0
        // Status harus "Impas, belum ada penambahan tabungan"

        $response = $this->post('/tabungan', $data);
        $response->assertRedirect();

        $this->assertDatabaseHas('tabungan_mahasiswa', [
            'nama_mahasiswa' => 'Testing Impas',
            'tabungan_per_periode' => 0,
            'status_keuangan' => 'Impas, belum ada penambahan tabungan',
        ]);
    }

    /**
     * Uji 6: Perhitungan skenario Defisit (Pengeluaran > Pemasukan).
     */
    public function test_perhitungan_skenario_defisit(): void
    {
        $data = [
            'nama_mahasiswa' => 'Testing Defisit',
            'saldo_awal' => 500000,
            'pemasukan_per_periode' => 1000000,
            'periode_pemasukan' => 'bulanan',
            'pengeluaran_makan' => 700000,
            'pengeluaran_transportasi' => 300000,
            'pengeluaran_lainnya' => 200000,
            'target_tabungan' => 4000000,
            'tanggal_pencatatan' => '2025-01-01',
        ];

        // Total pengeluaran = 1.200.000
        // Tabungan = -200.000
        // Status harus "Defisit, pengeluaran melebihi pemasukan"

        $response = $this->post('/tabungan', $data);
        $response->assertRedirect();

        $this->assertDatabaseHas('tabungan_mahasiswa', [
            'nama_mahasiswa' => 'Testing Defisit',
            'tabungan_per_periode' => -200000,
            'status_keuangan' => 'Defisit, pengeluaran melebihi pemasukan',
        ]);
    }

    /**
     * Uji 7: Skenario Pemasukan 0 (Aman dari Division by Zero).
     */
    public function test_pemasukan_nol_tidak_error_division_by_zero(): void
    {
        $data = [
            'nama_mahasiswa' => 'Testing Nol',
            'saldo_awal' => 200000,
            'pemasukan_per_periode' => 0,
            'periode_pemasukan' => 'bulanan',
            'pengeluaran_makan' => 50000,
            'pengeluaran_transportasi' => 20000,
            'pengeluaran_lainnya' => 10000,
            'target_tabungan' => 1000000,
            'tanggal_pencatatan' => '2025-01-01',
        ];

        $response = $this->post('/tabungan', $data);
        $response->assertRedirect();

        $this->assertDatabaseHas('tabungan_mahasiswa', [
            'nama_mahasiswa' => 'Testing Nol',
            'persentase_tabungan' => 0,
            'status_keuangan' => 'Defisit, pengeluaran melebihi pemasukan',
        ]);
    }

    /**
     * Uji 8: Validasi input negatif dan input kosong.
     */
    public function test_validasi_menolak_input_negatif_dan_kosong(): void
    {
        $response = $this->post('/tabungan', [
            'saldo_awal' => -5000,
            'pemasukan_per_periode' => -1000,
            'periode_pemasukan' => 'invalid_periode',
            'pengeluaran_makan' => -200,
            'pengeluaran_transportasi' => -100,
            'pengeluaran_lainnya' => -100,
            'target_tabungan' => -10000,
        ]);

        $response->assertSessionHasErrors([
            'saldo_awal',
            'pemasukan_per_periode',
            'periode_pemasukan',
            'pengeluaran_makan',
            'pengeluaran_transportasi',
            'pengeluaran_lainnya',
            'target_tabungan',
        ]);
    }

    /**
     * Uji: Nama mahasiswa otomatis menggunakan nama user yang sedang login jika dikosongkan.
     */
    public function test_nama_mahasiswa_otomatis_dari_user_login(): void
    {
        $response = $this->post('/tabungan', [
            'tujuan_tabungan' => 'Beli Laptop Kuliah',
            'saldo_awal' => 500000,
            'pemasukan_per_periode' => 1500000,
            'periode_pemasukan' => 'bulanan',
            'pengeluaran_makan' => 500000,
            'pengeluaran_transportasi' => 200000,
            'pengeluaran_lainnya' => 100000,
            'target_tabungan' => 5000000,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tabungan_mahasiswa', [
            'user_id' => $this->user->id,
            'nama_mahasiswa' => $this->user->name,
            'tujuan_tabungan' => 'Beli Laptop Kuliah',
        ]);
    }

    /**
     * Uji 9: Update data dan penghitungan ulang.
     */
    public function test_update_dan_hitung_ulang_data(): void
    {
        $record = TabunganMahasiswa::create([
            'user_id' => $this->user->id,
            'nama_mahasiswa' => 'Sebelum Update',
            'saldo_awal' => 1000000,
            'pemasukan_per_periode' => 2000000,
            'periode_pemasukan' => 'bulanan',
            'pengeluaran_makan' => 500000,
            'pengeluaran_transportasi' => 200000,
            'pengeluaran_lainnya' => 300000,
            'target_tabungan' => 5000000,
            'total_pengeluaran' => 1000000,
            'tabungan_per_periode' => 1000000,
            'saldo_setelah_satu_periode' => 2000000,
            'persentase_tabungan' => 50,
            'sisa_target' => 3000000,
            'jumlah_periode_tambahan' => 3,
            'status_keuangan' => 'Surplus, target dalam proses pencapaian',
        ]);

        // Perbarui pengeluaran makan menjadi lebih hemat (200.000)
        // Pengeluaran baru = 200k + 200k + 300k = 700.000
        // Tabungan baru = 2.000.000 - 700.000 = 1.300.000
        // Saldo baru = 1.000.000 + 1.300.000 = 2.300.000
        // Sisa target = 5.000.000 - 2.300.000 = 2.700.000
        // Periode tambahan = ceil(2.700.000 / 1.300.000) = 3 periode

        $response = $this->put("/tabungan/{$record->id}", [
            'nama_mahasiswa' => 'Setelah Update',
            'saldo_awal' => 1000000,
            'pemasukan_per_periode' => 2000000,
            'periode_pemasukan' => 'bulanan',
            'pengeluaran_makan' => 200000,
            'pengeluaran_transportasi' => 200000,
            'pengeluaran_lainnya' => 300000,
            'target_tabungan' => 5000000,
        ]);

        $response->assertRedirect("/tabungan/{$record->id}");

        $this->assertDatabaseHas('tabungan_mahasiswa', [
            'id' => $record->id,
            'nama_mahasiswa' => 'Setelah Update',
            'total_pengeluaran' => 700000,
            'tabungan_per_periode' => 1300000,
            'saldo_setelah_satu_periode' => 2300000,
        ]);
    }

    /**
     * Uji 10: Hapus data tabungan.
     */
    public function test_hapus_data_tabungan(): void
    {
        $record = TabunganMahasiswa::create([
            'user_id' => $this->user->id,
            'nama_mahasiswa' => 'Data Akan Dihapus',
            'saldo_awal' => 100000,
            'pemasukan_per_periode' => 500000,
            'periode_pemasukan' => 'bulanan',
            'pengeluaran_makan' => 200000,
            'pengeluaran_transportasi' => 100000,
            'pengeluaran_lainnya' => 100000,
            'target_tabungan' => 1000000,
            'total_pengeluaran' => 400000,
            'tabungan_per_periode' => 100000,
            'saldo_setelah_satu_periode' => 200000,
            'persentase_tabungan' => 20,
            'sisa_target' => 800000,
            'jumlah_periode_tambahan' => 8,
            'status_keuangan' => 'Surplus, target dalam proses pencapaian',
        ]);

        $response = $this->delete("/tabungan/{$record->id}");
        $response->assertRedirect('/tabungan');

        $this->assertDatabaseMissing('tabungan_mahasiswa', [
            'id' => $record->id,
        ]);
    }
}
