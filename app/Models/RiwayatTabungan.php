<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model RiwayatTabungan
 * 
 * Mencatat seluruh histori transaksi setoran tabungan yang dimasukkan oleh pengguna.
 */
class RiwayatTabungan extends Model
{
    use HasFactory;

    protected $table = 'riwayat_tabungan';

    protected $fillable = [
        'user_id',
        'tabungan_mahasiswa_id',
        'nominal',
        'jenis',
        'saldo_sebelumnya',
        'saldo_sesudahnya',
        'catatan',
        'tanggal_transaksi',
    ];

    protected $casts = [
        'nominal' => 'float',
        'saldo_sebelumnya' => 'float',
        'saldo_sesudahnya' => 'float',
        'tanggal_transaksi' => 'datetime',
    ];

    /**
     * Relasi balik ke model TabunganMahasiswa
     */
    public function tabunganMahasiswa()
    {
        return $this->belongsTo(TabunganMahasiswa::class, 'tabungan_mahasiswa_id');
    }

    /**
     * Relasi ke pengguna pemilik transaksi ini.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper untuk format nominal rupiah
     */
    public function getFormatNominalAttribute()
    {
        return TabunganMahasiswa::formatRupiah($this->nominal);
    }

    /**
     * Label jenis setoran tabungan yang ramah pengguna
     */
    public function getJenisLabelAttribute()
    {
        switch ($this->jenis) {
            case 'setoran_awal':
                return 'Setoran Awal';
            case 'setoran_tambahan':
                return 'Setor Tabungan';
            case 'alokasi_rutin':
                return 'Alokasi Rutin';
            case 'penarikan':
                return 'Penarikan Dana';
            case 'edit_data':
                return 'Perubahan Data';
            default:
                return 'Transaksi';
        }
    }

    /**
     * Badge CSS untuk jenis setoran
     */
    public function getJenisBadgeAttribute()
    {
        switch ($this->jenis) {
            case 'setoran_awal':
                return 'bg-primary text-white';
            case 'setoran_tambahan':
                return 'bg-success text-white';
            case 'alokasi_rutin':
                return 'bg-info text-dark';
            case 'penarikan':
                return 'bg-danger text-white';
            case 'edit_data':
                return 'bg-warning text-dark';
            default:
                return 'bg-secondary text-white';
        }
    }

    /**
     * Ikon Bootstrap Icons untuk setiap jenis transaksi
     */
    public function getJenisIconAttribute()
    {
        switch ($this->jenis) {
            case 'setoran_awal':
                return 'bi-wallet2';
            case 'setoran_tambahan':
                return 'bi-cash-coin';
            case 'alokasi_rutin':
                return 'bi-arrow-repeat';
            case 'penarikan':
                return 'bi-box-arrow-up';
            case 'edit_data':
                return 'bi-pencil-square';
            default:
                return 'bi-receipt';
        }
    }

    /**
     * Apakah transaksi ini menambah saldo (true) atau mengurangi (false)
     */
    public function getIsPositifAttribute()
    {
        return !in_array($this->jenis, ['penarikan']);
    }

    /**
     * Format saldo sebelumnya
     */
    public function getFormatSaldoSebelumnyaAttribute()
    {
        return TabunganMahasiswa::formatRupiah($this->saldo_sebelumnya);
    }

    /**
     * Format saldo sesudahnya
     */
    public function getFormatSaldoSesudahnyaAttribute()
    {
        return TabunganMahasiswa::formatRupiah($this->saldo_sesudahnya);
    }
}
