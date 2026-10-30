<?php

namespace App\Http\Controllers;

use App\Models\Pemasukan;
use App\Models\TabunganMahasiswa;
use App\Models\RiwayatTabungan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller PemasukanController
 *
 * Mengelola CRUD data pemasukan mahasiswa.
 * Jika masuk_ke_tabungan = true, saldo tabungan_mahasiswa akan ditambah
 * dan dicatat di riwayat_tabungan secara atomik menggunakan DB::transaction().
 *
 * KEAMANAN: Semua query difilter berdasarkan user_id = auth()->id().
 */
class PemasukanController extends Controller
{
    /**
     * Daftar pemasukan milik pengguna yang sedang login.
     * Mendukung filter: kategori, bulan, tahun.
     */
    public function index(Request $request)
    {
        $userId   = auth()->id();
        $bulanIni = now()->month;
        $tahunIni = now()->year;

        $query = Pemasukan::where('user_id', $userId);

        // Filter kategori
        if ($request->filled('kategori') && $request->kategori !== 'semua') {
            $query->where('kategori', $request->kategori);
        }

        // Filter bulan & tahun (default: bulan ini)
        $filterBulan = $request->filled('bulan') ? (int) $request->bulan : $bulanIni;
        $filterTahun = $request->filled('tahun') ? (int) $request->tahun : $tahunIni;

        $query->whereMonth('tanggal_pemasukan', $filterBulan)
              ->whereYear('tanggal_pemasukan', $filterTahun);

        $pemasukans = $query->orderBy('tanggal_pemasukan', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->paginate(15);

        // Total pemasukan bulan yang difilter
        $totalPemasukan = Pemasukan::where('user_id', $userId)
            ->whereMonth('tanggal_pemasukan', $filterBulan)
            ->whereYear('tanggal_pemasukan', $filterTahun)
            ->sum('nominal');

        return view('pemasukan.index', compact(
            'pemasukans',
            'totalPemasukan',
            'filterBulan',
            'filterTahun'
        ));
    }

    /**
     * Tampilkan form tambah pemasukan.
     * Menyertakan daftar tabungan milik user untuk dropdown alokasi.
     */
    public function create()
    {
        $tabungans = TabunganMahasiswa::where('user_id', auth()->id())
            ->orderBy('tujuan_tabungan')
            ->get();

        return view('pemasukan.create', compact('tabungans'));
    }

    /**
     * Simpan data pemasukan baru.
     * Jika masuk_ke_tabungan = true: tambahkan ke saldo tabungan & catat riwayat.
     */
    public function store(Request $request)
    {
        $validated = $this->validasiInput($request);

        if (!empty($validated['tabungan_mahasiswa_id'])) {
            $this->pastikanTabunganMilikUser($validated['tabungan_mahasiswa_id']);
        }

        DB::transaction(function () use ($validated) {
            $masukKeTabungan = (bool) ($validated['masuk_ke_tabungan'] ?? false);

            // Simpan data pemasukan
            $pemasukan = Pemasukan::create(array_merge($validated, [
                'user_id'          => auth()->id(),
                'masuk_ke_tabungan' => $masukKeTabungan,
            ]));

            // Jika pemasukan dialokasikan ke tabungan, tambahkan saldo
            if ($masukKeTabungan && $pemasukan->tabungan_mahasiswa_id) {
                $tabungan = TabunganMahasiswa::where('id', $pemasukan->tabungan_mahasiswa_id)
                    ->where('user_id', auth()->id())
                    ->lockForUpdate()
                    ->firstOrFail();

                $saldoSebelumnya = $tabungan->saldo_setelah_satu_periode;
                $saldoBaru       = $saldoSebelumnya + (float) $pemasukan->nominal;

                $tabungan->update([
                    'saldo_setelah_satu_periode' => $saldoBaru,
                    'saldo_awal'                 => $saldoBaru,
                ]);

                // Catat di riwayat_tabungan
                RiwayatTabungan::create([
                    'user_id'               => auth()->id(),
                    'tabungan_mahasiswa_id' => $tabungan->id,
                    'nominal'               => $pemasukan->nominal,
                    'jenis'                 => 'setoran_tambahan',
                    'saldo_sebelumnya'      => $saldoSebelumnya,
                    'saldo_sesudahnya'      => $saldoBaru,
                    'catatan'               => '[pemasukan_id:' . $pemasukan->id . '] ' .
                                              ($pemasukan->keterangan ?? 'Pemasukan kategori ' . $pemasukan->kategori_label . ' dialokasikan ke tabungan.'),
                    'tanggal_transaksi'     => $pemasukan->tanggal_pemasukan,
                ]);
            }
        });

        return redirect()->route('pemasukan.index')
            ->with('sukses', 'Data pemasukan berhasil disimpan.');
    }

    /**
     * Tampilkan form edit pemasukan.
     */
    public function edit($id)
    {
        $pemasukan = $this->getPemasukanMilikUser($id);
        $tabungans = TabunganMahasiswa::where('user_id', auth()->id())
            ->orderBy('tujuan_tabungan')
            ->get();

        return view('pemasukan.edit', compact('pemasukan', 'tabungans'));
    }

    /**
     * Perbarui data pemasukan.
     * Reverse efek lama pada tabungan jika ada, terapkan efek baru.
     */
    public function update(Request $request, $id)
    {
        $pemasukan = $this->getPemasukanMilikUser($id);
        $validated = $this->validasiInput($request);

        if (!empty($validated['tabungan_mahasiswa_id'])) {
            $this->pastikanTabunganMilikUser($validated['tabungan_mahasiswa_id']);
        }

        DB::transaction(function () use ($pemasukan, $validated) {
            $nominalLama        = (float) $pemasukan->nominal;
            $masukLama          = (bool) $pemasukan->masuk_ke_tabungan;
            $tabunganIdLama     = $pemasukan->tabungan_mahasiswa_id;
            $nominalBaru        = (float) $validated['nominal'];
            $masukBaru          = (bool) ($validated['masuk_ke_tabungan'] ?? false);
            $tabunganIdBaru     = $validated['tabungan_mahasiswa_id'] ?? null;

            // ── 1. Reverse efek lama ──────────────────────────────────────────
            if ($masukLama && $tabunganIdLama) {
                $tabunganLama = TabunganMahasiswa::where('id', $tabunganIdLama)
                    ->where('user_id', auth()->id())
                    ->lockForUpdate()
                    ->first();

                if ($tabunganLama) {
                    $saldoReverse = $tabunganLama->saldo_setelah_satu_periode - $nominalLama;
                    $tabunganLama->update([
                        'saldo_setelah_satu_periode' => $saldoReverse,
                        'saldo_awal'                 => $saldoReverse,
                    ]);

                    RiwayatTabungan::where('tabungan_mahasiswa_id', $tabunganIdLama)
                        ->where('user_id', auth()->id())
                        ->where('jenis', 'setoran_tambahan')
                        ->where('catatan', 'like', '%[pemasukan_id:' . $pemasukan->id . ']%')
                        ->delete();
                }
            }

            // ── 2. Update data pemasukan ──────────────────────────────────────
            $pemasukan->update(array_merge($validated, [
                'user_id'           => auth()->id(),
                'masuk_ke_tabungan' => $masukBaru,
            ]));

            // ── 3. Terapkan efek baru ─────────────────────────────────────────
            if ($masukBaru && $tabunganIdBaru) {
                $tabunganBaru = TabunganMahasiswa::where('id', $tabunganIdBaru)
                    ->where('user_id', auth()->id())
                    ->lockForUpdate()
                    ->firstOrFail();

                $saldoSebelumnya = $tabunganBaru->saldo_setelah_satu_periode;
                $saldoBaru       = $saldoSebelumnya + $nominalBaru;

                $tabunganBaru->update([
                    'saldo_setelah_satu_periode' => $saldoBaru,
                    'saldo_awal'                 => $saldoBaru,
                ]);

                RiwayatTabungan::create([
                    'user_id'               => auth()->id(),
                    'tabungan_mahasiswa_id' => $tabunganBaru->id,
                    'nominal'               => $nominalBaru,
                    'jenis'                 => 'setoran_tambahan',
                    'saldo_sebelumnya'      => $saldoSebelumnya,
                    'saldo_sesudahnya'      => $saldoBaru,
                    'catatan'               => '[pemasukan_id:' . $pemasukan->id . '] ' .
                                              ($pemasukan->keterangan ?? 'Pemasukan dialokasikan ke tabungan.'),
                    'tanggal_transaksi'     => $pemasukan->tanggal_pemasukan,
                ]);
            }
        });

        return redirect()->route('pemasukan.index')
            ->with('sukses', 'Data pemasukan berhasil diperbarui.');
    }

    /**
     * Hapus data pemasukan.
     * Jika masuk_ke_tabungan = true, reverse saldo tabungan terkait.
     */
    public function destroy($id)
    {
        $pemasukan = $this->getPemasukanMilikUser($id);

        DB::transaction(function () use ($pemasukan) {
            // Reverse saldo tabungan jika pemasukan sebelumnya dialokasikan
            if ($pemasukan->masuk_ke_tabungan && $pemasukan->tabungan_mahasiswa_id) {
                $tabungan = TabunganMahasiswa::where('id', $pemasukan->tabungan_mahasiswa_id)
                    ->where('user_id', auth()->id())
                    ->lockForUpdate()
                    ->first();

                if ($tabungan) {
                    $saldoBaru = $tabungan->saldo_setelah_satu_periode - (float) $pemasukan->nominal;
                    $tabungan->update([
                        'saldo_setelah_satu_periode' => $saldoBaru,
                        'saldo_awal'                 => $saldoBaru,
                    ]);

                    RiwayatTabungan::where('tabungan_mahasiswa_id', $pemasukan->tabungan_mahasiswa_id)
                        ->where('user_id', auth()->id())
                        ->where('jenis', 'setoran_tambahan')
                        ->where('catatan', 'like', '%[pemasukan_id:' . $pemasukan->id . ']%')
                        ->delete();
                }
            }

            $pemasukan->delete();
        });

        return redirect()->route('pemasukan.index')
            ->with('sukses', 'Data pemasukan berhasil dihapus.');
    }

    // ═══════════════════════════════════════════
    //  PRIVATE HELPERS
    // ═══════════════════════════════════════════

    private function getPemasukanMilikUser($id): Pemasukan
    {
        $pemasukan = Pemasukan::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (! $pemasukan) {
            abort(403, 'Kamu tidak memiliki akses ke data pemasukan ini.');
        }

        return $pemasukan;
    }

    private function pastikanTabunganMilikUser($tabunganId): void
    {
        $ada = TabunganMahasiswa::where('id', $tabunganId)
            ->where('user_id', auth()->id())
            ->exists();

        if (! $ada) {
            abort(403, 'Tabungan yang dipilih bukan milikmu.');
        }
    }

    private function validasiInput(Request $request): array
    {
        return $request->validate([
            'nominal'               => 'required|numeric|min:1',
            'kategori'              => 'required|in:uang_saku,uang_bulanan,kerja_paruh_waktu,beasiswa,lainnya',
            'masuk_ke_tabungan'     => 'boolean',
            'tabungan_mahasiswa_id' => 'required_if:masuk_ke_tabungan,1|nullable|exists:tabungan_mahasiswa,id',
            'keterangan'            => 'nullable|string|max:500',
            'tanggal_pemasukan'     => 'required|date',
        ], [
            'nominal.required'                 => 'Nominal pemasukan wajib diisi.',
            'nominal.numeric'                  => 'Nominal pemasukan harus berupa angka.',
            'nominal.min'                      => 'Nominal pemasukan minimal Rp 1.',
            'kategori.required'                => 'Kategori pemasukan wajib dipilih.',
            'kategori.in'                      => 'Kategori yang dipilih tidak valid.',
            'tabungan_mahasiswa_id.required_if'=> 'Pilih celengan tabungan tujuan alokasi.',
            'tabungan_mahasiswa_id.exists'     => 'Celengan tabungan yang dipilih tidak ditemukan.',
            'keterangan.max'                   => 'Keterangan maksimal 500 karakter.',
            'tanggal_pemasukan.required'       => 'Tanggal pemasukan wajib diisi.',
            'tanggal_pemasukan.date'           => 'Format tanggal pemasukan tidak valid.',
        ]);
    }
}
