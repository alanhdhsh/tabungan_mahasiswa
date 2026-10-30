<?php

namespace App\Http\Controllers;

use App\Models\TabunganMahasiswa;
use App\Models\RiwayatTabungan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller TabunganMahasiswaController
 * 
 * Mengelola alur logika aplikasi:
 * 1. Menampilkan Dashboard dan Riwayat (index)
 * 2. Menampilkan Form Input Data (create)
 * 3. Menghitung dan Menyimpan Data Baru (store)
 * 4. Menampilkan Detail Perhitungan (show)
 * 5. Menampilkan Form Edit Data (edit)
 * 6. Menghitung Ulang dan Memperbarui Data (update)
 * 7. Menghapus Data (destroy)
 *
 * KEAMANAN: Seluruh query dan aksi dibatasi berdasarkan pengguna yang sedang login
 * melalui auth()->id(). Setiap data celengan dan riwayat transaksi hanya dapat
 * diakses, diedit, dan dihapus oleh pemiliknya.
 */
class TabunganMahasiswaController extends Controller
{
    /**
     * Helper untuk mengambil data celengan milik pengguna yang sedang login.
     * Menggantikan findOrFail() biasa dengan pengecekan kepemilikan.
     * Jika data tidak ditemukan atau bukan milik pengguna, akan mengembalikan 403/404.
     */
    private function getTabunganMilikUser($id)
    {
        $tabungan = TabunganMahasiswa::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$tabungan) {
            abort(403, 'Kamu tidak memiliki akses ke data celengan ini.');
        }

