<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Pengeluaran
 *
 * Merepresentasikan tabel 'pengeluaran' pada database MySQL.
 * Menyimpan data pengeluaran mahasiswa yang dapat bersumber dari tabungan
 * atau dari luar tabungan.
 */
class Pengeluaran extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran';

    protected $fillable = [
        'user_id',
        'tabungan_mahasiswa_id',
        'nominal',
        'kategori',
        'sumber_dana',
        'keterangan',
        'tanggal_pengeluaran',
    ];

    protected $casts = [
        'nominal'             => 'decimal:2',
        'tanggal_pengeluaran' => 'date',
    ];

    // ═══════════════════════════════════════════
    //  RELASI
    // ═══════════════════════════════════════════

    /**
     * Pemilik data pengeluaran ini.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Tabungan yang menjadi sumber dana (opsional — nullable).
     */
    public function tabunganMahasiswa()
    {
        return $this->belongsTo(TabunganMahasiswa::class, 'tabungan_mahasiswa_id');
    }

    // ═══════════════════════════════════════════
    //  ACCESSOR
    // ═══════════════════════════════════════════

    /**
     * Label kategori dalam Bahasa Indonesia.
     */
    public function getKategoriLabelAttribute(): string
    {
        $label = [
            'kesehatan'          => 'Kesehatan',
            'pendidikan'         => 'Pendidikan',
            'transportasi'       => 'Transportasi',
            'perbaikan_perangkat'=> 'Perbaikan Perangkat',
            'kebutuhan_pribadi'  => 'Kebutuhan Pribadi',
            'lainnya'            => 'Lainnya',
        ];

        return $label[$this->kategori] ?? ucfirst($this->kategori);
    }

    /**
     * Label sumber dana dalam Bahasa Indonesia.
     */
    public function getSumberDanaLabelAttribute(): string
    {
        return $this->sumber_dana === 'tabungan' ? 'Dari Tabungan' : 'Luar Tabungan';
    }

    // ═══════════════════════════════════════════
    //  HELPER STATIS
    // ═══════════════════════════════════════════

    /**
     * Format angka menjadi format Rupiah.
     * Contoh: 1500000 → "Rp 1.500.000"
     */
    public static function formatRupiah($angka): string
    {
        if ($angka === null) {
            return 'Rp 0';
        }
        $tanda = $angka < 0 ? '-Rp ' : 'Rp ';
        return $tanda . number_format(abs((float) $angka), 0, ',', '.');
    }
}
