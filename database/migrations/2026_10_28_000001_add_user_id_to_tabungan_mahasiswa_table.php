<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom user_id ke tabel tabungan_mahasiswa
     * untuk mengikat setiap celengan ke pemiliknya (pengguna yang login).
     *
     * Kolom dibuat nullable agar data lama yang sudah ada sebelum fitur
     * multi-user tetap aman dan tidak terhapus.
     */
    public function up(): void
    {
        Schema::table('tabungan_mahasiswa', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
        });
    }

    /**
     * Batalkan migrasi (hapus kolom user_id).
     */
    public function down(): void
    {
        Schema::table('tabungan_mahasiswa', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
