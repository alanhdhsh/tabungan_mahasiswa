<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Membuat tabel target_tabungan
 * Tabel ini menyimpan target/tujuan menabung mahasiswa beserta progres pencapaiannya.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('target_tabungan', function (Blueprint $table) {
            $table->id();

            // FK ke tabel users
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Nama/label target tabungan
            $table->string('nama_target', 150);

            // Nominal yang ingin dicapai
            $table->decimal('nominal_target', 15, 2);

            // Dana yang sudah terkumpul (bertambah seiring waktu)
            $table->decimal('dana_terkumpul', 15, 2)->default(0);

            // Batas waktu target (opsional)
            $table->date('tanggal_target')->nullable();

            // Keterangan/deskripsi target (opsional)
            $table->text('keterangan')->nullable();

            // Status target: aktif, tercapai, atau dibatalkan
            $table->enum('status', ['aktif', 'tercapai', 'dibatalkan'])->default('aktif');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('target_tabungan');
    }
};
