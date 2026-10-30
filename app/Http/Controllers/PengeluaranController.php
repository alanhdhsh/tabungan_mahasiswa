<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use App\Models\TabunganMahasiswa;
use App\Models\RiwayatTabungan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller PengeluaranController
 *
 * Mengelola CRUD data pengeluaran mahasiswa.
 * Jika sumber dana berasal dari tabungan, saldo tabungan_mahasiswa akan diperbarui
 * dan dicatat di riwayat_tabungan secara atomik menggunakan DB::transaction().
 *
 * KEAMANAN: Semua query difilter berdasarkan user_id = auth()->id().
 */
class PengeluaranController extends Controller
{
    /**
     * Daftar pengeluaran milik pengguna yang sedang login.
     * Mendukung filter: kategori, sumber_dana, bulan, tahun.
     */
    public function index(Request $request)
    {
        $userId = auth()->id();
        $bulanIni = now()->month;
        $tahunIni = now()->year;

        $query = Pengeluaran::where('user_id', $userId);

        // Filter kategori
        if ($request->filled('kategori') && $request->kategori !== 'semua') {
            $query->where('kategori', $request->kategori);
        }

        // Filter sumber dana
        if ($request->filled('sumber_dana') && $request->sumber_dana !== 'semua') {
            $query->where('sumber_dana', $request->sumber_dana);
        }

        // Filter bulan & tahun (default: bulan ini)
        $filterBulan = $request->filled('bulan') ? (int) $request->bulan : $bulanIni;
        $filterTahun = $request->filled('tahun') ? (int) $request->tahun : $tahunIni;

        $query->whereMonth('tanggal_pengeluaran', $filterBulan)
              ->whereYear('tanggal_pengeluaran', $filterTahun);

        $pengeluarans = $query->orderBy('tanggal_pengeluaran', 'desc')
                              ->orderBy('created_at', 'desc')
                              ->paginate(15);

        // Total pengeluaran bulan yang difilter
        $totalPengeluaran = Pengeluaran::where('user_id', $userId)
            ->whereMonth('tanggal_pengeluaran', $filterBulan)
            ->whereYear('tanggal_pengeluaran', $filterTahun)
            ->sum('nominal');

        return view('pengeluaran.index', compact(
            'pengeluarans',
            'totalPengeluaran',
            'filterBulan',
            'filterTahun'
        ));
    }

    /**
     * Tampilkan form tambah pengeluaran.
     * Menyertakan daftar tabungan milik user untuk dropdown sumber dana.
     */
    public function create()
    {
        $tabungans = TabunganMahasiswa::where('user_id', auth()->id())
            ->orderBy('tujuan_tabungan')
            ->get();

        return view('pengeluaran.create', compact('tabungans'));
    }

    /**
     * Simpan data pengeluaran baru.
     * Jika sumber_dana = 'tabungan': kurangi saldo tabungan & catat riwayat.
     * Menggunakan DB::transaction() untuk menjamin konsistensi data.
     */
    public function store(Request $request)
    {
        $validated = $this->validasiInput($request);

        // Pastikan tabungan_mahasiswa_id milik user yang sedang login
        if ($validated['sumber_dana'] === 'tabungan' && !empty($validated['tabungan_mahasiswa_id'])) {
            $this->pastikanTabunganMilikUser($validated['tabungan_mahasiswa_id']);
        }

        DB::transaction(function () use ($validated) {
            // Simpan data pengeluaran
            $pengeluaran = Pengeluaran::create(array_merge($validated, [
                'user_id' => auth()->id(),
            ]));

            // Jika sumber dana dari tabungan, kurangi saldo
            if ($pengeluaran->sumber_dana === 'tabungan' && $pengeluaran->tabungan_mahasiswa_id) {
                $tabungan = TabunganMahasiswa::where('id', $pengeluaran->tabungan_mahasiswa_id)
                    ->where('user_id', auth()->id())
                    ->lockForUpdate()
                    ->firstOrFail();

                $saldoSebelumnya = $tabungan->saldo_setelah_satu_periode;
                $saldoBaru       = $saldoSebelumnya - (float) $pengeluaran->nominal;

                $tabungan->update([
                    'saldo_setelah_satu_periode' => $saldoBaru,
                    'saldo_awal'                 => $saldoBaru,
                ]);

                // Catat di riwayat_tabungan
                RiwayatTabungan::create([
                    'user_id'               => auth()->id(),
                    'tabungan_mahasiswa_id' => $tabungan->id,
                    'nominal'               => $pengeluaran->nominal,
                    'jenis'                 => 'pengeluaran',
                    'saldo_sebelumnya'      => $saldoSebelumnya,
                    'saldo_sesudahnya'      => $saldoBaru,
                    'catatan'               => '[pengeluaran_id:' . $pengeluaran->id . '] ' .
                                              ($pengeluaran->keterangan ?? 'Pengeluaran kategori ' . $pengeluaran->kategori_label),
                    'tanggal_transaksi'     => $pengeluaran->tanggal_pengeluaran,
                ]);
            }
        });

        return redirect()->route('pengeluaran.index')
            ->with('sukses', 'Data pengeluaran berhasil disimpan.');
    }