        return $tabungan;
    }

    /**
     * Menampilkan Dashboard Ringkasan dan Riwayat Perhitungan Tabungan Mahasiswa.
     * HANYA menampilkan data milik pengguna yang sedang login.
     */
    public function index(Request $request)
    {
        $userId = auth()->id();

        // Query dengan pencarian nama mahasiswa atau tujuan impian
        // FILTER: Hanya data milik pengguna yang sedang login
        $query = TabunganMahasiswa::where('user_id', $userId);

        if ($request->filled('cari')) {
            $keyword = $request->cari;
            $query->where(function($q) use ($keyword) {
                $q->where('nama_mahasiswa', 'like', '%' . $keyword . '%')
                  ->orWhere('tujuan_tabungan', 'like', '%' . $keyword . '%');
            });
        }

        if ($request->filled('kategori') && $request->kategori != 'semua') {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('status')) {
            if ($request->status == 'tercapai') {
                $query->where('status_keuangan', 'like', '%tercapai%');
            } elseif ($request->status == 'proses') {
                $query->where('status_keuangan', 'like', '%proses%');
            } elseif ($request->status == 'defisit') {
                $query->where('status_keuangan', 'like', '%defisit%');
            }
        }

        // Ambil data dengan urutan terbaru
        $riwayatTabungan = $query->orderBy('created_at', 'desc')->paginate(12);

        // Data statistik agregat untuk dashboard ringkasan
        // FILTER: Hanya menghitung data milik pengguna yang sedang login
        $totalMahasiswa = TabunganMahasiswa::where('user_id', $userId)->count();
        $totalAkumulasiSaldo = TabunganMahasiswa::where('user_id', $userId)->sum('saldo_setelah_satu_periode');
        $totalTargetTabungan = TabunganMahasiswa::where('user_id', $userId)->sum('target_tabungan');
        $rataRataTabungan = TabunganMahasiswa::where('user_id', $userId)->avg('tabungan_per_periode') ?? 0;
        $jumlahTargetTercapai = TabunganMahasiswa::where('user_id', $userId)->where('status_keuangan', 'like', '%tercapai%')->count();
        $jumlahDefisit = TabunganMahasiswa::where('user_id', $userId)->where('status_keuangan', 'like', '%defisit%')->count();

        // Ambil data terbaru milik pengguna ini untuk disorot di dashboard utama (jika ada)
        $dataTerbaru = TabunganMahasiswa::where('user_id', $userId)->latest()->first();

        return view('tabungan.index', compact(
            'riwayatTabungan',
            'totalMahasiswa',
            'totalAkumulasiSaldo',
            'totalTargetTabungan',
            'rataRataTabungan',
            'jumlahTargetTercapai',
            'jumlahDefisit',
            'dataTerbaru'
        ));
    }

    /**
     * Menampilkan form input data mahasiswa untuk perhitungan tabungan.
     */
    public function create()
    {
        return view('tabungan.create');
    }

    /**
     * Memvalidasi input, menjalankan algoritma perhitungan tabungan di server,
     * dan menyimpan hasilnya ke database MySQL.
     * KEAMANAN: user_id ditetapkan berdasarkan auth()->id(), bukan dari formulir.
     */
    public function store(Request $request)
    {
        // 1. Validasi data input
        $validatedData = $this->validasiInput($request);

        // Jika nama_mahasiswa tidak diisi di formulir, gunakan nama akun yang sedang login
        if (empty($validatedData['nama_mahasiswa'])) {
            $validatedData['nama_mahasiswa'] = auth()->user()->name ?? 'Mahasiswa';
        }

        // 2. Jalankan algoritma perhitungan tabungan secara otomatis
        $hasilPerhitungan = $this->hitungTabungan($validatedData);

        // 3. Gabungkan data input dengan data hasil perhitungan
        $dataSimpan = array_merge($validatedData, $hasilPerhitungan);

        // KEAMANAN: Tetapkan pemilik data berdasarkan pengguna yang sedang login
        $dataSimpan['user_id'] = auth()->id();

        if (empty($dataSimpan['tujuan_tabungan'])) {
            $dataSimpan['tujuan_tabungan'] = 'Tabungan Impian ' . $dataSimpan['nama_mahasiswa'];
        }
        if (empty($dataSimpan['kategori'])) {
            $dataSimpan['kategori'] = 'Umum';
        }

        // Isi tanggal pencatatan otomatis jika kosong
        if (empty($dataSimpan['tanggal_pencatatan'])) {
            $dataSimpan['tanggal_pencatatan'] = now()->toDateString();
        }

        // 4. Simpan ke database MySQL menggunakan Eloquent ORM
        $tabungan = TabunganMahasiswa::create($dataSimpan);

        // 5. Catat riwayat setoran awal secara otomatis
        if ($tabungan->saldo_awal > 0) {
            RiwayatTabungan::create([
                'user_id' => auth()->id(),
                'tabungan_mahasiswa_id' => $tabungan->id,
                'nominal' => $tabungan->saldo_awal,
                'jenis' => 'setoran_awal',
                'saldo_sebelumnya' => 0,
                'saldo_sesudahnya' => $tabungan->saldo_setelah_satu_periode,
                'catatan' => 'Pembukaan celengan baru "' . ($tabungan->tujuan_tabungan ?? $tabungan->nama_mahasiswa) . '" dengan saldo awal.',
                'tanggal_transaksi' => now(),
            ]);
        }

        // 6. Alihkan ke halaman detail hasil perhitungan dengan pesan sukses
        return redirect()->route('tabungan.show', $tabungan->id)
            ->with('sukses', 'Data tabungan atas nama ' . $tabungan->nama_mahasiswa . ' berhasil dihitung dan disimpan!');
    }

    /**
     * Menampilkan rincian lengkap hasil perhitungan tabungan per mahasiswa.
     * KEAMANAN: Hanya pemilik yang dapat melihat detail celengannya.
     */
    public function show($id)
    {
        $tabungan = $this->getTabunganMilikUser($id);

        return view('tabungan.show', compact('tabungan'));
    }

    /**
     * Menampilkan form untuk mengedit data mahasiswa yang sudah tersimpan.
     * KEAMANAN: Hanya pemilik yang dapat mengedit celengannya.
     */
    public function edit($id)
    {
        $tabungan = $this->getTabunganMilikUser($id);

        return view('tabungan.edit', compact('tabungan'));
    }

    /**
     * Memvalidasi input terbaru, MENGHITUNG ULANG hasil tabungan di server,
     * dan memperbarui data di database MySQL.
     * KEAMANAN: Hanya pemilik yang dapat memperbarui data celengannya.
     */
    public function update(Request $request, $id)
    {
        // Cari data yang akan diedit — hanya milik pengguna yang sedang login
        $tabungan = $this->getTabunganMilikUser($id);

        // 1. Validasi input
        $validatedData = $this->validasiInput($request);

        // Jika nama_mahasiswa tidak diisi, gunakan nama sebelumnya atau nama akun
        if (empty($validatedData['nama_mahasiswa'])) {
            $validatedData['nama_mahasiswa'] = $tabungan->nama_mahasiswa ?? (auth()->user()->name ?? 'Mahasiswa');
        }

        // 2. Hitung ulang hasil perhitungan berdasarkan input terbaru
        $hasilPerhitungan = $this->hitungTabungan($validatedData);

        // 3. Gabungkan data input dan hasil perhitungan baru
        $dataUpdate = array_merge($validatedData, $hasilPerhitungan);

        if (empty($dataUpdate['tujuan_tabungan'])) {
            $dataUpdate['tujuan_tabungan'] = $tabungan->tujuan_tabungan ?? ('Tabungan Impian ' . $dataUpdate['nama_mahasiswa']);
        }
        if (empty($dataUpdate['kategori'])) {
            $dataUpdate['kategori'] = $tabungan->kategori ?? 'Umum';
        }

        // Perbarui tanggal jika tidak diisi
        if (empty($dataUpdate['tanggal_pencatatan'])) {
            $dataUpdate['tanggal_pencatatan'] = $tabungan->tanggal_pencatatan ?? now()->toDateString();
        }

        // 4. Simpan pembaruan ke database
        $tabungan->update($dataUpdate);

        // 5. Alihkan ke halaman detail dengan notifikasi sukses
        return redirect()->route('tabungan.show', $tabungan->id)
            ->with('sukses', 'Data tabungan berhasil diperbarui dan dihitung ulang!');
    }

    /**
     * Menghapus data perhitungan tabungan dari database.
     * KEAMANAN: Hanya pemilik yang dapat menghapus celengannya.
     */
    public function destroy($id)
    {
        $tabungan = $this->getTabunganMilikUser($id);
        $nama = $tabungan->nama_mahasiswa;
        $tabungan->delete();

        return redirect()->route('tabungan.index')
            ->with('sukses', 'Data tabungan milik ' . $nama . ' berhasil dihapus.');
    }

    /**
     * Fitur Cepat Menabung: Tambah simpanan langsung ke celengan/target tabungan.
     * KEAMANAN: Hanya pemilik yang dapat menambah saldo ke celengannya.
     */
    public function tambahSaldo(Request $request, $id)
    {
        $request->validate([
            'nominal_tabungan' => 'required|numeric|min:1000',
            'catatan_setoran' => 'nullable|string|max:255',
        ], [
            'nominal_tabungan.required' => 'Nominal tabungan wajib diisi.',
            'nominal_tabungan.numeric' => 'Nominal tabungan harus berupa angka.',
            'nominal_tabungan.min' => 'Nominal tabungan minimal Rp 1.000.',
        ]);

        $tabungan = $this->getTabunganMilikUser($id);
        $nominal = (float) $request->nominal_tabungan;
        $saldoSebelumnya = $tabungan->saldo_setelah_satu_periode;

        // Tambahkan ke saldo awal
        $saldoBaru = $tabungan->saldo_awal + $nominal;
        $tabungan->saldo_awal = $saldoBaru;

        // Hitung ulang semua rumus
        $dataKalkulasi = [
            'saldo_awal' => $tabungan->saldo_awal,
            'pemasukan_per_periode' => $tabungan->pemasukan_per_periode,
            'pengeluaran_makan' => $tabungan->pengeluaran_makan,
            'pengeluaran_transportasi' => $tabungan->pengeluaran_transportasi,
            'pengeluaran_lainnya' => $tabungan->pengeluaran_lainnya,
            'pengeluaran_tidak_terduga' => $tabungan->pengeluaran_tidak_terduga ?? 0,
            'target_tabungan' => $tabungan->target_tabungan,
        ];

        $hasilPerhitungan = $this->hitungTabungan($dataKalkulasi);
        $tabungan->update(array_merge([
            'saldo_awal' => $saldoBaru,
        ], $hasilPerhitungan));



        // Catat riwayat setoran tambahan
        RiwayatTabungan::create([
            'user_id' => auth()->id(),
            'tabungan_mahasiswa_id' => $tabungan->id,
            'nominal' => $nominal,
            'jenis' => 'setoran_tambahan',
            'saldo_sebelumnya' => $saldoSebelumnya,
            'saldo_sesudahnya' => $tabungan->saldo_setelah_satu_periode,
            'catatan' => $request->catatan_setoran ?? 'Setoran tabungan ke celengan "' . ($tabungan->tujuan_tabungan ?? $tabungan->nama_mahasiswa) . '".',
            'tanggal_transaksi' => now(),
        ]);

        return back()->with('sukses', '🎉 Hore! Tabungan sebesar ' . TabunganMahasiswa::formatRupiah($nominal) . ' berhasil disisihkan ke celengan "' . ($tabungan->tujuan_tabungan ?? $tabungan->nama_mahasiswa) . '"!');
    }

    /**
     * Fitur Penarikan Dana: Kurangi saldo tabungan saat dibutuhkan.
     * KEAMANAN: Hanya pemilik yang dapat menarik dana dari celengannya.
     */
    public function tarikSaldo(Request $request, $id)
    {
        $request->validate([
            'nominal_penarikan' => 'required|numeric|min:1000',
            'catatan_penarikan' => 'nullable|string|max:255',
        ], [
            'nominal_penarikan.required' => 'Nominal penarikan wajib diisi.',
            'nominal_penarikan.numeric' => 'Nominal penarikan harus berupa angka.',
            'nominal_penarikan.min' => 'Nominal penarikan minimal Rp 1.000.',
        ]);

        $tabungan = $this->getTabunganMilikUser($id);
        $nominal = (float) $request->nominal_penarikan;

        // Pastikan saldo cukup untuk penarikan
        if ($nominal > $tabungan->saldo_awal) {
            return back()->with('error', '❌ Gagal! Nominal penarikan melebihi saldo yang tersedia (' . TabunganMahasiswa::formatRupiah($tabungan->saldo_awal) . ').');
        }

        $saldoSebelumnya = $tabungan->saldo_setelah_satu_periode;

        // Kurangi saldo awal
        $saldoBaru = $tabungan->saldo_awal - $nominal;
        $tabungan->saldo_awal = $saldoBaru;

        // Hitung ulang semua rumus
        $dataKalkulasi = [
            'saldo_awal' => $tabungan->saldo_awal,
            'pemasukan_per_periode' => $tabungan->pemasukan_per_periode,
            'pengeluaran_makan' => $tabungan->pengeluaran_makan,
            'pengeluaran_transportasi' => $tabungan->pengeluaran_transportasi,
            'pengeluaran_lainnya' => $tabungan->pengeluaran_lainnya,
            'pengeluaran_tidak_terduga' => $tabungan->pengeluaran_tidak_terduga ?? 0,
            'target_tabungan' => $tabungan->target_tabungan,
        ];

        $hasilPerhitungan = $this->hitungTabungan($dataKalkulasi);
        $tabungan->update(array_merge([
            'saldo_awal' => $saldoBaru,
        ], $hasilPerhitungan));

        // Catat riwayat penarikan
        RiwayatTabungan::create([
            'user_id' => auth()->id(),
            'tabungan_mahasiswa_id' => $tabungan->id,
            'nominal' => $nominal,
            'jenis' => 'penarikan',
            'saldo_sebelumnya' => $saldoSebelumnya,
            'saldo_sesudahnya' => $tabungan->saldo_setelah_satu_periode,
            'catatan' => $request->catatan_penarikan ?? 'Penarikan dana dari celengan "' . ($tabungan->tujuan_tabungan ?? $tabungan->nama_mahasiswa) . '".',
            'tanggal_transaksi' => now(),
        ]);

        return back()->with('sukses', '💰 Penarikan sebesar ' . TabunganMahasiswa::formatRupiah($nominal) . ' dari celengan "' . ($tabungan->tujuan_tabungan ?? $tabungan->nama_mahasiswa) . '" berhasil diproses.');
    }

    /**
     * Menampilkan halaman Riwayat Transaksi lengkap dengan filter dan pencarian.
     * KEAMANAN: Hanya pemilik yang dapat melihat riwayat celengannya.
     */
    public function riwayat(Request $request, $id)
    {
        // Pastikan celengan ini milik pengguna yang sedang login
        $tabungan = $this->getTabunganMilikUser($id);

        $query = RiwayatTabungan::where('tabungan_mahasiswa_id', $id)
            ->where('user_id', auth()->id());

        // Filter berdasarkan jenis transaksi
        if ($request->filled('jenis') && $request->jenis != 'semua') {
            $query->where('jenis', $request->jenis);
        }

        // Filter berdasarkan rentang tanggal
        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal_transaksi', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal_transaksi', '<=', $request->sampai_tanggal);
        }

        // Pencarian catatan
        if ($request->filled('cari')) {
            $query->where('catatan', 'like', '%' . $request->cari . '%');
        }

        // Ambil data riwayat transaksi dengan urutan terbaru
        $riwayatTransaksi = $query->orderBy('tanggal_transaksi', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Statistik ringkasan riwayat transaksi — hanya milik pengguna yang login
        $totalSetoran = RiwayatTabungan::where('tabungan_mahasiswa_id', $id)
            ->where('user_id', auth()->id())
            ->whereIn('jenis', ['setoran_awal', 'setoran_tambahan', 'alokasi_rutin'])
            ->sum('nominal');
        $totalPenarikan = RiwayatTabungan::where('tabungan_mahasiswa_id', $id)
            ->where('user_id', auth()->id())
            ->where('jenis', 'penarikan')
            ->sum('nominal');
        $jumlahTransaksi = RiwayatTabungan::where('tabungan_mahasiswa_id', $id)
            ->where('user_id', auth()->id())
            ->count();
        $transaksiTerakhir = RiwayatTabungan::where('tabungan_mahasiswa_id', $id)
            ->where('user_id', auth()->id())
            ->latest('tanggal_transaksi')
            ->first();

        return view('tabungan.riwayat', compact(
            'tabungan',
            'riwayatTransaksi',
            'totalSetoran',
            'totalPenarikan',
            'jumlahTransaksi',
            'transaksiTerakhir'
        ));
    }

    /**
     * Method bantu untuk memvalidasi input formulir.
     * Menggunakan pesan bahasa Indonesia yang mudah dipahami.
     */
    private function validasiInput(Request $request): array
    {
        $aturan = [
            'nama_mahasiswa' => 'nullable|string|max:150',
            'tujuan_tabungan' => 'nullable|string|max:150',
            'kategori' => 'nullable|string|max:50',
            'catatan_impian' => 'nullable|string|max:500',
            'saldo_awal' => 'required|numeric|min:0',
            'pemasukan_per_periode' => 'required|numeric|min:0',
            'periode_pemasukan' => 'required|in:harian,mingguan,bulanan',
            'pengeluaran_makan' => 'required|numeric|min:0',
            'pengeluaran_transportasi' => 'required|numeric|min:0',
            'pengeluaran_lainnya' => 'required|numeric|min:0',
            'pengeluaran_tidak_terduga' => 'nullable|numeric|min:0',
            'target_tabungan' => 'required|numeric|min:0',
            'tanggal_pencatatan' => 'nullable|date',
        ];

        $pesan = [
            'nama_mahasiswa.max' => 'Nama mahasiswa maksimal 150 karakter.',
            'tujuan_tabungan.max' => 'Nama tujuan tabungan maksimal 150 karakter.',
            'saldo_awal.required' => 'Saldo awal tabungan wajib diisi.',
            'saldo_awal.numeric' => 'Saldo awal harus berupa angka.',
            'saldo_awal.min' => 'Saldo awal tidak boleh bernilai negatif.',
            'pemasukan_per_periode.required' => 'Pemasukan per periode wajib diisi.',
            'pemasukan_per_periode.numeric' => 'Pemasukan harus berupa angka.',
            'pemasukan_per_periode.min' => 'Pemasukan tidak boleh bernilai negatif.',
            'periode_pemasukan.required' => 'Silakan pilih jenis periode pemasukan.',
            'periode_pemasukan.in' => 'Periode pemasukan harus salah satu dari: harian, mingguan, atau bulanan.',
            'pengeluaran_makan.required' => 'Pengeluaran makan wajib diisi.',
            'pengeluaran_makan.numeric' => 'Pengeluaran makan harus berupa angka.',
            'pengeluaran_makan.min' => 'Pengeluaran makan tidak boleh negatif.',
            'pengeluaran_transportasi.required' => 'Pengeluaran transportasi wajib diisi.',
            'pengeluaran_transportasi.numeric' => 'Pengeluaran transportasi harus berupa angka.',
            'pengeluaran_transportasi.min' => 'Pengeluaran transportasi tidak boleh negatif.',
            'pengeluaran_lainnya.required' => 'Pengeluaran lainnya wajib diisi.',
            'pengeluaran_lainnya.numeric' => 'Pengeluaran lainnya harus berupa angka.',
            'pengeluaran_lainnya.min' => 'Pengeluaran lainnya tidak boleh negatif.',
            'pengeluaran_tidak_terduga.numeric' => 'Pengeluaran tidak terduga harus berupa angka.',
            'pengeluaran_tidak_terduga.min' => 'Pengeluaran tidak terduga tidak boleh negatif.',
            'target_tabungan.required' => 'Target tabungan impian wajib diisi.',
            'target_tabungan.numeric' => 'Target tabungan harus berupa angka.',
            'target_tabungan.min' => 'Target tabungan tidak boleh bernilai negatif.',
            'tanggal_pencatatan.date' => 'Format tanggal pencatatan tidak valid.',
        ];

        return $request->validate($aturan, $pesan);
    }

    /**
     * ALGORITMA PERHITUNGAN TABUNGAN OTOMATIS
     * 
     * Method ini memisahkan logika matematika dari tampilan dan dapat dipakai ulang.
     * Kode menggunakan operasi aritmetika dasar dan percabangan if-elseif-else
     * agar mudah dipahami oleh mahasiswa pemula.
     */
    private function hitungTabungan(array $data): array
    {
        // 1. Ekstrak nilai input ke variabel yang jelas
        $saldoAwal = (float) $data['saldo_awal'];
        $pemasukan = (float) $data['pemasukan_per_periode'];
        $makan = (float) $data['pengeluaran_makan'];
        $transportasi = (float) $data['pengeluaran_transportasi'];
        $lainnya = (float) $data['pengeluaran_lainnya'];
        $tidakTerduga = (float) ($data['pengeluaran_tidak_terduga'] ?? 0);
        $target = (float) $data['target_tabungan'];

        // 2. Rumus: Total Pengeluaran = Makan + Transportasi + Lainnya + Tidak Terduga
        $totalPengeluaran = $makan + $transportasi + $lainnya + $tidakTerduga;

        // 3. Rumus: Tabungan Per Periode = Pemasukan - Total Pengeluaran
        $tabunganPerPeriode = $pemasukan - $totalPengeluaran;

        // 4. Rumus: Saldo Setelah Satu Periode = Saldo Awal + Tabungan Per Periode
        $saldoSetelahSatuPeriode = $saldoAwal + $tabunganPerPeriode;

        // 5. Rumus: Persentase Tabungan = (Tabungan Per Periode / Pemasukan) * 100%
        // Penanganan aman pembagian dengan nol (jika pemasukan nol, persentase = 0)
        if ($pemasukan > 0) {
            $persentaseTabungan = round(($tabunganPerPeriode / $pemasukan) * 100, 2);
        } else {
            $persentaseTabungan = 0.0;
        }

        // 6. Rumus: Sisa Target = Target Tabungan - Saldo Setelah Satu Periode
        $sisaTarget = $target - $saldoSetelahSatuPeriode;
        if ($sisaTarget < 0) {
            $sisaTarget = 0.0; // Jika saldo melebihi target, sisa target adalah 0
        }

        // 7. Penentuan Status Keuangan dan Estimasi Jumlah Periode Tambahan
        $statusKeuangan = '';
        $jumlahPeriodeTambahan = null;

        // Kasus A: Target Tabungan Sudah Tercapai
        if ($saldoSetelahSatuPeriode >= $target && $target > 0) {
            $statusKeuangan = 'Target tabungan telah tercapai';
            $jumlahPeriodeTambahan = 0;
            $sisaTarget = 0.0;
        } elseif ($target <= 0) {
            $statusKeuangan = 'Target tabungan telah tercapai';
            $jumlahPeriodeTambahan = 0;
            $sisaTarget = 0.0;
        }
        // Kasus B: Pengeluaran Sama Dengan Pemasukan (Tabungan = 0)
        elseif ($tabunganPerPeriode == 0) {
            $statusKeuangan = 'Impas, belum ada penambahan tabungan';
            $jumlahPeriodeTambahan = null; // Tidak dapat tercapai karena tidak ada penambahan tabungan
        }
        // Kasus C: Pengeluaran Lebih Besar Dari Pemasukan (Tabungan Negatif / Defisit)
        elseif ($tabunganPerPeriode < 0) {
            $statusKeuangan = 'Defisit, pengeluaran melebihi pemasukan';
            $jumlahPeriodeTambahan = null; // Tidak dapat diprediksi / tabungan justru berkurang
        }
        // Kasus D: Tabungan Positif dan Target Belum Tercapai (Surplus)
        else {
            $statusKeuangan = 'Surplus, target dalam proses pencapaian';
            // Rumus: Pembulatan ke atas dari (Sisa Target / Tabungan Per Periode)
            $jumlahPeriodeTambahan = (int) ceil($sisaTarget / $tabunganPerPeriode);
        }

        // Kembalikan array berisi seluruh hasil perhitungan
        return [
            'total_pengeluaran' => round($totalPengeluaran, 2),
            'tabungan_per_periode' => round($tabunganPerPeriode, 2),
            'saldo_setelah_satu_periode' => round($saldoSetelahSatuPeriode, 2),
            'persentase_tabungan' => $persentaseTabungan,
            'sisa_target' => round($sisaTarget, 2),
            'jumlah_periode_tambahan' => $jumlahPeriodeTambahan,
            'status_keuangan' => $statusKeuangan,
        ];
    }
}
