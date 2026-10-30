<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Membuat tabel anggaran_bulanan
 * Tabel ini menyimpan batas anggaran per kategori pengeluaran untuk setiap bulan.
 * Setiap user hanya boleh memiliki satu anggaran per bulan (unique constraint).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggaran_bulanan', function (Blueprint $table) {
            $table->id();

            // FK ke tabel users
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Bulan (1-12)
            $table->tinyInteger('bulan')->unsigned();

            // Tahun (4 digit)
            $table->integer('tahun');

            // Batas total pengeluaran dalam sebulan
            $table->decimal('batas_total', 15, 2)->default(0);

            // Batas per kategori (opsional — jika null, tidak ada batas khusus kategori ini)
            $table->decimal('batas_kesehatan', 15, 2)->default(0)->nullable();
            $table->decimal('batas_pendidikan', 15, 2)->default(0)->nullable();
            $table->decimal('batas_transportasi', 15, 2)->default(0)->nullable();
            $table->decimal('batas_perbaikan_perangkat', 15, 2)->default(0)->nullable();
            $table->decimal('batas_kebutuhan_pribadi', 15, 2)->default(0)->nullable();
            $table->decimal('batas_lainnya', 15, 2)->default(0)->nullable();

            // Persentase ambang batas peringatan (default 80%)
            $table->tinyInteger('batas_peringatan_persen')->default(80);

            $table->timestamps();

            // Satu user hanya boleh punya satu anggaran per bulan per tahun
            $table->unique(['user_id', 'bulan', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggaran_bulanan');
    }
};
