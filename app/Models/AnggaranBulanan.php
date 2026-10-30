<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model AnggaranBulanan
 *
 * Merepresentasikan tabel 'anggaran_bulanan' pada database MySQL.
 * Menyimpan batas anggaran pengeluaran per kategori untuk setiap bulan per user.
 */
class AnggaranBulanan extends Model
{
    use HasFactory;

    protected $table = 'anggaran_bulanan';

    protected $fillable = [
        'user_id',
        'bulan',
        'tahun',
        'batas_total',
        'batas_kesehatan',
        'batas_pendidikan',
        'batas_transportasi',
        'batas_perbaikan_perangkat',
        'batas_kebutuhan_pribadi',
        'batas_lainnya',
        'batas_peringatan_persen',
    ];

    protected $casts = [
        'batas_total'               => 'decimal:2',
        'batas_kesehatan'           => 'decimal:2',
        'batas_pendidikan'          => 'decimal:2',
        'batas_transportasi'        => 'decimal:2',
        'batas_perbaikan_perangkat' => 'decimal:2',
        'batas_kebutuhan_pribadi'   => 'decimal:2',
        'batas_lainnya'             => 'decimal:2',
    ];

    // ═══════════════════════════════════════════
    //  RELASI
    // ═══════════════════════════════════════════

    /**
     * Pemilik anggaran ini.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ═══════════════════════════════════════════
    //  METHOD KALKULASI
    // ═══════════════════════════════════════════

    /**
     * Hitung persentase penggunaan anggaran.
     *
     * @param  float $totalPengeluaran Total pengeluaran bulan ini.
     * @return float Persentase penggunaan (bisa lebih dari 100).
     */
    public function getPenggunaanPersen(float $totalPengeluaran): float
    {
        if ((float) $this->batas_total <= 0) {
            return 0.0;
        }

        return round(($totalPengeluaran / (float) $this->batas_total) * 100, 2);
    }

    /**
     * Hitung sisa anggaran bulan ini.
     *
     * @param  float $totalPengeluaran Total pengeluaran bulan ini.
     * @return float Sisa anggaran (bisa negatif jika melebihi batas).
     */
    public function getSisaAnggaran(float $totalPengeluaran): float
    {
        return (float) $this->batas_total - $totalPengeluaran;
    }

    /**
     * Dapatkan status anggaran berdasarkan persentase penggunaan.
     *
     * @param  float $totalPengeluaran  Total pengeluaran bulan ini.
     * @param  int   $batasPeringatan   Persentase ambang batas peringatan (default 80).
     * @return string 'aman' | 'peringatan' | 'terlampaui'
     */
    public function getStatusAnggaran(float $totalPengeluaran, int $batasPeringatan = 80): string
    {
        $persen = $this->getPenggunaanPersen($totalPengeluaran);

        if ($persen > 100) {
            return 'terlampaui';
        } elseif ($persen >= $batasPeringatan) {
            return 'peringatan';
        } else {
            return 'aman';
        }
    }
}
