<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Pemasukan
 *
 * Merepresentasikan tabel 'pemasukan' pada database MySQL.
 * Menyimpan data pemasukan mahasiswa. Jika masuk_ke_tabungan = true,
 * nominal ditambahkan ke saldo tabungan_mahasiswa yang dipilih.
 */
class Pemasukan extends Model
{
    use HasFactory;

    protected $table = 'pemasukan';

    protected $fillable = [
        'user_id',
        'nominal',
        'kategori',
        'masuk_ke_tabungan',
        'tabungan_mahasiswa_id',
        'keterangan',
        'tanggal_pemasukan',
    ];

    protected $casts = [
        'nominal'            => 'decimal:2',
        'masuk_ke_tabungan'  => 'boolean',
        'tanggal_pemasukan'  => 'date',
    ];

    // ═══════════════════════════════════════════
    //  RELASI
    // ═══════════════════════════════════════════

    /**
     * Pemilik data pemasukan ini.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Tabungan tujuan alokasi (opsional — nullable).
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
            'uang_saku'         => 'Uang Saku',
            'uang_bulanan'      => 'Uang Bulanan',
            'kerja_paruh_waktu' => 'Kerja Paruh Waktu',
            'beasiswa'          => 'Beasiswa',
            'lainnya'           => 'Lainnya',
        ];

        return $label[$this->kategori] ?? ucfirst($this->kategori);
    }

    // ═══════════════════════════════════════════
    //  HELPER STATIS
    // ═══════════════════════════════════════════

    /**
     * Format angka menjadi format Rupiah.
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
