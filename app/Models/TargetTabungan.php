<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model TargetTabungan
 *
 * Merepresentasikan tabel 'target_tabungan' pada database MySQL.
 * Menyimpan target/tujuan menabung mahasiswa beserta progres dana terkumpul.
 */
class TargetTabungan extends Model
{
    use HasFactory;

    protected $table = 'target_tabungan';

    protected $fillable = [
        'user_id',
        'nama_target',
        'nominal_target',
        'dana_terkumpul',
        'tanggal_target',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'nominal_target' => 'decimal:2',
        'dana_terkumpul' => 'decimal:2',
        'tanggal_target' => 'date',
    ];

    // ═══════════════════════════════════════════
    //  RELASI
    // ═══════════════════════════════════════════

    /**
     * Pemilik target tabungan ini.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ═══════════════════════════════════════════
    //  ACCESSOR
    // ═══════════════════════════════════════════

    /**
     * Persentase pencapaian target (0–100, dibatasi agar tidak melebihi 100).
     */
    public function getPersentasePencapaianAttribute(): float
    {
        $target = (float) $this->nominal_target;

        if ($target <= 0) {
            return 100.0;
        }

        $persen = ((float) $this->dana_terkumpul / $target) * 100;

        return min(100.0, max(0.0, round($persen, 2)));
    }

    /**
     * Kekurangan dana yang masih dibutuhkan untuk mencapai target.
     */
    public function getKekuranganDanaAttribute(): float
    {
        return max(0.0, (float) $this->nominal_target - (float) $this->dana_terkumpul);
    }

    /**
     * Label status dalam Bahasa Indonesia.
     */
    public function getStatusLabelAttribute(): string
    {
        $label = [
            'aktif'      => 'Aktif',
            'tercapai'   => 'Tercapai',
            'dibatalkan' => 'Dibatalkan',
        ];

        return $label[$this->status] ?? ucfirst($this->status);
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
