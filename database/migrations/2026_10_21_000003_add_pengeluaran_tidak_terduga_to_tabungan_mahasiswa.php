<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom pengeluaran_tidak_terduga ke tabel tabungan_mahasiswa.
     */
    public function up(): void
    {
        Schema::table('tabungan_mahasiswa', function (Blueprint $table) {
            // Kolom pengeluaran tidak terduga, diletakkan setelah pengeluaran_lainnya
            $table->decimal('pengeluaran_tidak_terduga', 15, 2)->default(0)->after('pengeluaran_lainnya');
        });
    }

    /**
     * Batalkan migrasi (hapus kolom jika di-rollback).
     */
    public function down(): void
    {
        Schema::table('tabungan_mahasiswa', function (Blueprint $table) {
            $table->dropColumn('pengeluaran_tidak_terduga');
        });
    }
};
