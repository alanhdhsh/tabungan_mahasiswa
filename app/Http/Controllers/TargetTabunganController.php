<?php

namespace App\Http\Controllers;

use App\Models\TargetTabungan;
use Illuminate\Http\Request;

/**
 * Controller TargetTabunganController
 *
 * Mengelola CRUD target menabung mahasiswa.
 * Status target otomatis berubah menjadi 'tercapai' ketika
 * dana_terkumpul >= nominal_target.
 *
 * KEAMANAN: Semua query difilter berdasarkan user_id = auth()->id().
 */
class TargetTabunganController extends Controller
{
    /**
     * Daftar target tabungan milik pengguna.
     * Urutan: aktif terlebih dahulu, lalu tercapai, lalu dibatalkan.
     */
    public function index()
    {
        $targets = TargetTabungan::where('user_id', auth()->id())
            ->orderByRaw("FIELD(status, 'aktif', 'tercapai', 'dibatalkan')")
            ->orderBy('created_at', 'desc')
            ->get();

        return view('target.index', compact('targets'));
    }

    /**
     * Tampilkan form tambah target tabungan baru.
     */
    public function create()
    {
        return view('target.create');
    }

    /**
     * Simpan target tabungan baru.
     */
    public function store(Request $request)
    {
        $validated = $this->validasiInput($request);

        TargetTabungan::create(array_merge($validated, [
            'user_id' => auth()->id(),
        ]));

        return redirect()->route('target.index')
            ->with('sukses', 'Target tabungan baru berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit target tabungan.
     */
    public function edit($id)
    {
        $target = $this->getTargetMilikUser($id);

        return view('target.edit', compact('target'));
    }

    /**
     * Perbarui data target tabungan.
     * Otomatis ubah status ke 'tercapai' jika dana_terkumpul >= nominal_target.
     */
    public function update(Request $request, $id)
    {
        $target    = $this->getTargetMilikUser($id);
        $validated = $this->validasiInput($request, $id);

        // Otomatis perbarui status jika target sudah tercapai
        if (isset($validated['dana_terkumpul']) && isset($validated['nominal_target'])) {
            if ((float) $validated['dana_terkumpul'] >= (float) $validated['nominal_target']) {
                $validated['status'] = 'tercapai';
            }
        }

        $target->update($validated);

        return redirect()->route('target.index')
            ->with('sukses', 'Data target tabungan berhasil diperbarui.');
    }

    /**
     * Hapus data target tabungan.
     */
    public function destroy($id)
    {
        $target = $this->getTargetMilikUser($id);
        $nama   = $target->nama_target;
        $target->delete();

        return redirect()->route('target.index')
            ->with('sukses', 'Target tabungan "' . $nama . '" berhasil dihapus.');
    }

    /**
     * Tambahkan dana ke target tabungan.
     * Otomatis cek dan update status ke 'tercapai' jika sudah mencukupi.
     */
    public function tambahDana(Request $request, $id)
    {
        $target = $this->getTargetMilikUser($id);

        $request->validate([
            'nominal_tambahan' => 'required|numeric|min:1',
            'catatan'          => 'nullable|string|max:255',
        ], [
            'nominal_tambahan.required' => 'Nominal dana yang ditambahkan wajib diisi.',
            'nominal_tambahan.numeric'  => 'Nominal harus berupa angka.',
            'nominal_tambahan.min'      => 'Nominal minimal Rp 1.',
        ]);

        $nominal      = (float) $request->nominal_tambahan;
        $danaBaru     = (float) $target->dana_terkumpul + $nominal;
        $statusBaru   = $target->status;

        // Otomatis tandai tercapai jika dana sudah mencukupi
        if ($danaBaru >= (float) $target->nominal_target) {
            $statusBaru = 'tercapai';
        }

        $target->update([
            'dana_terkumpul' => $danaBaru,
            'status'         => $statusBaru,
        ]);

        $pesan = 'Dana sebesar ' . TargetTabungan::formatRupiah($nominal) . ' berhasil ditambahkan ke target "' . $target->nama_target . '".';
        if ($statusBaru === 'tercapai') {
            $pesan .= ' 🎉 Selamat, target tabunganmu telah tercapai!';
        }

        return redirect()->route('target.index')->with('sukses', $pesan);
    }

    // ═══════════════════════════════════════════
    //  PRIVATE HELPERS
    // ═══════════════════════════════════════════

    private function getTargetMilikUser($id): TargetTabungan
    {
        $target = TargetTabungan::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (! $target) {
            abort(403, 'Kamu tidak memiliki akses ke target tabungan ini.');
        }

        return $target;
    }

    private function validasiInput(Request $request, $id = null): array
    {
        return $request->validate([
            'nama_target'    => 'required|string|max:150',
            'nominal_target' => 'required|numeric|min:1',
            'dana_terkumpul' => 'nullable|numeric|min:0',
            'tanggal_target' => 'nullable|date',
            'keterangan'     => 'nullable|string|max:500',
            'status'         => 'nullable|in:aktif,tercapai,dibatalkan',
        ], [
            'nama_target.required'    => 'Nama target tabungan wajib diisi.',
            'nama_target.max'         => 'Nama target maksimal 150 karakter.',
            'nominal_target.required' => 'Nominal target tabungan wajib diisi.',
            'nominal_target.numeric'  => 'Nominal target harus berupa angka.',
            'nominal_target.min'      => 'Nominal target minimal Rp 1.',
            'dana_terkumpul.min'      => 'Dana terkumpul tidak boleh negatif.',
            'tanggal_target.date'     => 'Format tanggal target tidak valid.',
            'keterangan.max'          => 'Keterangan maksimal 500 karakter.',
            'status.in'               => 'Status yang dipilih tidak valid.',
        ]);
    }
}
