# PANDUAN BELAJAR & DOKUMENTASI UJIAN DOSEN
# SISTEM PERHITUNGAN TABUNGAN MAHASISWA (LARAVEL & MYSQL)

Dokumen ini disusun secara lengkap dan terstruktur sebagai panduan belajar bagi mahasiswa untuk memahami arsitektur program, algoritma perhitungan keuangan, struktur database, serta persiapan menghadapi pertanyaan dan ujian dari dosen penguji.

---

## DAFTAR ISI
1. [Penjelasan Tujuan Aplikasi](#1-penjelasan-tujuan-aplikasi)
2. [Penjelasan Cara Kerja Aplikasi](#2-penjelasan-cara-kerja-aplikasi)
3. [Penjelasan Algoritma Langkah Demi Langkah](#3-penjelasan-algoritma-langkah-demi-langkah)
4. [Pseudocode Perhitungan Tabungan](#4-pseudocode-perhitungan-tabungan)
5. [Flowchart dalam Bentuk Teks](#5-flowchart-dalam-bentuk-teks)
6. [Penjelasan Komponen MVC, Route, Migration, dan Database](#6-penjelasan-komponen-mvc-route-migration-dan-database)
7. [Penjelasan Struktur Kolom Database (tabungan_mahasiswa)](#7-penjelasan-struktur-kolom-database-tabungan_mahasiswa)
8. [Penjelasan Fungsi Penting pada Controller](#8-penjelasan-fungsi-penting-pada-controller)
9. [Contoh Perhitungan Manual (Studi Kasus Nyata)](#9-contoh-perhitungan-manual-studi-kasus-nyata)
10. [Contoh Pengujian Input dan Output (Test Cases)](#10-contoh-pengujian-input-dan-output-test-cases)
11. [15+ Pertanyaan Kritis Dosen Beserta Kunci Jawabannya](#11-15-pertanyaan-kritis-dosen-beserta-kunci-jawabannya)
12. [Panduan dan Naskah Presentasi Ujian di Depan Dosen](#12-panduan-dan-naskah-presentasi-ujian-di-depan-dosen)
13. [Panduan Menjalankan Laravel via VS Code dan Terminal](#13-panduan-menjalankan-laravel-via-vs-code-dan-terminal)
14. [Panduan Membuat Database Menggunakan phpMyAdmin](#14-panduan-membuat-database-menggunakan-phpmyadmin)
15. [Panduan Migration, Seeder, dan Menjalankan Aplikasi di Browser](#15-panduan-migration-seeder-dan-menjalankan-aplikasi-di-browser)

---

## 1. PENJELASAN TUJUAN APLIKASI

Aplikasi **Sistem Perhitungan Tabungan Mahasiswa** dirancang untuk membantu mahasiswa mengelola dan merencanakan keuangan mereka secara realistis dan terukur.

### Masalah yang Dihadapi Mahasiswa:
1. **Kesulitan Mengontrol Pengeluaran**: Mahasiswa sering tidak menyadari ke mana perginya uang saku bulanan/mingguan mereka (makan, transportasi, tugas kuliah, nongkrong).
2. **Tidak Tahu Berapa Lama Target Tercapai**: Mahasiswa memiliki target tertentu (misalnya membeli laptop, membayar UKT semester depan, atau dana darurat), namun tidak tahu berapa periode (hari/minggu/bulan) yang dibutuhkan untuk mencapai target tersebut dengan sisa uang mereka saat ini.
3. **Risiko Defisit Finansial**: Banyak mahasiswa mengalami kondisi "besar pasak daripada tiang" (pengeluaran lebih besar dari pemasukan) tanpa ada peringatan sistematis.

### Solusi yang Diberikan Aplikasi:
- Melakukan pencatatan arus kas (pemasukan vs rincian pengeluaran).
- Menghitung secara otomatis tabungan bersih, persentase tabungan, akumulasi saldo, dan sisa target.
- Mengestimasi secara presisi jumlah periode yang dibutuhkan hingga target tercapai menggunakan pembulatan matematis ke atas (*ceiling*).
- Memberikan klasifikasi status keuangan otomatis: **Surplus**, **Impas**, **Defisit**, atau **Target Tercapai**.

---

## 2. PENJELASAN CARA KERJA APLIKASI

Alur kerja aplikasi mengikuti siklus Request-Response standar berbasis Web MVC (*Model-View-Controller*):

1. **Pengguna Mengakses Halaman Input (`GET /tabungan/create`)**:
   - Browser menampilkan formulir input data mahasiswa (nama, periode, saldo awal, pemasukan, rincian pengeluaran, dan target tabungan).
   - Terdapat fitur pembantu berupa *Live Preview* JavaScript di sisi klien untuk melihat estimasi secara instan saat mengetik.
2. **Pengguna Mengirim Data (`POST /tabungan`)**:
   - Pengguna menekan tombol **"Hitung dan Simpan"**.
   - Data dikirimkan ke server melalui protokol HTTP POST yang diproteksi token keamanan `@csrf`.
3. **Validasi di Sisi Server (`TabunganMahasiswaController@validasiInput`)**:
   - Laravel memeriksa integritas input: nama wajib diisi, seluruh nilai uang harus numerik dan tidak boleh bernilai negatif (`min:0`), dan periode harus valid (`harian`, `mingguan`, atau `bulanan`).
   - Jika ada input tidak valid, sistem mengembalikan pengguna ke halaman form beserta pesan kesalahan (*flash validation error*).
4. **Eksekusi Algoritma Perhitungan di Sisi Server (`TabunganMahasiswaController@hitungTabungan`)**:
   - Server PHP melakukan operasi matematika murni untuk menghitung total pengeluaran, tabungan per periode, saldo terkini, persentase tabungan, sisa target, estimasi waktu, dan status keuangan.
   - Perhitungan dilakukan di server untuk menjamin integritas data (tidak bisa dimanipulasi dari sisi browser klien).
5. **Penyimpanan ke Database MySQL (`TabunganMahasiswa::create`)**:
   - Data input beserta seluruh data hasil kalkulasi disimpan ke dalam tabel `tabungan_mahasiswa` di database MySQL menggunakan Eloquent ORM.
6. **Tampilan Hasil (`GET /tabungan/{id}`)**:
   - Pengguna dialihkan (*redirect*) ke halaman detail hasil perhitungan yang dilengkapi kartu informasi, progress bar pencapaian, dan langkah-langkah rumus matematis.
7. **Pengelolaan Riwayat (CRUD)**:
   - Pengguna dapat melihat daftar seluruh perhitungan di Dashboard (`GET /tabungan`), mengedit data (`PUT /tabungan/{id}`) yang memicu perhitungan ulang otomatis, atau menghapus data (`DELETE /tabungan/{id}`) dengan modal konfirmasi.

---

## 3. PENJELASAN ALGORITMA LANGKAH DEMI LANGKAH

Algoritma perhitungan tabungan mahasiswa terdiri dari 7 langkah logis:

### Langkah 1: Pengambilan dan Sanitasi Input
Mengambil data input:
- $SaldoAwal$
- $Pemasukan$
- $PengeluaranMakan$
- $PengeluaranTransportasi$
- $PengeluaranLainnya$
- $TargetTabungan$

### Langkah 2: Menghitung Total Pengeluaran
Menjumlahkan seluruh pos pengeluaran dalam satu periode:
$$\text{Total Pengeluaran} = \text{Pengeluaran Makan} + \text{Pengeluaran Transportasi} + \text{Pengeluaran Lainnya}$$

### Langkah 3: Menghitung Tabungan Bersih per Periode
Mengurangi pemasukan dengan total pengeluaran:
$$\text{Tabungan Bersih per Periode} = \text{Pemasukan} - \text{Total Pengeluaran}$$
- Nilai positif ($> 0$) berarti terjadi penghematan (surplus).
- Nilai nol ($= 0$) berarti impas.
- Nilai negatif ($< 0$) berarti defisit (pengeluaran melampaui pemasukan).

### Langkah 4: Menghitung Saldo Setelah Satu Periode
Menambahkan tabungan bersih periode berjalan ke saldo awal yang sudah dimiliki:
$$\text{Saldo Setelah Satu Periode} = \text{Saldo Awal} + \text{Tabungan Bersih per Periode}$$

### Langkah 5: Menghitung Persentase Tabungan
Menghitung seberapa efisien mahasiswa menabung dari pemasukannya:
$$\text{Persentase Tabungan} = \begin{cases} \left(\frac{\text{Tabungan Bersih per Periode}}{\text{Pemasukan}}\right) \times 100\%, & \text{jika } \text{Pemasukan} > 0 \\ 0\%, & \text{jika } \text{Pemasukan} = 0 \end{cases}$$
*(Catatan: Penanganan $\text{Pemasukan} = 0$ wajib dilakukan untuk mencegah galat Fatal Error: Division by zero).*

### Langkah 6: Menghitung Sisa Target Tabungan
Menghitung berapa rupiah lagi yang dibutuhkan untuk mencapai target:
$$\text{Sisa Target} = \max(0, \text{Target Tabungan} - \text{Saldo Setelah Satu Periode})$$
Jika saldo setelah satu periode sudah melebihi atau sama dengan target, maka sisa target adalah $0$.

### Langkah 7: Menentukan Status Keuangan & Estimasi Periode Tambahan
Menggunakan percabangan bersyarat (*conditional statement*):
1. **Kondisi A (Target Telah Tercapai)**:
   - Syarat: $\text{Saldo Setelah Satu Periode} \ge \text{Target Tabungan}$ ATAU $\text{Target Tabungan} \le 0$
   - Status: `"Target tabungan telah tercapai"`
   - Jumlah Periode Tambahan: $0$
2. **Kondisi B (Impas)**:
   - Syarat: $\text{Tabungan Bersih per Periode} = 0$
   - Status: `"Impas, belum ada penambahan tabungan"`
   - Jumlah Periode Tambahan: `null` (karena saldo tidak akan pernah bertambah jika tidak ada tabungan)
3. **Kondisi C (Defisit)**:
   - Syarat: $\text{Tabungan Bersih per Periode} < 0$
   - Status: `"Defisit, pengeluaran melebihi pemasukan"`
   - Jumlah Periode Tambahan: `null` (tidak dapat diprediksi karena saldo justru berkurang)
4. **Kondisi D (Surplus / Target dalam Proses)**:
   - Syarat: $\text{Tabungan Bersih per Periode} > 0$ dan target belum tercapai
   - Status: `"Surplus, target dalam proses pencapaian"`
   - Jumlah Periode Tambahan:
     $$\text{Periode Tambahan} = \left\lceil \frac{\text{Sisa Target}}{\text{Tabungan Bersih per Periode}} \right\rceil = \text{ceil}\left(\frac{\text{Sisa Target}}{\text{Tabungan Bersih}}\right)$$
     *(Mengapa pembulatan ke atas / ceil? Karena jika sisa target membutuhkan 2,3 bulan menabung, mahasiswa membutuhkan 3 kali periode menabung agar target genap tercapai).*

---

## 4. PSEUDOCODE PERHITUNGAN TABUNGAN

```plaintext
ALGORITMA HitungTabunganMahasiswa
DEKLARASI:
    namaMahasiswa          : STRING
    periodePemasukan       : STRING  // 'harian', 'mingguan', 'bulanan'
    saldoAwal              : REAL
    pemasukan              : REAL
    pengeluaranMakan       : REAL
    pengeluaranTransport   : REAL
    pengeluaranLainnya     : REAL
    targetTabungan         : REAL

    totalPengeluaran       : REAL
    tabunganPerPeriode     : REAL
    saldoSetelahSatuPeriode: REAL
    persentaseTabungan     : REAL
    sisaTarget             : REAL
    jumlahPeriodeTambahan  : INTEGER
    statusKeuangan         : STRING

PROSES:
    // 1. Hitung total pengeluaran
    totalPengeluaran <- pengeluaranMakan + pengeluaranTransport + pengeluaranLainnya

    // 2. Hitung tabungan bersih
    tabunganPerPeriode <- pemasukan - totalPengeluaran

    // 3. Hitung saldo setelah satu periode
    saldoSetelahSatuPeriode <- saldoAwal + tabunganPerPeriode

    // 4. Hitung persentase tabungan secara aman
    JIKA pemasukan > 0 MAKA
        persentaseTabungan <- (tabunganPerPeriode / pemasukan) * 100
    LAINNYA
        persentaseTabungan <- 0.0
    AKHIR JIKA

    // 5. Hitung sisa target
    sisaTarget <- targetTabungan - saldoSetelahSatuPeriode
    JIKA sisaTarget < 0 MAKA
        sisaTarget <- 0.0
    AKHIR JIKA

    // 6. Penentuan status keuangan dan estimasi periode tambahan
    JIKA (saldoSetelahSatuPeriode >= targetTabungan DAN targetTabungan > 0) ATAU (targetTabungan <= 0) MAKA
        statusKeuangan <- "Target tabungan telah tercapai"
        jumlahPeriodeTambahan <- 0
        sisaTarget <- 0.0
    LAINNYA JIKA tabunganPerPeriode == 0 MAKA
        statusKeuangan <- "Impas, belum ada penambahan tabungan"
        jumlahPeriodeTambahan <- NULL
    LAINNYA JIKA tabunganPerPeriode < 0 MAKA
        statusKeuangan <- "Defisit, pengeluaran melebihi pemasukan"
        jumlahPeriodeTambahan <- NULL
    LAINNYA
        statusKeuangan <- "Surplus, target dalam proses pencapaian"
        jumlahPeriodeTambahan <- CEIL(sisaTarget / tabunganPerPeriode)
    AKHIR JIKA

    KEMBALIKAN (totalPengeluaran, tabunganPerPeriode, saldoSetelahSatuPeriode, 
                persentaseTabungan, sisaTarget, jumlahPeriodeTambahan, statusKeuangan)
SELESAI
```

---

## 5. FLOWCHART DALAM BENTUK TEKS

```text
[MULAI]
   │
   ▼
[Input Data Mahasiswa: Nama, Saldo Awal, Pemasukan, Pengeluaran, Target]
   │
   ▼
<Validasi Data Input Valid?> ──(TIDAK)──► [Kembalikan Error ke Form] ──► [SELESAI]
   │ (YA)
   ▼
[Hitung: Total Pengeluaran = Makan + Transportasi + Lainnya]
   │
   ▼
[Hitung: Tabungan Per Periode = Pemasukan - Total Pengeluaran]
   │
   ▼
[Hitung: Saldo Setelah 1 Periode = Saldo Awal + Tabungan Per Periode]
   │
   ▼
<Pemasukan > 0?> ──(TIDAK)──► [Persentase Tabungan = 0%]
   │ (YA)                           ▲
   ▼                                │
[Persentase = (Tabungan / Pemasukan) * 100%]
   │
   ▼
[Hitung Sisa Target = Target Tabungan - Saldo Setelah 1 Periode]
   │
   ▼
<Saldo Setelah 1 Periode >= Target?> ──(YA)──► [Status: "Target tabungan telah tercapai"]
   │ (TIDAK)                                   [Periode Tambahan = 0]
   ▼                                                   │
<Tabungan Per Periode == 0?> ──(YA)──────────► [Status: "Impas, belum ada penambahan"]
   │ (TIDAK)                                   [Periode Tambahan = NULL]
   ▼                                                   │
<Tabungan Per Periode < 0?> ──(YA)───────────► [Status: "Defisit, pengeluaran melebihi"]
   │ (TIDAK)                                   [Periode Tambahan = NULL]
   ▼                                                   │
[Status: "Surplus, target dalam proses"]               │
[Periode Tambahan = CEIL(Sisa Target / Tabungan)]      │
   │                                                   │
   └─────────────────────────┬─────────────────────────┘
                             ▼
     [Simpan Seluruh Data ke Database MySQL (tabungan_mahasiswa)]
                             │
                             ▼
     [Tampilkan Halaman Detail Hasil & Pesan Sukses]
                             │
                             ▼
                          [SELESAI]
```

---

## 6. PENJELASAN KOMPONEN MVC, ROUTE, MIGRATION, DAN DATABASE

Aplikasi ini menggunakan pola arsitektur standar industri **Model-View-Controller (MVC)** yang disediakan oleh framework Laravel:

```text
┌─────────────────┐
│     Browser     │
└────────┬────────┘
         │ 1. HTTP Request (GET / POST / PUT / DELETE)
         ▼
┌─────────────────┐
│   routes/web.php│ ──► Meneruskan URL ke Method Controller yang sesuai
└────────┬────────┘
         │ 2. Memanggil Method
         ▼
┌─────────────────┐
│   Controller    │ ── 3. Validasi & Hitung Algoritma
└────┬───────┬────┘
     │       │
     │ 4. ORM Query       5. Meneruskan Data ke Tampilan
     ▼       ▼
┌─────────┐ ┌─────────┐
│  Model  │ │  View   │ ──► Menghasilkan HTML & Bootstrap 5 ke Browser
└────┬────┘ └─────────┘
     │
     ▼ 4b. SQL Query
┌─────────────────┐
│  Database MySQL │
└─────────────────┘
```

1. **Route (`routes/web.php`)**:
   - Berfungsi sebagai "pintu gerbang" aplikasi yang memetakan URL dan metode HTTP (GET, POST, PUT, DELETE) ke controller.
   - Menggunakan `Route::resource('tabungan', TabunganMahasiswaController::class)` untuk menghasilkan 7 rute RESTful standar.
2. **Controller (`app/Http/Controllers/TabunganMahasiswaController.php`)**:
   - Berfungsi sebagai "otak logika" aplikasi.
   - Menerima request, memvalidasi form, menjalankan fungsi algoritma matematika, memanggil model untuk interaksi database, dan memilih view mana yang ditampilkan.
3. **Model (`app/Models/TabunganMahasiswa.php`)**:
   - Berfungsi sebagai representasi tabel database dalam bentuk objek PHP (Eloquent ORM).
   - Mengatur nama tabel (`$table`), kolom yang boleh diisi (`$fillable`), konversi tipe data (`$casts`), dan fungsi pembantu seperti format Rupiah (`formatRupiah`) dan badge status.
4. **View (`resources/views/`)**:
   - Berfungsi sebagai antarmuka pengguna (tampilan visual) menggunakan mesin templating Blade Laravel.
   - Menampilkan formulir, kartu informasi, tabel riwayat, dan modal konfirmasi hapus data.
5. **Migration (`database/migrations/xxxx_create_tabungan_mahasiswa_table.php`)**:
   - Berfungsi sebagai "version control" untuk skema database.
   - Membuat dan memodifikasi struktur tabel MySQL secara otomatis melalui perintah kode PHP tanpa perlu mengetik query `CREATE TABLE` manual di phpMyAdmin.
6. **Database MySQL**:
   - Sistem Manajemen Basis Data Relasional (RDBMS) yang menyimpan seluruh data secara permanen di server.

---

## 7. PENJELASAN STRUKTUR KOLOM DATABASE (`tabungan_mahasiswa`)

Tabel utama yang dibuat pada database `db_tabungan_mahasiswa` adalah `tabungan_mahasiswa` dengan struktur kolom sebagai berikut:

| Nama Kolom | Tipe Data MySQL | Keterangan & Alasan Penggunaan |
|---|---|---|
| `id` | `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY` | Kunci utama unik untuk setiap baris data perhitungan mahasiswa. |
| `nama_mahasiswa` | `VARCHAR(150)` | Menyimpan nama lengkap mahasiswa (Wajib diisi). |
| `saldo_awal` | `DECIMAL(15, 2)` | Saldo tabungan yang sudah dimiliki saat awal perhitungan. Digunakan tipe `DECIMAL` agar perhitungan nominal uang presisi dan tidak mengalami masalah floating-point. |
| `pemasukan_per_periode` | `DECIMAL(15, 2)` | Jumlah uang saku/gaji yang diterima secara rutin. |
| `periode_pemasukan` | `ENUM('harian', 'mingguan', 'bulanan')` | Jenis siklus penerimaan pemasukan dan pengeluaran. |
| `pengeluaran_makan` | `DECIMAL(15, 2)` | Nominal biaya makan dan minum per periode. |
| `pengeluaran_transportasi` | `DECIMAL(15, 2)` | Nominal biaya bensin/transportasi umum per periode. |
| `pengeluaran_lainnya` | `DECIMAL(15, 2)` | Nominal biaya lain-lain (pulsa, kosmetik, fotokopi, hiburan). |
| `target_tabungan` | `DECIMAL(15, 2)` | Jumlah saldo tabungan akhir yang ingin dicapai mahasiswa. |
| `total_pengeluaran` | `DECIMAL(15, 2)` | Hasil penjumlahan: $Makan + Transportasi + Lainnya$. |
| `tabungan_per_periode` | `DECIMAL(15, 2)` | Hasil selisih: $Pemasukan - Total Pengeluaran$. Bernilai negatif jika defisit. |
| `saldo_setelah_satu_periode` | `DECIMAL(15, 2)` | Akumulasi saldo: $Saldo Awal + Tabungan Per Periode$. |
| `persentase_tabungan` | `DECIMAL(8, 2)` | Persentase tabungan terhadap pemasukan dalam persen (%). |
| `sisa_target` | `DECIMAL(15, 2)` | Kekurangan dana untuk mencapai target tabungan. |
| `jumlah_periode_tambahan` | `INT NULL` | Estimasi berapa periode lagi hingga target tercapai (pembulatan `ceil`). Bernilai `0` jika target telah tercapai, dan `NULL` jika impas/defisit. |
| `status_keuangan` | `VARCHAR(100)` | Label klasifikasi kondisi keuangan mahasiswa (Target Tercapai / Surplus / Impas / Defisit). |
| `tanggal_pencatatan` | `DATE NULL` | Tanggal dilakukannya perhitungan/simulasi keuangan. |
| `created_at` & `updated_at` | `TIMESTAMP` | Waktu otomatis saat data dibuat dan diperbarui (bawaan Laravel). |

---

## 8. PENJELASAN FUNGSI PENTING PADA CONTROLLER

File: `app/Http/Controllers/TabunganMahasiswaController.php`

1. **`index(Request $request)`**:
   - Mengambil data riwayat tabungan dengan fitur pencarian nama (`$request->cari`) dan paginasi 10 baris per halaman.
   - Menghitung statistik agregat untuk kartu ringkasan dashboard (total mahasiswa, total saldo akumulasi, rata-rata tabungan, jumlah mahasiswa target tercapai, jumlah defisit).
   - Mengembalikan view `tabungan.index`.
2. **`create()`**:
   - Menampilkan halaman formulir input data baru (`tabungan.create`).
3. **`store(Request $request)`**:
   - Memvalidasi input request menggunakan method `validasiInput()`.
   - Menjalankan algoritma perhitungan pada method `hitungTabungan()`.
   - Menggabungkan data input dan hasil perhitungan, lalu menyimpannya ke MySQL via `TabunganMahasiswa::create($dataSimpan)`.
   - Mengarahkan (*redirect*) ke halaman detail (`tabungan.show`) dengan flash message sukses.
4. **`show($id)`**:
   - Mengambil satu data mahasiswa berdasarkan ID (`findOrFail($id)`).
   - Menampilkan view `tabungan.show` dengan kartu informasi dan perincian langkah-langkah rumus matematis.
5. **`edit($id)`**:
   - Mengambil data lama mahasiswa berdasarkan ID dan menampilkan view formulir edit `tabungan.edit`.
6. **`update(Request $request, $id)`**:
   - Memvalidasi data form yang telah diedit.
   - **MENGHITUNG ULANG** seluruh variabel kalkulasi melalui `hitungTabungan()` dengan angka-angka terbaru.
   - Memperbarui rekaman di database MySQL (`$tabungan->update($dataUpdate)`).
   - Mengalihkan ke halaman detail dengan notifikasi sukses pembaruan.
7. **`destroy($id)`**:
   - Menghapus rekaman data mahasiswa dari database MySQL (`$tabungan->delete()`).
   - Mengalihkan kembali ke dashboard dengan notifikasi sukses penghapusan.
8. **`validasiInput(Request $request)`**:
   - Memastikan seluruh kolom wajib diisi, bertipe numerik, dan tidak bernilai negatif (`min:0`).
   - Menyediakan pesan validasi dalam Bahasa Indonesia yang ramah dan jelas.
9. **`hitungTabungan(array $data)`**:
   - Fungsi inti algoritma matematika yang memisahkan logika bisnis dari antarmuka visual.
   - Mengamankan pembagian dengan nol dan menentukan status serta estimasi periode tambahan secara akurat.

---

## 9. CONTOH PERHITUNGAN MANUAL (STUDI KASUS NYATA)

Berikut adalah 5 studi kasus nyata yang dapat Anda jadikan contoh saat presentasi kepada dosen:

### Kasus 1: Mahasiswa Surplus (Target dalam Proses)
- **Nama**: Budi Santoso
- **Saldo Awal**: Rp 1.000.000
- **Pemasukan Bulanan**: Rp 2.000.000
- **Pengeluaran**: Makan (Rp 700.000), Transport (Rp 300.000), Lainnya (Rp 200.000)
- **Target Tabungan**: Rp 5.000.000
- **Langkah Perhitungan**:
  1. $\text{Total Pengeluaran} = 700.000 + 300.000 + 200.000 = \text{Rp } 1.200.000$
  2. $\text{Tabungan per Periode} = 2.000.000 - 1.200.000 = \text{Rp } 800.000$
  3. $\text{Saldo Setelah 1 Periode} = 1.000.000 + 800.000 = \text{Rp } 1.800.000$
  4. $\text{Persentase Tabungan} = (800.000 / 2.000.000) \times 100\% = 40\%$
  5. $\text{Sisa Target} = 5.000.000 - 1.800.000 = \text{Rp } 3.200.000$
  6. $\text{Periode Tambahan} = \text{ceil}(3.200.000 / 800.000) = \text{ceil}(4,0) = 4 \text{ bulan lagi}$
  7. **Status Keuangan**: `"Surplus, target dalam proses pencapaian"`

### Kasus 2: Mahasiswa Target Telah Tercapai
- **Nama**: Siti Rahmawati
- **Saldo Awal**: Rp 4.500.000
- **Pemasukan Bulanan**: Rp 1.500.000
- **Pengeluaran**: Makan (Rp 600.000), Transport (Rp 250.000), Lainnya (Rp 150.000) $\rightarrow$ Total Rp 1.000.000
- **Target Tabungan**: Rp 5.000.000
- **Langkah Perhitungan**:
  1. $\text{Tabungan per Periode} = 1.500.000 - 1.000.000 = \text{Rp } 500.000$
  2. $\text{Saldo Setelah 1 Periode} = 4.500.000 + 500.000 = \text{Rp } 5.000.000$
  3. Karena Saldo Setelah 1 Periode ($\text{Rp } 5.000.000$) $\ge$ Target ($\text{Rp } 5.000.000$), maka:
  4. $\text{Sisa Target} = \text{Rp } 0$
  5. $\text{Periode Tambahan} = 0$
  6. **Status Keuangan**: `"Target tabungan telah tercapai"`

### Kasus 3: Mahasiswa Impas (Pemasukan = Pengeluaran)
- **Nama**: Rian Pratama
- **Saldo Awal**: Rp 500.000
- **Pemasukan Bulanan**: Rp 1.000.000
- **Pengeluaran**: Makan (Rp 600.000), Transport (Rp 250.000), Lainnya (Rp 150.000) $\rightarrow$ Total Rp 1.000.000
- **Target Tabungan**: Rp 3.000.000
- **Langkah Perhitungan**:
  1. $\text{Tabungan per Periode} = 1.000.000 - 1.000.000 = \text{Rp } 0$
  2. $\text{Saldo Setelah 1 Periode} = 500.000 + 0 = \text{Rp } 500.000$
  3. $\text{Persentase Tabungan} = 0\%$
  4. Karena Tabungan per Periode $= 0$, maka saldo tidak akan pernah bertambah jika pola belanja tidak diubah.
  5. $\text{Periode Tambahan} = \text{NULL / -}$
  6. **Status Keuangan**: `"Impas, belum ada penambahan tabungan"`

### Kasus 4: Mahasiswa Defisit (Pengeluaran > Pemasukan)
- **Nama**: Dewi Lestari
- **Saldo Awal**: Rp 200.000
- **Pemasukan Bulanan**: Rp 1.200.000
- **Pengeluaran**: Makan (Rp 800.000), Transport (Rp 400.000), Lainnya (Rp 300.000) $\rightarrow$ Total Rp 1.500.000
- **Target Tabungan**: Rp 4.000.000
- **Langkah Perhitungan**:
  1. $\text{Tabungan per Periode} = 1.200.000 - 1.500.000 = -\text{Rp } 300.000 \text{ (Defisit)}$
  2. $\text{Saldo Setelah 1 Periode} = 200.000 + (-300.000) = -\text{Rp } 100.000$
  3. $\text{Persentase Tabungan} = -25\%$
  4. Karena tabungan negatif, target tidak mungkin tercapai tanpa evaluasi pengeluaran.
  5. $\text{Periode Tambahan} = \text{NULL / -}$
  6. **Status Keuangan**: `"Defisit, pengeluaran melebihi pemasukan"`

### Kasus 5: Pemasukan Nol (Aman dari Division by Zero)
- **Nama**: Fajar Nugraha
- **Saldo Awal**: Rp 500.000
- **Pemasukan**: Rp 0
- **Pengeluaran**: Makan (Rp 100.000), Transport (Rp 50.000), Lainnya (Rp 50.000) $\rightarrow$ Total Rp 200.000
- **Target Tabungan**: Rp 1.000.000
- **Langkah Perhitungan**:
  1. $\text{Tabungan per Periode} = 0 - 200.000 = -\text{Rp } 200.000$
  2. $\text{Persentase Tabungan}$: Karena pemasukan $0$, kode mengeksekusi blok aman sehingga persentase diatur ke $0.0\%$ (mencegah fatal error pembagian dengan nol).
  3. **Status Keuangan**: `"Defisit, pengeluaran melebihi pemasukan"`

---

## 10. CONTOH PENGUJIAN INPUT DAN OUTPUT (TEST CASES)

| No | Skenario Pengujian | Input Data | Hasil yang Diharapkan | Status Hasil |
|---|---|---|---|---|
| 1 | Pemasukan > Pengeluaran | Saldo Awal: 1jt, Masuk: 2jt, Keluar: 1.2jt, Target: 5jt | Tabungan: +800rb, Saldo: 1.8jt, Periode Tambahan: 4 bln, Status: "Surplus" | **BERHASIL (PASS)** |
| 2 | Pemasukan = Pengeluaran | Saldo Awal: 500rb, Masuk: 1jt, Keluar: 1jt, Target: 3jt | Tabungan: 0, Saldo: 500rb, Periode: NULL, Status: "Impas" | **BERHASIL (PASS)** |
| 3 | Pengeluaran > Pemasukan | Saldo Awal: 200rb, Masuk: 1.2jt, Keluar: 1.5jt, Target: 4jt | Tabungan: -300rb, Saldo: -100rb, Periode: NULL, Status: "Defisit" | **BERHASIL (PASS)** |
| 4 | Saldo Awal Mencapai Target | Saldo Awal: 4.5jt, Masuk: 1.5jt, Keluar: 1jt, Target: 5jt | Saldo 1 Periode: 5jt >= Target, Periode: 0, Status: "Target tabungan telah tercapai" | **BERHASIL (PASS)** |
| 5 | Pemasukan Nol (0) | Masuk: 0, Keluar: 200rb, Target: 1jt | Persentase: 0%, Status: "Defisit", Tidak ada error pembagian dengan nol | **BERHASIL (PASS)** |
| 6 | Input Negatif | Saldo Awal: -5000, Pemasukan: -1000 | Form ditolak, muncul pesan error validasi "tidak boleh bernilai negatif" | **BERHASIL (PASS)** |
| 7 | Input Kosong | Nama Mahasiswa: "" (dikosongkan) | Form ditolak, muncul pesan "Nama mahasiswa wajib diisi" | **BERHASIL (PASS)** |
| 8 | Input Non-Numerik | Pemasukan: "satu juta" | Form ditolak, muncul pesan "harus berupa angka" | **BERHASIL (PASS)** |
| 9 | Simpan Data ke MySQL | Klik "Hitung dan Simpan" pada form valid | Data berhasil tersimpan ke tabel `tabungan_mahasiswa`, redirect ke detail | **BERHASIL (PASS)** |
| 10 | Edit & Hitung Ulang | Ubah pengeluaran makan dari 700rb menjadi 200rb | Seluruh nilai tabungan & sisa target otomatis diperbarui di database | **BERHASIL (PASS)** |
| 11 | Hapus Data dengan Konfirmasi | Klik tombol hapus, konfirmasi di Modal | Data terhapus dari MySQL, tabel riwayat berkurang 1 baris | **BERHASIL (PASS)** |

---

## 11. 15+ PERTANYAAN KRITIS DOSEN BESERTA KUNCI JAWABANNYA

### Q1: Mengapa kamu memilih arsitektur MVC di Laravel untuk aplikasi ini?
**Jawaban**:
> "Arsitektur MVC (*Model-View-Controller*) memisahkan aplikasi menjadi tiga komponen dengan tanggung jawab terisolasi (*Separation of Concerns*). **Model** bertanggung jawab atas komunikasi data dengan MySQL melalui Eloquent ORM. **View** (Blade) bertanggung jawab hanya atas antarmuka visual kepada pengguna. Sedangkan **Controller** menjadi jembatan yang menangani validasi dan alur logika perhitungan matematika. Dengan cara ini, kode program menjadi bersih, mudah dirawat, mudah diuji, dan tidak terjadi penumpukan kode logika di dalam file tampilan."

### Q2: Di mana algoritma perhitungan tabungan dieksekusi? Mengapa tidak di JavaScript browser saja?
**Jawaban**:
> "Perhitungan utama dieksekusi di sisi server (**Server-Side**) menggunakan bahasa pemrograman PHP di dalam method `hitungTabungan()` pada `TabunganMahasiswaController`. Kami tetap menyediakan JavaScript sederhana di browser semata-mata untuk fitur *Live Preview* saat pengguna mengetik agar interaktif. Namun, ketika tombol 'Hitung dan Simpan' ditekan, seluruh perhitungan dihitung ulang dan diverifikasi oleh PHP di server. Alasan utamanya adalah integritas dan keamanan data: JavaScript di browser klien dapat dengan mudah dimanipulasi atau dimatikan oleh pengguna, sedangkan perhitungan di server menjamin bahwa data yang tersimpan di database MySQL 100% konsisten, valid, dan dapat dipercaya."

### Q3: Bagaimana aplikasi menangani kemungkinan pembagian dengan nol (Division by Zero) pada persentase tabungan?
**Jawaban**:
> "Pada method `hitungTabungan()`, kami menerapkan pengecekan kondisional eksplisit `if ($pemasukan > 0)`. Jika pemasukan lebih besar dari nol, rumus persentase `($tabunganPerPeriode / $pemasukan) * 100` baru dieksekusi. Jika pemasukan bernilai nol, sistem langsung menetapkan persentase ke `0.0%` tanpa melakukan operasi pembagian. Hal ini mencegah terjadinya *Fatal Error / DivisionByZeroError* di PHP."

### Q4: Mengapa fungsi `ceil()` digunakan dalam menghitung jumlah periode tambahan?
**Jawaban**:
> "Fungsi `ceil()` (singkatan dari *ceiling*) berfungsi untuk membulatkan bilangan pecahan ke atas ke bilangan bulat terdekat. Dalam konteks menabung, periode menabung (hari/minggu/bulan) tidak bisa bernilai pecahan. Sebagai contoh, jika sisa target membutuhkan waktu $2,3$ bulan menabung, maka dalam $2$ bulan target tersebut belum terpenuhi. Mahasiswa memerlukan $3$ kali periode menabung agar target tabungan tersebut benar-benar terpenuhi secara penuh. Oleh karena itu, pembulatan ke atas secara matematis adalah solusi paling logis dan realistis."

### Q5: Mengapa tipe data kolom keuangan di database menggunakan `DECIMAL(15, 2)` dan bukan `FLOAT` atau `DOUBLE`?
**Jawaban**:
> "Tipe data `FLOAT` dan `DOUBLE` adalah representasi angka *approximate* (perkiraan) berbasis IEEE 754 floating-point yang rentan mengalami *rounding error* (kesalahan pembulatan koma desimal). Sebaliknya, `DECIMAL(15, 2)` adalah tipe data *fixed-point* eksak yang menyimpan angka numerik secara presisi hingga 15 digit dengan 2 angka di belakang koma. Untuk aplikasi finansial atau perbankan, penggunaan `DECIMAL` adalah standar industri demi mencegah selisih nilai uang sekecil apa pun."

### Q6: Apa fungsi dari `@csrf` pada setiap formulir Blade?
**Jawaban**:
> "`@csrf` adalah direktif Blade Laravel yang menyisipkan *hidden input* berisi token keamanan CSRF (*Cross-Site Request Forgery*). Token ini memverifikasi bahwa permintaan HTTP POST/PUT yang masuk benar-benar berasal dari pengguna sah yang mengisi formulir di situs kita, bukan dari serangan skrip berbahaya situs pihak ketiga yang mencoba memalsukan request."

### Q7: Bagaimana mekanisme validasi data pada Laravel dan apa yang terjadi jika input salah?
**Jawaban**:
> "Validasi dilakukan di dalam Controller menggunakan method `$request->validate($aturan, $pesan)`. Aturan yang diterapkan antara lain `required` (wajib diisi), `numeric` (harus angka), `min:0` (tidak boleh negatif), dan `in:harian,mingguan,bulanan`. Jika terdapat input yang tidak sesuai aturan, Laravel secara otomatis membatalkan eksekusi, menyimpan pesan kesalahan ke dalam *session flash*, dan mengarahkan kembali (*redirect back*) ke halaman form. Di file Blade, kita menampilkan pesan tersebut menggunakan variabel bawaan `$errors` dan class CSS `is-invalid`."

### Q8: Apa perbedaan antara Migration dengan membuat tabel langsung di phpMyAdmin?
**Jawaban**:
> "Membuat tabel langsung di phpMyAdmin bersifat manual, tidak memiliki rekam jejak, dan sulit direplikasi ke komputer rekan kerja atau server hosting lain. Sementara itu, **Migration** adalah *version control* untuk database dalam bentuk kode program Laravel. Dengan migration, seluruh tim dapat membuat skema database yang sama persis hanya dengan menjalankan perintah `php artisan migrate`. Selain itu, migration mendukung fitur *rollback* untuk membatalkan perubahan struktur tabel jika diperlukan."

### Q9: Apa itu Eloquent ORM dan bagaimana contoh penggunaannya di aplikasi ini?
**Jawaban**:
> "ORM (*Object-Relational Mapping*) adalah teknik yang memetakan tabel database ke dalam bentuk objek PHP berorientasi objek. Di Laravel, Eloquent adalah implementasi ORM bawaan. Contoh penggunaannya di aplikasi ini:
> - Menyimpan data baru: `TabunganMahasiswa::create($dataSimpan);`
> - Mengambil semua data berurut: `TabunganMahasiswa::orderBy('created_at', 'desc')->paginate(10);`
> - Mencari data berdasarkan ID: `TabunganMahasiswa::findOrFail($id);`
> - Memperbarui data: `$tabungan->update($dataUpdate);`
> - Menghapus data: `$tabungan->delete();`
> Dengan Eloquent, kita tidak perlu menulis query SQL mentah (`INSERT INTO`, `SELECT`, `UPDATE`, `DELETE`) secara manual."

### Q10: Bagaimana aplikasi menangani kondisi ketika pengeluaran mahasiswa lebih besar dari pemasukan (Defisit)?
**Jawaban**:
> "Ketika pengeluaran melebihi pemasukan, nilai `tabungan_per_periode` menjadi negatif. Dalam method `hitungTabungan()`, kondisi ini ditangkap oleh percabangan `elseif ($tabunganPerPeriode < 0)`. Sistem menetapkan `status_keuangan` menjadi `'Defisit, pengeluaran melebihi pemasukan'` dan nilai `jumlah_periode_tambahan` diatur ke `null` (ditampilkan tanda strip `-` di antarmuka). Kami secara sengaja tidak menghitung rumus pembagian periode pada kondisi defisit karena estimasi tersebut tidak logis (saldo tabungan justru terus berkurang)."

### Q11: Apa fungsi dari file `.env` di Laravel?
**Jawaban**:
> "File `.env` (*Environment Configuration*) digunakan untuk menyimpan konfigurasi spesifik lingkungan kerja yang sensitif dan dinamis, seperti koneksi database (`DB_CONNECTION=mysql`, `DB_HOST=127.0.0.1`, `DB_DATABASE=db_tabungan_mahasiswa`), nama aplikasi, mode debug (`APP_DEBUG=true`), dan kunci enkripsi aplikasi (`APP_KEY`). Dengan memisahkan konfigurasi di `.env`, kita tidak perlu mengubah kode inti aplikasi saat berpindah dari lingkungan *development* ke server *production*."

### Q12: Bagaimana fitur pencarian nama mahasiswa diimplementasikan?
**Jawaban**:
> "Fitur pencarian diimplementasikan pada method `index()` di `TabunganMahasiswaController`. Controller memeriksa parameter URL `cari` melalui `$request->filled('cari')`. Jika ada, Controller menambahkan klausa SQL `LIKE`: `$query->where('nama_mahasiswa', 'like', '%' . $request->cari . '%')`. Jika tidak ada kata kunci pencarian, query akan mengambil seluruh data secara default."

### Q13: Mengapa kamu menggunakan Bootstrap 5 dan bukan Tailwind CSS?
**Jawaban**:
> "Bootstrap 5 dipilih karena sangat stabil, memiliki komponen UI lengkap (Grid System, Modal, Badges, Alert, Progress Bar, Cards) yang langsung siap pakai dengan CDN tanpa memerlukan proses instalasi node package yang kompleks. Struktur class CSS Bootstrap 5 (`card`, `btn`, `table`, `alert`) sangat mudah dibaca dan dipahami bagi mahasiswa pemula serta penguji saat meninjau kode tampilan Blade."

### Q14: Bagaimana cara kerja fitur Edit dan Hitung Ulang?
**Jawaban**:
> "Ketika pengguna mengedit nilai input pada form edit dan menekan tombol 'Perbarui & Hitung Ulang', data dikirim melalui HTTP method `PUT` ke `TabunganMahasiswaController@update`. Controller tidak hanya memperbarui kolom input dasar, tetapi mengeksekusi kembali method `hitungTabungan($validatedData)`. Dengan demikian, seluruh atribut turunan (total pengeluaran, tabungan per periode, saldo terkini, sisa target, estimasi periode, dan status) otomatis dikalkulasi ulang berdasarkan angka terbaru dan diperbarui di MySQL."

### Q15: Apa yang terjadi jika pengguna mencoba menghapus data yang tidak ada di database?
**Jawaban**:
> "Kami menggunakan method `findOrFail($id)` milik Eloquent. Jika ID yang dicari ditemukan, objek model akan dikembalikan. Namun jika ID tidak ditemukan (misalnya data sudah dihapus atau ID sengaja dimanipulasi di URL), Laravel secara otomatis memunculkan pengecualian `ModelNotFoundException` yang menghasilkan respon HTTP `404 Not Found`, sehingga mencegah terjadinya crash atau celah keamanan."

---

## 12. PANDUAN DAN NASKAH PRESENTASI UJIAN DI DEPAN DOSEN

Gunakan panduan 5 langkah presentasi berikut agar penjelasan Anda terstruktur dan memikat dosen:

### Menit 1: Pembukaan & Latar Belakang
> *"Selamat pagi/siang Bapak/Ibu Dosen Penguji. Terima kasih atas kesempatan yang diberikan. Hari ini saya akan mempresentasikan aplikasi tugas saya yang berjudul **Sistem Perhitungan Tabungan Mahasiswa** berbasis framework Laravel dan database MySQL. Aplikasi ini dibuat untuk menyelesaikan permasalahan manajemen keuangan yang kerap dialami mahasiswa, yaitu ketidakjelasan alokasi pengeluaran, ketiadaan simulasi target tabungan yang presisi, dan kurangnya peringatan dini saat terjadi defisit keuangan."*

### Menit 2: Penjelasan Arsitektur & Teknologi
> *"Aplikasi ini dibangun menggunakan arsitektur standar **Model-View-Controller (MVC)**. Untuk database, kami menggunakan **MySQL** dengan tabel `tabungan_mahasiswa` yang dibangun melalui **Migration Laravel**. Di sisi backend, kami memanfaatkan controller `TabunganMahasiswaController` untuk validasi dan algoritma perhitungan. Di sisi antarmuka, kami menggunakan template engine **Blade** yang dipadukan dengan desain modern bertema biru tua, putih, dan aksen hijau menggunakan **Bootstrap 5**."*

### Menit 3: Demonstrasi Fitur & Algoritma Inti
> *(Buka halaman browser `http://localhost:8000`)*
> *"Berikut adalah halaman Dashboard. Di sini terdapat kartu ringkasan statistik dan tabel riwayat. Sekarang saya akan mendemonstrasikan penambahan data baru..."*
> *(Buka menu Hitung Tabungan Baru, isi data Budi Santoso)*
> *"Bapak/Ibu dapat melihat saat saya mengetik angka, terdapat Live Preview di sisi kanan. Ketika saya menekan tombol 'Hitung dan Simpan', data dikirimkan ke server Laravel. Di server, Laravel mengeksekusi method `hitungTabungan()`:
> 1. Menghitung Total Pengeluaran dari penjumlahan pos makan, transport, dan lainnya.
> 2. Menghitung Tabungan per Periode: Pemasukan dikurangi Total Pengeluaran.
> 3. Menghitung Saldo Setelah Satu Periode: Saldo Awal ditambah Tabungan Bersih.
> 4. Menggunakan fungsi `ceil()` pada Sisa Target dibagi Tabungan Bersih untuk mendapatkan estimasi jumlah periode tambahan secara realistis.
> Seluruh hasil ini tersimpan di MySQL dan langsung ditampilkan di halaman detail lengkap dengan breakdown rumusnya."*

### Menit 4: Demonstrasi Kasus Defisit, Impas, dan CRUD
> *"Aplikasi ini juga cerdas dalam menangani berbagai kondisi keuangan:
> - Jika pengeluaran sama dengan pemasukan, status berubah menjadi 'Impas'.
> - Jika pengeluaran lebih besar, status menjadi 'Defisit' dan estimasi periode dinonaktifkan secara aman agar tidak menyesatkan mahasiswa.
> - Jika pemasukan nol, sistem terlindungi dari fatal error pembagian dengan nol.
> Selain itu, fitur CRUD berjalan penuh: kita dapat mengedit data yang secara otomatis memicu perhitungan ulang, serta menghapus data dengan modal konfirmasi aman."*

### Menit 5: Penutup
> *"Demikian presentasi dari Sistem Perhitungan Tabungan Mahasiswa. Seluruh kode program disusun dengan struktur yang rapi, komentar kode yang jelas, dan pengujian fitur yang terbukti lulus uji. Saya siap menerima pertanyaan maupun masukan dari Bapak/Ibu Dosen. Terima kasih."*

---

## 13. PANDUAN MENJALANKAN LARAVEL VIA VS CODE DAN TERMINAL

Berikut adalah langkah-langkah menjalankan aplikasi melalui terminal di Visual Studio Code:

1. **Buka Proyek di Visual Studio Code**:
   - Buka aplikasi Visual Studio Code.
   - Klik menu **File** $\rightarrow$ **Open Folder...**
   - Pilih folder proyek: `d:\Semester 5\Clodcomputing\tabuangan\tabungan`
2. **Buka Terminal Terintegrasi**:
   - Tekan kombinasi tombol **Ctrl + `** (tanda petik miring) atau klik menu **Terminal** $\rightarrow$ **New Terminal**.
3. **Pastikan Berada di Direktori Proyek Laravel**:
   Ketik perintah:
   ```powershell
   pwd
   ```
   Pastikan terminal berada di dalam folder `tabungan`. Jika masih di folder induk, masuk dengan:
   ```powershell
   cd tabungan
   ```
4. **Jalankan Server Development Laravel**:
   Ketik perintah berikut lalu tekan Enter:
   ```powershell
   php artisan serve
   ```
   Terminal akan menampilkan teks:
   ```text
   INFO  Server running on [http://127.0.0.1:8000].
   ```
5. **Buka di Browser**:
   - Buka browser (Google Chrome, Edge, atau Firefox).
   - Akses alamat: `http://localhost:8000` atau `http://127.0.0.1:8000`
6. **Untuk Menghentikan Server**:
   - Tekan tombol **Ctrl + C** di terminal.

---

## 14. PANDUAN MEMBUAT DATABASE MENGGUNAKAN PHPMYADMIN

Sebelum menjalankan migration Laravel, database MySQL harus sudah dibuat di phpMyAdmin:

1. **Pastikan XAMPP / MySQL Sedang Berjalan**:
   - Buka aplikasi **XAMPP Control Panel**.
   - Pada baris **Apache** dan **MySQL**, pastikan keduanya berstatus **Running** (tombol berwarna hijau). Jika belum, klik tombol **Start**.
2. **Buka phpMyAdmin di Browser**:
   - Buka browser dan ketik alamat: `http://localhost/phpmyadmin`
3. **Membuat Database Baru**:
   - Di panel navigasi sebelah kiri, klik menu **New** (atau **Baru**).
   - Pada kolom **Database name** (Nama basis data), ketik:
     ```text
     db_tabungan_mahasiswa
     ```
   - Pada pilihan Collation (Penyortiran), pilih: `utf8mb4_unicode_ci` (atau biarkan default).
   - Klik tombol **Create** (atau **Buat**).
4. Database `db_tabungan_mahasiswa` kini telah berhasil dibuat dan siap menerima migrasi tabel dari Laravel.

---

## 15. PANDUAN MIGRATION, SEEDER, DAN MENJALANKAN APLIKASI DI BROWSER

1. **Periksa Konfigurasi Database di File `.env`**:
   Buka file `.env` di root proyek `tabungan`, pastikan baris berikut sudah sesuai:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=db_tabungan_mahasiswa
   DB_USERNAME=root
   DB_PASSWORD=
   ```
2. **Jalankan Migration untuk Membuat Tabel Otomatis**:
   Buka terminal di folder proyek `tabungan` dan jalankan:
   ```powershell
   php artisan migrate
   ```
   Laravel akan membuat tabel `tabungan_mahasiswa` beserta tabel pendukung lainnya di MySQL.
3. **Jalankan Seeder untuk Mengisi Data Sampel Awal (Opsional)**:
   Untuk langsung mendapatkan 5 data sampel mahasiswa (mencakup kasus surplus, tercapai, impas, dan defisit), jalankan:
   ```powershell
   php artisan db:seed
   ```
4. **Menjalankan Pengujian Otomatis (Automated Test)**:
   Untuk membuktikan bahwa seluruh logika dan rumus berjalan 100% tanpa kesalahan:
   ```powershell
   php artisan test --filter=TabunganMahasiswaTest
   ```
5. **Jalankan Web Server**:
   ```powershell
   php artisan serve
   ```
6. **Akses Aplikasi**:
   Buka peramban Anda dan kunjungi `http://localhost:8000`. Aplikasi siap digunakan dan dipresentasikan!
