@extends('layouts.app')

@section('title', 'TabunganKu - Celengan & Target Impian Mahasiswa')

@section('content')
<!-- Hero Section FinTech TabunganKu -->
<div class="row g-4 mb-4 align-items-stretch">
    <!-- Kolom Kiri: Virtual Digital Card TabunganKu -->
    <div class="col-lg-7">
        <div class="fintech-card h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="chip-icon"></span>
                        <i class="bi bi-wifi fs-4 text-white-50" style="transform: rotate(90deg);"></i>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-white bg-opacity-20 text-black px-3 py-1 rounded-pill small">
                            <i class="bi bi-shield-check me-1"></i> Dashboard Tabungan Mahasiswa
                        </span>
                    </div>
                </div>

                <small class="text-white-50 d-block text-uppercase letter-spacing-1 fw-semibold mb-1" style="letter-spacing: 1px;">
                    Total Saldo Terkumpul di Tabungan
                </small>
                <h1 class="display-5 fw-extrabold text-white mb-2 brand-font">
                    {{ \App\Models\TabunganMahasiswa::formatRupiah($totalAkumulasiSaldo) }}
                </h1>

                <div class="d-flex flex-wrap gap-3 align-items-center text-white-50 small mt-2">
                    <span>
                        <i class="bi bi-bullseye text-warning me-1"></i> Total Target Impian: 
                        <strong class="text-white">{{ \App\Models\TabunganMahasiswa::formatRupiah($totalTargetTabungan) }}</strong>
                    </span>
                    <span>•</span>
                    <span>
                        <i class="bi bi-pie-chart text-info me-1"></i> Rata-rata Tabungan: 
                        <strong class="text-white">{{ \App\Models\TabunganMahasiswa::formatRupiah($rataRataTabungan) }}</strong>
                    </span>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top border-white border-opacity-10 d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <span class="text-white-50 small d-block">Platform Tabungan & Celengan Pintar</span>
                    <strong class="text-white font-monospace">**** **** 8829 TBG-ID</strong>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('tabungan.create') }}" class="btn btn-mint btn-sm px-3 py-2">
                        <i class="bi bi-plus-circle"></i> Buka Celengan Baru
                    </a>
                    <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3 py-2" data-bs-toggle="modal" data-bs-target="#modalKalkulatorGlobal">
                        <i class="bi bi-calculator"></i> Hitung Simulasi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: 4 Kartu Metrik Ringkasan -->
    <div class="col-lg-5">
        <div class="row g-3 h-100">
            <div class="col-6">
                <div class="stat-mini-box h-100">
                    <div class="stat-mini-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-piggy-bank-fill"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-semibold d-block">Celengan Aktif</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ $totalMahasiswa }}</h4>
                        <small class="text-success small fw-semibold">Rencana impian</small>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-mini-box h-100">
                    <div class="stat-mini-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-semibold d-block">Target Tercapai</small>
                        <h4 class="fw-bold mb-0 text-success">{{ $jumlahTargetTercapai }}</h4>
                        <small class="text-muted small">Impian terwujud</small>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-mini-box h-100">
                    <div class="stat-mini-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-semibold d-block">Dalam Proses</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ max(0, $totalMahasiswa - $jumlahTargetTercapai - $jumlahDefisit) }}</h4>
                        <small class="text-muted small">Menuju target</small>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-mini-box h-100">
                    <div class="stat-mini-icon bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-shield-exclamation"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-semibold d-block">Perlu Evaluasi</small>
                        <h4 class="fw-bold mb-0 {{ $jumlahDefisit > 0 ? 'text-danger' : 'text-muted' }}">{{ $jumlahDefisit }}</h4>
                        <small class="text-muted small">{{ $jumlahDefisit > 0 ? 'Arus kas defisit' : 'Keuangan sehat' }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bilah Filter & Tampilan (Nav Tab Celengan vs Tabel Buku Rekening) -->
<div class="card card-custom mb-4 border-0">
    <div class="card-body p-3">
        <div class="row g-3 align-items-center justify-content-between">
            <!-- Mode Tampilan Switcher -->
            <div class="col-md-5">
                <ul class="nav nav-pills p-1 bg-light rounded-pill d-inline-flex" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill px-3 py-1 fw-bold" id="pills-grid-tab" data-bs-toggle="pill" data-bs-target="#pills-grid" type="button" role="tab">
                            <i class="bi bi-grid-fill me-1"></i> Kartu Celengan Impian
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill px-3 py-1 fw-bold" id="pills-table-tab" data-bs-toggle="pill" data-bs-target="#pills-table" type="button" role="tab">
                            <i class="bi bi-list-ul me-1"></i> Buku Tabungan & Rekap
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Form Pencarian & Kategori -->
            <div class="col-md-7">
                <form action="{{ route('tabungan.index') }}" method="GET" class="d-flex flex-wrap gap-2 justify-content-md-end">
                    <!-- Dropdown Kategori -->
                    <select name="kategori" class="form-select form-select-sm rounded-pill w-auto bg-light border-0 fw-semibold" onchange="this.form.submit()">
                        <option value="semua" {{ request('kategori') == 'semua' || !request('kategori') ? 'selected' : '' }}>Semua Kategori</option>
                        <option value="Gadget & Kuliah" {{ request('kategori') == 'Gadget & Kuliah' ? 'selected' : '' }}>💻 Gadget & Kuliah</option>
                        <option value="Pendidikan" {{ request('kategori') == 'Pendidikan' ? 'selected' : '' }}>🎓 Pendidikan / UKT</option>
                        <option value="Dana Darurat" {{ request('kategori') == 'Dana Darurat' ? 'selected' : '' }}>🛡️ Dana Darurat</option>
                        <option value="Kendaraan" {{ request('kategori') == 'Kendaraan' ? 'selected' : '' }}>🛵 Kendaraan</option>
                        <option value="Liburan & Hiburan" {{ request('kategori') == 'Liburan & Hiburan' ? 'selected' : '' }}>🏖️ Liburan & Hiburan</option>
                        <option value="Umum" {{ request('kategori') == 'Umum' ? 'selected' : '' }}>🐷 Tabungan Umum</option>
                    </select>

                    <!-- Input Pencarian -->
                    <div class="input-group input-group-sm w-auto">
                        <input type="text" name="cari" class="form-control rounded-start-pill ps-3 bg-light border-0" placeholder="Cari impian / nama..." value="{{ request('cari') }}">
                        <button type="submit" class="btn btn-navy rounded-end-pill px-3">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>

                    @if(request('cari') || (request('kategori') && request('kategori') != 'semua'))
                        <a href="{{ route('tabungan.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill" title="Reset Filter">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Tab Content -->
<div class="tab-content" id="pills-tabContent">
    <!-- MODE 1: GRID KARTU CELENGAN IMPIAN (Tampilan Utama Web Tabungan) -->
    <div class="tab-pane fade show active" id="pills-grid" role="tabpanel">
        @if($riwayatTabungan->isEmpty())
            <div class="card card-custom p-5 text-center">
                <div class="mb-3">
                    <span class="d-inline-flex p-3 rounded-circle bg-light text-muted fs-1">
                        <i class="bi bi-piggy-bank"></i>
                    </span>
                </div>
                <h4 class="fw-bold text-dark">Belum Ada Celengan Tabungan</h4>
                <p class="text-muted col-md-6 mx-auto mb-4">
                    @if(request('cari') || request('kategori'))
                        Tidak ditemukan celengan dengan kata kunci "<strong>{{ request('cari') ?? request('kategori') }}</strong>".
                    @else
                        Mulai langkah finansial cerdasmu dengan membuat celengan impian pertama untuk gadget, kuliah, atau dana darurat.
                    @endif
                </p>
                <div>
                    <a href="{{ route('tabungan.create') }}" class="btn btn-mint px-4 py-2">
                        <i class="bi bi-plus-circle me-1"></i> Buka Celengan Impian Sekarang
                    </a>
                </div>
            </div>
        @else
            <div class="row g-4">
                @foreach($riwayatTabungan as $item)
                    <div class="col-md-6 col-lg-4">
                        <div class="goal-card">
                            <!-- Header Card: Icon, Kategori, & Menu -->
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="goal-icon-badge {{ $item->kategori_badge }}">
                                            <i class="bi {{ $item->kategori_icon }}"></i>
                                        </div>
                                        <div>
                                            <span class="badge {{ $item->kategori_badge }} rounded-pill px-2 py-1 small">
                                                {{ $item->kategori ?? 'Tabungan' }}
                                            </span>
                                            <small class="text-muted d-block mt-1">
                                                <i class="bi bi-person me-1"></i>{{ $item->nama_mahasiswa }}
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Dropdown Menu Singkat -->
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light rounded-circle text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm rounded-3">
                                            <li>
                                                <a class="dropdown-item py-2" href="{{ route('tabungan.show', $item->id) }}">
                                                    <i class="bi bi-eye text-primary me-2"></i>Lihat Rincian Buku
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-2" href="{{ route('tabungan.edit', $item->id) }}">
                                                    <i class="bi bi-pencil text-warning me-2"></i>Ubah Target Impian
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button type="button" class="dropdown-item py-2 text-danger" data-bs-toggle="modal" data-bs-target="#modalHapus{{ $item->id }}">
                                                    <i class="bi bi-trash me-2"></i>Hapus Celengan
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Judul Celengan Impian -->
                                <h5 class="fw-bold text-dark mb-1">
                                    {{ $item->tujuan_tabungan ?? 'Celengan Impian ' . $item->nama_mahasiswa }}
                                </h5>
                                <p class="text-muted small mb-3">
                                    {{ $item->catatan_impian ? Str::limit($item->catatan_impian, 60) : 'Rencana tabungan rutin per ' . $item->periode_pemasukan }}
                                </p>

                                <!-- Visual Angka Saldo vs Target -->
                                <div class="p-3 bg-light rounded-4 mb-3">
                                    <div class="d-flex justify-content-between align-items-baseline mb-1">
                                        <small class="text-muted fw-semibold">Saldo Terkumpul:</small>
                                        <span class="fs-5 fw-extrabold text-success">
                                            {{ \App\Models\TabunganMahasiswa::formatRupiah($item->saldo_setelah_satu_periode) }}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center text-muted small">
                                        <span>Target Impian:</span>
                                        <span class="fw-bold text-dark">{{ \App\Models\TabunganMahasiswa::formatRupiah($item->target_tabungan) }}</span>
                                    </div>

                                    <!-- Progress Bar Animasi -->
                                    <div class="mt-2 mb-1">
                                        <div class="d-flex justify-content-between small text-muted mb-1">
                                            <span>Progress Capaian</span>
                                            <strong class="text-success">{{ $item->persentase_progress }}%</strong>
                                        </div>
                                        <div class="progress-goal">
                                            <div class="progress-goal-bar" style="width: {{ $item->persentase_progress }}%;"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Badge & Estimasi -->
                                <div class="d-flex justify-content-between align-items-center mb-3 small">
                                    <span class="badge {{ $item->badge_class }} rounded-pill px-3 py-1">
                                        {{ $item->status_keuangan }}
                                    </span>
                                    @if($item->jumlah_periode_tambahan !== null && $item->jumlah_periode_tambahan > 0)
                                        <span class="text-muted fw-semibold">
                                            <i class="bi bi-clock-history me-1"></i>~{{ $item->jumlah_periode_tambahan }} {{ $item->periode_pemasukan }} lagi
                                        </span>
                                    @elseif($item->sisa_target <= 0)
                                        <span class="text-success fw-bold">
                                            <i class="bi bi-trophy-fill me-1 text-warning"></i>Tuntas!
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Tombol Aksi Menabung Cepat & Detail -->
                            <div class="pt-2 border-top border-light d-flex gap-2">
                                <button type="button" class="btn btn-mint btn-sm w-100 py-2" data-bs-toggle="modal" data-bs-target="#modalNabungCepat{{ $item->id }}">
                                    <i class="bi bi-cash-coin me-1"></i> Nabung Cepat
                                </button>
                                <a href="{{ route('tabungan.show', $item->id) }}" class="btn btn-outline-secondary btn-sm px-3 py-2 rounded-pill" title="Lihat Buku Tabungan">
                                    <i class="bi bi-book"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Modal Nabung Cepat untuk Celengan Ini -->
                        <div class="modal fade" id="modalNabungCepat{{ $item->id }}" tabindex="-1" aria-labelledby="modalNabungCepatLabel{{ $item->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                    <form action="{{ route('tabungan.tambahSaldo', $item->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header border-0 p-4" style="background: linear-gradient(135deg, var(--accent-mint) 0%, #059669 100%); color: #ffffff;">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="rounded-circle bg-white bg-opacity-20 p-2 fs-3 text-white">
                                                    <i class="bi bi-piggy-bank"></i>
                                                </div>
                                                <div>
                                                    <h5 class="modal-title fw-bold mb-0">Nabung ke Celengan</h5>
                                                    <small class="text-white-50">{{ $item->tujuan_tabungan ?? $item->nama_mahasiswa }}</small>
                                                </div>
                                            </div>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4 bg-light">
                                            <div class="card border-0 rounded-4 p-3 mb-3 bg-white shadow-sm">
                                                <small class="text-muted d-block">Saldo Saat Ini:</small>
                                                <h4 class="fw-bold text-dark mb-1">{{ \App\Models\TabunganMahasiswa::formatRupiah($item->saldo_setelah_satu_periode) }}</h4>
                                                <small class="text-muted">Target yang ingin dicapai: {{ \App\Models\TabunganMahasiswa::formatRupiah($item->target_tabungan) }}</small>
                                            </div>

                                            <label class="form-label fw-bold text-dark small">Pilih / Masukkan Nominal Tabungan:</label>
                                            <div class="input-group mb-3">
                                                <span class="input-group-text bg-white border-end-0 fw-bold text-success">Rp</span>
                                                <input type="number" name="nominal_tabungan" id="inputNominal{{ $item->id }}" class="form-control form-control-lg border-start-0 fw-bold" placeholder="50000" min="1000" required>
                                            </div>

                                            <!-- Pilihan Nominal Cepat -->
                                            <div class="d-flex gap-2 flex-wrap mb-2">
                                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="document.getElementById('inputNominal{{ $item->id }}').value = 20000">+Rp 20.000</button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="document.getElementById('inputNominal{{ $item->id }}').value = 50000">+Rp 50.000</button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="document.getElementById('inputNominal{{ $item->id }}').value = 100000">+Rp 100.000</button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="document.getElementById('inputNominal{{ $item->id }}').value = 500000">+Rp 500.000</button>
                                            </div>
                                            <small class="text-muted">Nominal akan langsung dimasukkan ke saldo tabungan dan target akan dihitung ulang secara otomatis.</small>
                                        </div>
                                        <div class="modal-footer border-0 p-3 bg-light d-flex justify-content-between">
                                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-mint px-4">
                                                <i class="bi bi-check2-circle me-1"></i> Masukkan ke Celengan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Hapus -->
                        <div class="modal fade" id="modalHapus{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow rounded-4 text-start">
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="modal-title fw-bold text-danger">
                                            <i class="bi bi-exclamation-triangle-fill me-2"></i>Hapus Celengan Tabungan?
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body py-3">
                                        <p class="text-secondary mb-0">
                                            Apakah Anda yakin ingin menghapus celengan <strong>"{{ $item->tujuan_tabungan ?? $item->nama_mahasiswa }}"</strong> milik <strong>{{ $item->nama_mahasiswa }}</strong>? Tindakan ini tidak dapat dibatalkan.
                                        </p>
                                    </div>
                                    <div class="modal-footer border-0 pt-0">
                                        <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                                        <form action="{{ route('tabungan.destroy', $item->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger rounded-pill px-4">
                                                <i class="bi bi-trash-fill me-1"></i>Ya, Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- MODE 2: TABEL BUKU TABUNGAN & REKAP (Ledger View) -->
    <div class="tab-pane fade" id="pills-table" role="tabpanel">
        <div class="card card-custom border-0">
            <div class="card-header card-header-custom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-journal-bookmark-fill text-primary me-2"></i>Buku Besar & Rekapitulasi Tabungan
                    </h5>
                    <small class="text-muted">Data terperinci rencana alokasi dan histori tabungan mahasiswa</small>
                </div>
            </div>
            <div class="card-body p-0">
                @if($riwayatTabungan->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        Tidak ada catatan tabungan ditemukan.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 50px;">No</th>
                                    <th>Celengan & Pemilik</th>
                                    <th>Kategori</th>
                                    <th>Pemasukan Rutin</th>
                                    <th>Pengeluaran</th>
                                    <th>Alokasi Nabung</th>
                                    <th>Saldo Terkini</th>
                                    <th>Target Impian</th>
                                    <th>Status</th>
                                    <th class="text-center" style="width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($riwayatTabungan as $index => $item)
                                    <tr>
                                        <td class="text-center text-muted fw-semibold">
                                            {{ $riwayatTabungan->firstItem() + $index }}
                                        </td>
                                        <td>
                                            <strong class="text-dark d-block">{{ $item->tujuan_tabungan ?? 'Tabungan Impian' }}</strong>
                                            <small class="text-muted">
                                                <i class="bi bi-person me-1"></i>{{ $item->nama_mahasiswa }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge {{ $item->kategori_badge }} rounded-pill">
                                                <i class="bi {{ $item->kategori_icon }} me-1"></i>{{ $item->kategori ?? 'Umum' }}
                                            </span>
                                        </td>
                                        <td class="text-success fw-semibold">
                                            {{ \App\Models\TabunganMahasiswa::formatRupiah($item->pemasukan_per_periode) }}
                                            <small class="text-muted d-block text-capitalize">/ {{ $item->periode_pemasukan }}</small>
                                        </td>
                                        <td class="text-danger fw-semibold">
                                            {{ \App\Models\TabunganMahasiswa::formatRupiah($item->total_pengeluaran) }}
                                        </td>
                                        <td>
                                            <strong class="{{ $item->tabungan_per_periode >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ \App\Models\TabunganMahasiswa::formatRupiah($item->tabungan_per_periode) }}
                                            </strong>
                                            <small class="text-muted d-block">({{ $item->persentase_tabungan }}%)</small>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark fs-6">
                                                {{ \App\Models\TabunganMahasiswa::formatRupiah($item->saldo_setelah_satu_periode) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div>{{ \App\Models\TabunganMahasiswa::formatRupiah($item->target_tabungan) }}</div>
                                            <div class="progress-goal mt-1" style="height: 6px;">
                                                <div class="progress-goal-bar" style="width: {{ $item->persentase_progress }}%;"></div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $item->badge_class }} rounded-pill px-2 py-1 small">
                                                {{ $item->status_keuangan }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('tabungan.show', $item->id) }}" class="btn btn-outline-primary" title="Lihat Buku Tabungan">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('tabungan.edit', $item->id) }}" class="btn btn-outline-warning text-dark" title="Ubah Rencana">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalHapus{{ $item->id }}" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Navigasi Pagination -->
@if($riwayatTabungan->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $riwayatTabungan->links('pagination::bootstrap-5') }}
    </div>
@endif

<!-- Edukasi / Financial Tips Mahasiswa Card -->
<div class="card card-custom mt-4 border-0" style="background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); border-left: 5px solid var(--accent-mint) !important;">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-md-2 text-center text-md-start mb-3 mb-md-0">
                <span class="fs-1 text-success">💡</span>
            </div>
            <div class="col-md-7">
                <h5 class="fw-bold text-dark mb-1">Tips Menabung Mahasiswa: Rumus 50/30/20</h5>
                <p class="text-muted small mb-0">
                    Alokasikan <strong>50%</strong> uang saku untuk kebutuhan makan & transport, <strong>30%</strong> untuk keinginan, dan minimal <strong>20%</strong> langsung disisihkan ke Celengan Tabungan di awal periode!
                </p>
            </div>
            <div class="col-md-3 text-md-end mt-3 mt-md-0">
                <a href="{{ route('tabungan.create') }}" class="btn btn-mint btn-sm px-4">
                    <i class="bi bi-plus-lg me-1"></i> Buka Celengan Baru
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
