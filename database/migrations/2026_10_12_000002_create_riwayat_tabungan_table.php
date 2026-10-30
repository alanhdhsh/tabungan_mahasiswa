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
        Schema::create('riwayat_tabungan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tabungan_mahasiswa_id')->constrained('tabungan_mahasiswa')->onDelete('cascade');
            $table->decimal('nominal', 15, 2);
            $table->string('jenis', 50)->default('setoran_tambahan');
            $table->decimal('saldo_sebelumnya', 15, 2)->default(0);
            $table->decimal('saldo_sesudahnya', 15, 2)->default(0);
            $table->string('catatan', 255)->nullable();
            $table->dateTime('tanggal_transaksi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_tabungan');
    }
};
