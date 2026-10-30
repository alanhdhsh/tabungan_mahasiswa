<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom user_id ke tabel riwayat_tabungan
     * agar setiap transaksi dapat difilter langsung berdasarkan pemilik.
     *
     * Meskipun riwayat sudah terkait ke tabungan_mahasiswa via foreign key,
     * kolom user_id di sini memudahkan query langsung tanpa join.
     * Kolom nullable untuk menjaga data lama.
     */
    public function up(): void
    {
        Schema::table('riwayat_tabungan', function (Blueprint $table) {
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
        Schema::table('riwayat_tabungan', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
