<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Membuat tabel pengeluaran
 * Tabel ini menyimpan data pengeluaran mahasiswa yang dapat bersumber
 * dari dana tabungan atau dari luar tabungan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengeluaran', function (Blueprint $table) {
            $table->id();

            // FK ke tabel users — wajib ada pemilik
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // FK opsional ke tabungan_mahasiswa — diisi jika sumber dana dari tabungan
            $table->unsignedBigInteger('tabungan_mahasiswa_id')->nullable();
            $table->foreign('tabungan_mahasiswa_id')
                  ->references('id')
                  ->on('tabungan_mahasiswa')
                  ->onDelete('set null');

            // Nominal pengeluaran (tidak boleh negatif)
            $table->decimal('nominal', 15, 2)->unsigned();

            // Kategori pengeluaran — dikontrol via validasi enum di controller
            $table->string('kategori', 100);

            // Sumber dana: dari tabungan atau luar tabungan
            $table->enum('sumber_dana', ['tabungan', 'luar_tabungan']);

            // Keterangan tambahan (opsional)
            $table->text('keterangan')->nullable();

            // Tanggal pengeluaran terjadi
            $table->date('tanggal_pengeluaran');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengeluaran');
    }
};