    /**
     * Tampilkan form edit pengeluaran.
     * Memastikan data milik pengguna yang sedang login.
     */
    public function edit($id)
    {
        $pengeluaran = $this->getPengeluaranMilikUser($id);
        $tabungans   = TabunganMahasiswa::where('user_id', auth()->id())
            ->orderBy('tujuan_tabungan')
            ->get();

        return view('pengeluaran.edit', compact('pengeluaran', 'tabungans'));
    }

    /**
     * Perbarui data pengeluaran.
     * Jika sumber_dana atau nominal berubah, reverse efek lama dan terapkan efek baru.
     */
    public function update(Request $request, $id)
    {
        $pengeluaran = $this->getPengeluaranMilikUser($id);
        $validated   = $this->validasiInput($request);

        if ($validated['sumber_dana'] === 'tabungan' && !empty($validated['tabungan_mahasiswa_id'])) {
            $this->pastikanTabunganMilikUser($validated['tabungan_mahasiswa_id']);
        }

        DB::transaction(function () use ($pengeluaran, $validated) {
            $nominalLama       = (float) $pengeluaran->nominal;
            $sumberDanaLama    = $pengeluaran->sumber_dana;
            $tabunganIdLama    = $pengeluaran->tabungan_mahasiswa_id;
            $nominalBaru       = (float) $validated['nominal'];
            $sumberDanaBaru    = $validated['sumber_dana'];
            $tabunganIdBaru    = $validated['tabungan_mahasiswa_id'] ?? null;

            // ── 1. Reverse efek lama jika perlu ──────────────────────────────
            if ($sumberDanaLama === 'tabungan' && $tabunganIdLama) {
                $tabunganLama = TabunganMahasiswa::where('id', $tabunganIdLama)
                    ->where('user_id', auth()->id())
                    ->lockForUpdate()
                    ->first();

                if ($tabunganLama) {
                    $saldoReverse = $tabunganLama->saldo_setelah_satu_periode + $nominalLama;
                    $tabunganLama->update([
                        'saldo_setelah_satu_periode' => $saldoReverse,
                        'saldo_awal'                 => $saldoReverse,
                    ]);

                    // Hapus entri riwayat yang terkait dengan pengeluaran lama
                    RiwayatTabungan::where('tabungan_mahasiswa_id', $tabunganIdLama)
                        ->where('user_id', auth()->id())
                        ->where('jenis', 'pengeluaran')
                        ->where('catatan', 'like', '%[pengeluaran_id:' . $pengeluaran->id . ']%')
                        ->delete();
                }
            }

            // ── 2. Update data pengeluaran ────────────────────────────────────
            $pengeluaran->update(array_merge($validated, ['user_id' => auth()->id()]));

            // ── 3. Terapkan efek baru jika perlu ─────────────────────────────
            if ($sumberDanaBaru === 'tabungan' && $tabunganIdBaru) {
                $tabunganBaru = TabunganMahasiswa::where('id', $tabunganIdBaru)
                    ->where('user_id', auth()->id())
                    ->lockForUpdate()
                    ->firstOrFail();

                $saldoSebelumnya = $tabunganBaru->saldo_setelah_satu_periode;
                $saldoBaru       = $saldoSebelumnya - $nominalBaru;

                $tabunganBaru->update([
                    'saldo_setelah_satu_periode' => $saldoBaru,
                    'saldo_awal'                 => $saldoBaru,
                ]);

                RiwayatTabungan::create([
                    'user_id'               => auth()->id(),
                    'tabungan_mahasiswa_id' => $tabunganBaru->id,
                    'nominal'               => $nominalBaru,
                    'jenis'                 => 'pengeluaran',
                    'saldo_sebelumnya'      => $saldoSebelumnya,
                    'saldo_sesudahnya'      => $saldoBaru,
                    'catatan'               => '[pengeluaran_id:' . $pengeluaran->id . '] ' .
                                              ($pengeluaran->keterangan ?? 'Pengeluaran kategori ' . $pengeluaran->kategori_label),
                    'tanggal_transaksi'     => $pengeluaran->tanggal_pengeluaran,
                ]);
            }
        });

        return redirect()->route('pengeluaran.index')
            ->with('sukses', 'Data pengeluaran berhasil diperbarui.');
    }

