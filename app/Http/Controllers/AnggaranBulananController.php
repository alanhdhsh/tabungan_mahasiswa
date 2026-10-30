<?php

namespace App\Http\Controllers;

use App\Models\AnggaranBulanan;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;

/**
 * Controller AnggaranBulananController
 *
 * Mengelola anggaran pengeluaran bulanan milik mahasiswa.
 * Membandingkan realisasi pengeluaran dari tabel pengeluaran
 * terhadap batas anggaran yang telah ditetapkan.
 *
 * KEAMANAN: Semua query difilter berdasarkan user_id = auth()->id().
 */
class AnggaranBulananController extends Controller
{
    /**
     * Tampilkan ringkasan anggaran bulan ini beserta realisasi per kategori.
     */
    public function index()
    {
        $userId   = auth()->id();
        $bulanIni = now()->month;
        $tahunIni = now()->year;

        // Ambil anggaran bulan ini (jika belum ada, null)
        $anggaran = AnggaranBulanan::where('user_id', $userId)
            ->where('bulan', $bulanIni)
            ->where('tahun', $tahunIni)
            ->first();

        // Hitung total pengeluaran bulan ini dari tabel pengeluaran
        $totalPengeluaran = Pengeluaran::where('user_id', $userId)
            ->whereMonth('tanggal_pengeluaran', $bulanIni)
            ->whereYear('tanggal_pengeluaran', $tahunIni)
            ->sum('nominal');

        // Hitung pengeluaran per kategori bulan ini
        $pengeluaranPerKategori = Pengeluaran::where('user_id', $userId)
            ->whereMonth('tanggal_pengeluaran', $bulanIni)
            ->whereYear('tanggal_pengeluaran', $tahunIni)
            ->selectRaw('kategori, SUM(nominal) as total')
            ->groupBy('kategori')
            ->pluck('total', 'kategori')
            ->toArray();

        // Hitung status & sisa anggaran (hanya jika anggaran sudah dibuat)
        $sisaAnggaran     = $anggaran ? $anggaran->getSisaAnggaran($totalPengeluaran) : null;
        $statusAnggaran   = $anggaran ? $anggaran->getStatusAnggaran($totalPengeluaran, $anggaran->batas_peringatan_persen) : null;
        $persenPenggunaan = $anggaran ? $anggaran->getPenggunaanPersen($totalPengeluaran) : null;

        return view('anggaran.index', compact(
            'anggaran',
            'totalPengeluaran',
            'pengeluaranPerKategori',
            'sisaAnggaran',
            'statusAnggaran',
            'persenPenggunaan',
            'bulanIni',
            'tahunIni'
        ));
    }

    /**
     * Tampilkan form edit/buat anggaran bulan ini.
     */
    public function editBulanIni()
    {
        $userId   = auth()->id();
        $bulanIni = now()->month;
        $tahunIni = now()->year;

        // Ambil atau inisialisasi anggaran kosong untuk bulan ini
        $anggaran = AnggaranBulanan::where('user_id', $userId)
            ->where('bulan', $bulanIni)
            ->where('tahun', $tahunIni)
            ->first();

        return view('anggaran.edit', compact('anggaran', 'bulanIni', 'tahunIni'));
    }

    /**
     * Simpan atau perbarui anggaran bulan ini menggunakan updateOrCreate.
     */
    public function simpanBulanIni(Request $request)
    {
        $userId   = auth()->id();
        $bulanIni = now()->month;
        $tahunIni = now()->year;

        $validated = $request->validate([
            'batas_total'               => 'required|numeric|min:0',
            'batas_kesehatan'           => 'nullable|numeric|min:0',
            'batas_pendidikan'          => 'nullable|numeric|min:0',
            'batas_transportasi'        => 'nullable|numeric|min:0',
            'batas_perbaikan_perangkat' => 'nullable|numeric|min:0',
            'batas_kebutuhan_pribadi'   => 'nullable|numeric|min:0',
            'batas_lainnya'             => 'nullable|numeric|min:0',
            'batas_peringatan_persen'   => 'nullable|integer|min:1|max:100',
        ], [
            'batas_total.required'    => 'Batas total anggaran wajib diisi.',
            'batas_total.numeric'     => 'Batas total anggaran harus berupa angka.',
            'batas_total.min'         => 'Batas total anggaran tidak boleh negatif.',
            'batas_kesehatan.min'     => 'Batas anggaran kesehatan tidak boleh negatif.',
            'batas_pendidikan.min'    => 'Batas anggaran pendidikan tidak boleh negatif.',
            'batas_transportasi.min'  => 'Batas anggaran transportasi tidak boleh negatif.',
            'batas_perbaikan_perangkat.min' => 'Batas anggaran perbaikan perangkat tidak boleh negatif.',
            'batas_kebutuhan_pribadi.min'   => 'Batas anggaran kebutuhan pribadi tidak boleh negatif.',
            'batas_lainnya.min'       => 'Batas anggaran lainnya tidak boleh negatif.',
            'batas_peringatan_persen.min'   => 'Persentase peringatan minimal 1%.',
            'batas_peringatan_persen.max'   => 'Persentase peringatan maksimal 100%.',
        ]);

        AnggaranBulanan::updateOrCreate(
            [
                'user_id' => $userId,
                'bulan'   => $bulanIni,
                'tahun'   => $tahunIni,
            ],
            array_merge($validated, [
                'batas_peringatan_persen' => $validated['batas_peringatan_persen'] ?? 80,
            ])
        );

        return redirect()->route('anggaran.index')
            ->with('sukses', 'Anggaran bulan ini berhasil disimpan.');
    }

    /**
     * Tampilkan detail anggaran bulan tertentu.
     */
    public function show($bulan, $tahun)
    {
        $userId = auth()->id();
        $bulan  = (int) $bulan;
        $tahun  = (int) $tahun;

        $anggaran = AnggaranBulanan::where('user_id', $userId)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->firstOrFail();

        // Hitung total pengeluaran untuk bulan tersebut
        $totalPengeluaran = Pengeluaran::where('user_id', $userId)
            ->whereMonth('tanggal_pengeluaran', $bulan)
            ->whereYear('tanggal_pengeluaran', $tahun)
            ->sum('nominal');

        // Pengeluaran per kategori untuk bulan tersebut
        $pengeluaranPerKategori = Pengeluaran::where('user_id', $userId)
            ->whereMonth('tanggal_pengeluaran', $bulan)
            ->whereYear('tanggal_pengeluaran', $tahun)
            ->selectRaw('kategori, SUM(nominal) as total')
            ->groupBy('kategori')
            ->pluck('total', 'kategori')
            ->toArray();

        $sisaAnggaran     = $anggaran->getSisaAnggaran($totalPengeluaran);
        $statusAnggaran   = $anggaran->getStatusAnggaran($totalPengeluaran, $anggaran->batas_peringatan_persen);
        $persenPenggunaan = $anggaran->getPenggunaanPersen($totalPengeluaran);

        return view('anggaran.show', compact(
            'anggaran',
            'totalPengeluaran',
            'pengeluaranPerKategori',
            'sisaAnggaran',
            'statusAnggaran',
            'persenPenggunaan',
            'bulan',
            'tahun'
        ));
    }
}
