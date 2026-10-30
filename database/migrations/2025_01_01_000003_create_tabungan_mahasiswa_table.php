<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk membuat tabel tabungan_mahasiswa.
     */
    public function up(): void
    {
        Schema::create('tabungan_mahasiswa', function (Blueprint $table) {
            $table->id();
            
            // Data Input Mahasiswa
            $table->string('nama_mahasiswa', 150);
            $table->decimal('saldo_awal', 15, 2)->default(0);
            $table->decimal('pemasukan_per_periode', 15, 2)->default(0);
            $table->enum('periode_pemasukan', ['harian', 'mingguan', 'bulanan'])->default('bulanan');
            $table->decimal('pengeluaran_makan', 15, 2)->default(0);
            $table->decimal('pengeluaran_transportasi', 15, 2)->default(0);
            $table->decimal('pengeluaran_lainnya', 15, 2)->default(0);
            $table->decimal('target_tabungan', 15, 2)->default(0);
            
            // Hasil Perhitungan Otomatis
            $table->decimal('total_pengeluaran', 15, 2)->default(0);
            $table->decimal('tabungan_per_periode', 15, 2)->default(0);
            $table->decimal('saldo_setelah_satu_periode', 15, 2)->default(0);
            $table->decimal('persentase_tabungan', 8, 2)->default(0);
            $table->decimal('sisa_target', 15, 2)->default(0);
            $table->integer('jumlah_periode_tambahan')->nullable()->default(0);
            $table->string('status_keuangan', 100);
            $table->date('tanggal_pencatatan')->nullable();
            
            // Timestamps bawaan Laravel (created_at & updated_at)
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi (drop tabel jika di-rollback).
     */
    public function down(): void
    {
        Schema::dropIfExists('tabungan_mahasiswa');
    }
};
