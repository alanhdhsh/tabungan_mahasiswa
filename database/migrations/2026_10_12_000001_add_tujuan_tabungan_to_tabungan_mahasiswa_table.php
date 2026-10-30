<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tabungan_mahasiswa', function (Blueprint $table) {
            $table->string('tujuan_tabungan', 150)->nullable()->default('Tabungan Masa Depan')->after('nama_mahasiswa');
            $table->string('kategori', 50)->nullable()->default('Umum')->after('tujuan_tabungan');
            $table->text('catatan_impian')->nullable()->after('status_keuangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tabungan_mahasiswa', function (Blueprint $table) {
            $table->dropColumn(['tujuan_tabungan', 'kategori', 'catatan_impian']);
        });
    }
};