    /**
     * Hapus data pengeluaran.
     * Jika sumber_dana = 'tabungan', kembalikan saldo tabungan & hapus riwayat terkait.
     */
    public function destroy($id)
    {
        $pengeluaran = $this->getPengeluaranMilikUser($id);

        DB::transaction(function () use ($pengeluaran) {
            // Kembalikan saldo tabungan jika pengeluaran bersumber dari tabungan
            if ($pengeluaran->sumber_dana === 'tabungan' && $pengeluaran->tabungan_mahasiswa_id) {
                $tabungan = TabunganMahasiswa::where('id', $pengeluaran->tabungan_mahasiswa_id)
                    ->where('user_id', auth()->id())
                    ->lockForUpdate()
                    ->first();

                if ($tabungan) {
                    $saldoBaru = $tabungan->saldo_setelah_satu_periode + (float) $pengeluaran->nominal;
                    $tabungan->update([
                        'saldo_setelah_satu_periode' => $saldoBaru,
                        'saldo_awal'                 => $saldoBaru,
                    ]);

                    // Hapus entri riwayat yang terkait dengan pengeluaran ini
                    RiwayatTabungan::where('tabungan_mahasiswa_id', $pengeluaran->tabungan_mahasiswa_id)
                        ->where('user_id', auth()->id())
                        ->where('jenis', 'pengeluaran')
                        ->where('catatan', 'like', '%[pengeluaran_id:' . $pengeluaran->id . ']%')
                        ->delete();
                }
            }

            $pengeluaran->delete();
        });

        return redirect()->route('pengeluaran.index')
            ->with('sukses', 'Data pengeluaran berhasil dihapus.');
    }

    // ═══════════════════════════════════════════
    //  PRIVATE HELPERS
    // ═══════════════════════════════════════════

    /**
     * Ambil data pengeluaran milik user yang sedang login atau abort 403.
     */
    private function getPengeluaranMilikUser($id): Pengeluaran
    {
        $pengeluaran = Pengeluaran::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (! $pengeluaran) {
            abort(403, 'Kamu tidak memiliki akses ke data pengeluaran ini.');
        }

        return $pengeluaran;
    }

    /**
     * Pastikan tabungan_mahasiswa_id milik user yang sedang login.
     */
    private function pastikanTabunganMilikUser($tabunganId): void
    {
        $ada = TabunganMahasiswa::where('id', $tabunganId)
            ->where('user_id', auth()->id())
            ->exists();

        if (! $ada) {
            abort(403, 'Tabungan yang dipilih bukan milikmu.');
        }
    }

    /**
     * Aturan validasi input pengeluaran dengan pesan Bahasa Indonesia.
     */
    private function validasiInput(Request $request): array
    {
        return $request->validate([
            'nominal'               => 'required|numeric|min:1',
            'kategori'              => 'required|in:kesehatan,pendidikan,transportasi,perbaikan_perangkat,kebutuhan_pribadi,lainnya',
            'sumber_dana'           => 'required|in:tabungan,luar_tabungan',
            'tabungan_mahasiswa_id' => 'required_if:sumber_dana,tabungan|nullable|exists:tabungan_mahasiswa,id',
            'keterangan'            => 'nullable|string|max:500',
            'tanggal_pengeluaran'   => 'required|date',
        ], [
            'nominal.required'               => 'Nominal pengeluaran wajib diisi.',
            'nominal.numeric'                => 'Nominal pengeluaran harus berupa angka.',
            'nominal.min'                    => 'Nominal pengeluaran minimal Rp 1.',
            'kategori.required'              => 'Kategori pengeluaran wajib dipilih.',
            'kategori.in'                    => 'Kategori yang dipilih tidak valid.',
            'sumber_dana.required'           => 'Sumber dana wajib dipilih.',
            'sumber_dana.in'                 => 'Sumber dana yang dipilih tidak valid.',
            'tabungan_mahasiswa_id.required_if' => 'Pilih celengan tabungan yang menjadi sumber dana.',
            'tabungan_mahasiswa_id.exists'   => 'Celengan tabungan yang dipilih tidak ditemukan.',
            'keterangan.max'                 => 'Keterangan maksimal 500 karakter.',
            'tanggal_pengeluaran.required'   => 'Tanggal pengeluaran wajib diisi.',
            'tanggal_pengeluaran.date'       => 'Format tanggal pengeluaran tidak valid.',
        ]);
    }
}
