<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model TabunganMahasiswa
 * 
 * Model ini merepresentasikan tabel 'tabungan_mahasiswa' pada database MySQL.
 * Digunakan untuk berinteraksi dengan data tabungan (CRUD) melalui Eloquent ORM.
 */
class TabunganMahasiswa extends Model
{
    use HasFactory;

    // Menentukan nama tabel utama secara eksplisit
    protected $table = 'tabungan_mahasiswa';

    // Kolom-kolom yang diizinkan untuk diisi secara massal (Mass Assignment)
    protected $fillable = [
        'user_id',
        'nama_mahasiswa',
        'tujuan_tabungan',
        'kategori',
        'catatan_impian',
        'saldo_awal',
        'pemasukan_per_periode',
        'periode_pemasukan',
        'pengeluaran_makan',
        'pengeluaran_transportasi',
        'pengeluaran_lainnya',
        'pengeluaran_tidak_terduga',
        'target_tabungan',
        'total_pengeluaran',
        'tabungan_per_periode',
        'saldo_setelah_satu_periode',
        'persentase_tabungan',
        'sisa_target',
        'jumlah_periode_tambahan',
        'status_keuangan',
        'tanggal_pencatatan',
    ];

    // Konversi tipe data kolom secara otomatis
    protected $casts = [
        'saldo_awal' => 'float',
        'pemasukan_per_periode' => 'float',
        'pengeluaran_makan' => 'float',
        'pengeluaran_transportasi' => 'float',
        'pengeluaran_lainnya' => 'float',
        'pengeluaran_tidak_terduga' => 'float',
        'target_tabungan' => 'float',
        'total_pengeluaran' => 'float',
        'tabungan_per_periode' => 'float',
        'saldo_setelah_satu_periode' => 'float',
        'persentase_tabungan' => 'float',
        'sisa_target' => 'float',
        'jumlah_periode_tambahan' => 'integer',
        'tanggal_pencatatan' => 'date',
    ];

    /**
     * Helper fungsi sederhana untuk memformat angka menjadi format Rupiah.
     * Contoh: 1500000 -> "Rp 1.500.000"
     */
    public static function formatRupiah($angka)
    {
        if ($angka === null) {
            return 'Rp 0';
        }
        $tanda = $angka < 0 ? '-Rp ' : 'Rp ';
        return $tanda . number_format(abs((float) $angka), 0, ',', '.');
    }

    /**
     * Helper untuk mendapatkan warna badge status keuangan pada tampilan Blade
     */
    public function getBadgeClassAttribute()
    {
        if (str_contains(strtolower($this->status_keuangan), 'tercapai')) {
            return 'bg-success text-white';
        } elseif (str_contains(strtolower($this->status_keuangan), 'defisit')) {
            return 'bg-danger text-white';
        } elseif (str_contains(strtolower($this->status_keuangan), 'impas')) {
            return 'bg-warning text-dark';
        } else {
            return 'bg-primary text-white';
        }
    }

    /**
     * Helper untuk menghitung persentase pencapaian saldo terhadap target (0 - 100%)
     * Digunakan untuk progress bar dashboard tanpa mengubah nilai saldo asli.
     */
    public function getPersentaseProgressAttribute()
    {
        if ($this->target_tabungan <= 0) {
            return 100.0;
        }

        if ($this->saldo_setelah_satu_periode <= 0) {
            return 0.0;
        }

        $persen = ($this->saldo_setelah_satu_periode / $this->target_tabungan) * 100;
        
        // Batasi progress bar maksimal 100% dan minimal 0% untuk tampilan
        return min(100.0, max(0.0, round($persen, 1)));
    }

    /**
     * Helper untuk ikon kategori tabungan
     */
    public function getKategoriIconAttribute()
    {
        $kat = strtolower($this->kategori ?? '');
        if (str_contains($kat, 'gadget') || str_contains($kat, 'laptop')) {
            return 'bi-laptop';
        } elseif (str_contains($kat, 'pendidikan') || str_contains($kat, 'kuliah') || str_contains($kat, 'ukt')) {
            return 'bi-mortarboard-fill';
        } elseif (str_contains($kat, 'darurat')) {
            return 'bi-shield-check';
        } elseif (str_contains($kat, 'motor') || str_contains($kat, 'kendaraan')) {
            return 'bi-scooter';
        } elseif (str_contains($kat, 'liburan') || str_contains($kat, 'healing')) {
            return 'bi-airplane-fill';
        } else {
            return 'bi-piggy-bank-fill';
        }
    }

    /**
     * Helper untuk badge warna kategori tabungan
     */
    public function getKategoriBadgeAttribute()
    {
        $kat = strtolower($this->kategori ?? '');
        if (str_contains($kat, 'gadget')) {
            return 'badge-cat-gadget';
        } elseif (str_contains($kat, 'pendidikan')) {
            return 'badge-cat-edu';
        } elseif (str_contains($kat, 'darurat')) {
            return 'badge-cat-emergency';
        } elseif (str_contains($kat, 'kendaraan')) {
            return 'badge-cat-transport';
        } elseif (str_contains($kat, 'liburan')) {
            return 'badge-cat-holiday';
        } else {
            return 'badge-cat-general';
        }
    }

    /**
     * Relasi ke pengguna pemilik celengan ini.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke seluruh riwayat setoran tabungan yang dimasukkan pengguna
     */
    public function riwayatTabungans()
    {
        return $this->hasMany(RiwayatTabungan::class, 'tabungan_mahasiswa_id')->orderBy('created_at', 'desc');
    }
}
