<?php

namespace Database\Seeders;

use App\Models\TabunganMahasiswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TabunganMahasiswaSeeder extends Seeder
{
    /**
     * Jalankan seeder data sampel untuk pengujian berbagai skenario keuangan.
     */
    public function run(): void
    {
        $user = User::first() ?? User::create([
            'name' => 'Demo Mahasiswa',
            'email' => 'demo@example.com',
            'password' => Hash::make('password123'),
        ]);

        $dataSampel = [
            [
                'nama_mahasiswa' => 'Budi Santoso',
                'saldo_awal' => 1000000,
                'pemasukan_per_periode' => 2000000,
                'periode_pemasukan' => 'bulanan',
                'pengeluaran_makan' => 700000,
                'pengeluaran_transportasi' => 300000,
                'pengeluaran_lainnya' => 200000,
                'target_tabungan' => 5000000,
                'tanggal_pencatatan' => now()->subDays(10)->toDateString(),
            ],
            [
                'nama_mahasiswa' => 'Siti Rahmawati',
                'saldo_awal' => 4500000,
                'pemasukan_per_periode' => 1500000,
                'periode_pemasukan' => 'bulanan',
                'pengeluaran_makan' => 600000,
                'pengeluaran_transportasi' => 250000,
                'pengeluaran_lainnya' => 150000,
                'target_tabungan' => 5000000,
                'tanggal_pencatatan' => now()->subDays(7)->toDateString(),
            ],
            [
                'nama_mahasiswa' => 'Rian Pratama',
                'saldo_awal' => 500000,
                'pemasukan_per_periode' => 1000000,
                'periode_pemasukan' => 'bulanan',
                'pengeluaran_makan' => 600000,
                'pengeluaran_transportasi' => 250000,
                'pengeluaran_lainnya' => 150000,
                'target_tabungan' => 3000000,
                'tanggal_pencatatan' => now()->subDays(4)->toDateString(),
            ],
            [
                'nama_mahasiswa' => 'Dewi Lestari',
                'saldo_awal' => 200000,
                'pemasukan_per_periode' => 1200000,
                'periode_pemasukan' => 'bulanan',
                'pengeluaran_makan' => 800000,
                'pengeluaran_transportasi' => 400000,
                'pengeluaran_lainnya' => 300000,
                'target_tabungan' => 4000000,
                'tanggal_pencatatan' => now()->subDays(2)->toDateString(),
            ],
            [
                'nama_mahasiswa' => 'Ahmad Fauzan',
                'saldo_awal' => 2500000,
                'pemasukan_per_periode' => 500000,
                'periode_pemasukan' => 'mingguan',
                'pengeluaran_makan' => 200000,
                'pengeluaran_transportasi' => 75000,
                'pengeluaran_lainnya' => 50000,
                'target_tabungan' => 6000000,
                'tanggal_pencatatan' => now()->toDateString(),
            ],
        ];

        foreach ($dataSampel as $item) {
            // Jalankan algoritma kalkulasi yang sama
            $makan = (float) $item['pengeluaran_makan'];
            $transport = (float) $item['pengeluaran_transportasi'];
            $lainnya = (float) $item['pengeluaran_lainnya'];
            $pemasukan = (float) $item['pemasukan_per_periode'];
            $saldoAwal = (float) $item['saldo_awal'];
            $target = (float) $item['target_tabungan'];

            $totalPengeluaran = $makan + $transport + $lainnya;
            $tabunganPerPeriode = $pemasukan - $totalPengeluaran;
            $saldoSetelahSatuPeriode = $saldoAwal + $tabunganPerPeriode;

            $persentase = $pemasukan > 0 ? round(($tabunganPerPeriode / $pemasukan) * 100, 2) : 0;
            $sisaTarget = max(0, $target - $saldoSetelahSatuPeriode);

            if ($saldoSetelahSatuPeriode >= $target && $target > 0) {
                $status = 'Target tabungan telah tercapai';
                $periodeTambahan = 0;
            } elseif ($tabunganPerPeriode == 0) {
                $status = 'Impas, belum ada penambahan tabungan';
                $periodeTambahan = null;
            } elseif ($tabunganPerPeriode < 0) {
                $status = 'Defisit, pengeluaran melebihi pemasukan';
                $periodeTambahan = null;
            } else {
                $status = 'Surplus, target dalam proses pencapaian';
                $periodeTambahan = (int) ceil($sisaTarget / $tabunganPerPeriode);
            }

            TabunganMahasiswa::create(array_merge($item, [
                'user_id' => $user->id,
                'total_pengeluaran' => $totalPengeluaran,
                'tabungan_per_periode' => $tabunganPerPeriode,
                'saldo_setelah_satu_periode' => $saldoSetelahSatuPeriode,
                'persentase_tabungan' => $persentase,
                'sisa_target' => $sisaTarget,
                'jumlah_periode_tambahan' => $periodeTambahan,
                'status_keuangan' => $status,
            ]));
        }
    }
}
