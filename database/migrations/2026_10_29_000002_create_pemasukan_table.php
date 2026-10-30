<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Membuat tabel pemasukan
 * Tabel ini menyimpan data pemasukan mahasiswa. Jika masuk_ke_tabungan = true,
 * nominal akan ditambahkan ke saldo tabungan_mahasiswa yang dipilih.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemasukan', function (Blueprint $table) {
            $table->id();

            // FK ke tabel users
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Nominal pemasukan (tidak boleh negatif)
            $table->decimal('nominal', 15, 2)->unsigned();

            // Kategori pemasukan — dikontrol via validasi di controller
            $table->string('kategori', 100);

            // Apakah nominal ini dimasukkan ke saldo tabungan?
            $table->boolean('masuk_ke_tabungan')->default(false);

            // FK opsional ke tabungan_mahasiswa — diisi jika masuk_ke_tabungan = true
            $table->unsignedBigInteger('tabungan_mahasiswa_id')->nullable();
            $table->foreign('tabungan_mahasiswa_id')
                  ->references('id')
                  ->on('tabungan_mahasiswa')
                  ->onDelete('set null');

            // Keterangan tambahan (opsional)
            $table->text('keterangan')->nullable();

            // Tanggal pemasukan diterima
            $table->date('tanggal_pemasukan');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemasukan');
    }
};
