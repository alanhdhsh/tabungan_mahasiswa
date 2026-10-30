@extends('layouts.app')

@section('title', 'Buku Tabungan Digital - ' . ($tabungan->tujuan_tabungan ?? $tabungan->nama_mahasiswa))

@section('content')
<!-- Header Navigasi -->
<div class="row align-items-center mb-4 no-print">
    <div class="col-md-7">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2 small">
                <li class="breadcrumb-item"><a href="{{ route('tabungan.index') }}" class="text-decoration-none">Dashboard Celengan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Buku Tabungan Digital</li>
            </ol>
        </nav>
        <h2 class="fw-bold text-dark mb-1 brand-font">
            <i class="bi bi-journal-check text-success me-2"></i>Buku Tabungan & Rencana Impian
        </h2>
        <p class="text-muted mb-0">Rincian pencapaian saldo celengan dan evaluasi arus kas mahasiswa.</p>
    </div>
<div class="col-md-5 text-md-end mt-3 mt-md-0 d-flex flex-wrap gap-2 justify-content-md-end">
        <a href="{{ route('tabungan.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
        <a href="{{ route('tabungan.riwayat', $tabungan->id) }}" class="btn btn-outline-primary rounded-pill px-3 py-2">
            <i class="bi bi-clock-history me-1"></i>Riwayat Transaksi
        </a>
        <button type="button" class="btn btn-mint rounded-pill px-3 py-2" data-bs-toggle="modal" data-bs-target="#modalNabungCepatDetail">
            <i class="bi bi-cash-coin me-1"></i> Setor ke Celengan
        </button>
        <button type="button" class="btn btn-outline-danger rounded-pill px-3 py-2" data-bs-toggle="modal" data-bs-target="#modalTarikDanaDetail">
            <i class="bi bi-box-arrow-up me-1"></i> Tarik Dana
        </button>
        <a href="{{ route('tabungan.edit', $tabungan->id) }}" class="btn btn-navy rounded-pill px-3 py-2">
            <i class="bi bi-pencil me-1"></i>Edit Target
        </a>
        <button type="button" class="btn btn-outline-primary rounded-pill px-3 py-2" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>Cetak Buku
        </button>
    </div>
</div>

<div class="row g-4">
    <!-- Kolom Kiri: Kartu Tabungan Digital & Milestone -->
    <div class="col-lg-8">
        <!-- Kartu Tabungan Impian Premium -->
        <div class="fintech-card mb-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="chip-icon"></span>
                    <span class="badge {{ $tabungan->kategori_badge }} rounded-pill px-3 py-1">
                        <i class="bi {{ $tabungan->kategori_icon }} me-1"></i>{{ $tabungan->kategori ?? 'Umum' }}
                    </span>
                </div>
                <div class="text-end">
                    <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-1 small">
                        Buku Tabungan #{{ str_pad($tabungan->id, 5, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
            </div>

            <small class="text-white-50 d-block text-uppercase letter-spacing-1">Pemilik Tabungan: {{ $tabungan->nama_mahasiswa }}</small>
            <h2 class="fw-bold text-white mb-2 brand-font">
                {{ $tabungan->tujuan_tabungan ?? 'Celengan Impian' }}
            </h2>
            <p class="text-white-50 small mb-4">
                {{ $tabungan->catatan_impian ?? 'Rencana tabungan mahasiswa berbasis pemasukan rutin ' . $tabungan->periode_pemasukan }}
            </p>

            <!-- Box Saldo vs Target -->
            <div class="p-3 rounded-4 mb-3" style="background: rgba(255,255,255,0.12); backdrop-filter: blur(8px);">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-2 mb-md-0">
                        <small class="text-white-50 d-block">Saldo Tabungan Saat Ini:</small>
                        <h3 class="fw-extrabold text-white mb-0">
                            {{ \App\Models\TabunganMahasiswa::formatRupiah($tabungan->saldo_setelah_satu_periode) }}
                        </h3>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <small class="text-white-50 d-block">Target yang Dikejar:</small>
                        <h4 class="fw-bold text-warning mb-0">
                            {{ \App\Models\TabunganMahasiswa::formatRupiah($tabungan->target_tabungan) }}
                        </h4>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mt-3">
                    <div class="d-flex justify-content-between text-white small mb-1">
                        <span>Pencapaian Menuju Target</span>
                        <strong class="text-success-light">{{ $tabungan->persentase_progress }}%</strong>
                    </div>
                    <div class="progress" style="height: 12px; background-color: rgba(255,255,255,0.25); border-radius: 8px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $tabungan->persentase_progress }}%;" aria-valuenow="{{ $tabungan->persentase_progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex justify-content-between text-white-50 small mt-1">
                        <span>Saldo Awal: {{ \App\Models\TabunganMahasiswa::formatRupiah($tabungan->saldo_awal) }}</span>
                        <span>Sisa Kebutuhan: {{ \App\Models\TabunganMahasiswa::formatRupiah($tabungan->sisa_target) }}</span>
                    </div>
                </div>
            </div>

            <!-- Milestone Steps -->
            <div class="d-flex justify-content-between text-center small text-white-50 mt-3 pt-2 border-top border-white border-opacity-10">
                <div>
                    <i class="bi bi-flag-fill {{ $tabungan->persentase_progress >= 0 ? 'text-success' : 'text-white-50' }} fs-5 d-block"></i>
                    <span>Mulai</span>
                </div>
                <div>
                    <i class="bi bi-circle-fill {{ $tabungan->persentase_progress >= 25 ? 'text-success' : 'text-white-50' }} fs-6 d-block"></i>
                    <span>25%</span>
                </div>
                <div>
                    <i class="bi bi-circle-fill {{ $tabungan->persentase_progress >= 50 ? 'text-success' : 'text-white-50' }} fs-6 d-block"></i>
                    <span>50%</span>
                </div>
                <div>
                    <i class="bi bi-circle-fill {{ $tabungan->persentase_progress >= 75 ? 'text-success' : 'text-white-50' }} fs-6 d-block"></i>
                    <span>75%</span>
                </div>
                <div>
                    <i class="bi bi-trophy-fill {{ $tabungan->persentase_progress >= 100 ? 'text-warning' : 'text-white-50' }} fs-5 d-block"></i>
                    <span>Tercapai!</span>
                </div>
            </div>
        </div>

        <!-- Rincian Buku Kas / Parameter Keuangan -->
        <div class="card card-custom mb-4">
            <div class="card-header card-header-custom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-clipboard2-data-fill text-primary me-2"></i>Rincian Arus Kas & Parameter Tabungan
                </h5>
                <span class="badge bg-light text-dark border">
                    Periode: {{ ucfirst($tabungan->periode_pemasukan) }}
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <tbody>
                            <tr>
                                <td class="ps-4 text-muted fw-semibold" style="width: 40%;">Pemasukan Uang Saku Rutin</td>
                                <td class="fw-bold text-success fs-6">
                                    {{ \App\Models\TabunganMahasiswa::formatRupiah($tabungan->pemasukan_per_periode) }}
                                    <small class="text-muted fw-normal">/ {{ $tabungan->periode_pemasukan }}</small>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4 text-muted fw-semibold">Pengeluaran Makan & Minum</td>
                                <td class="text-danger fw-semibold">
                                    {{ \App\Models\TabunganMahasiswa::formatRupiah($tabungan->pengeluaran_makan) }}
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4 text-muted fw-semibold">Pengeluaran Bensin / Transport</td>
                                <td class="text-danger fw-semibold">
                                    {{ \App\Models\TabunganMahasiswa::formatRupiah($tabungan->pengeluaran_transportasi) }}
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4 text-muted fw-semibold">Pengeluaran Pulsa / Kebutuhan Lain</td>
                                <td class="text-danger fw-semibold">
                                    {{ \App\Models\TabunganMahasiswa::formatRupiah($tabungan->pengeluaran_lainnya) }}
                                </td>
                            </tr>
                            <tr class="table-light">
                                <td class="ps-4 fw-bold text-dark">Total Pengeluaran Rutin</td>
                                <td class="fw-bold text-danger fs-6">
                                    {{ \App\Models\TabunganMahasiswa::formatRupiah($tabungan->total_pengeluaran) }}
                                </td>
                            </tr>
                            <tr class="table-success bg-opacity-10">
                                <td class="ps-4 fw-bold text-success">Uang yang Berhasil Ditabung / Periode</td>
                                <td class="fw-extrabold text-success fs-5">
                                    {{ \App\Models\TabunganMahasiswa::formatRupiah($tabungan->tabungan_per_periode) }}
                                    <span class="badge bg-success ms-2">{{ $tabungan->persentase_tabungan }}% dari uang saku</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Evaluasi Keuangan & Rekomendasi Waktu -->
    <div class="col-lg-4">
        <!-- Kartu Status Keuangan -->
        <div class="card card-custom mb-4">
            <div class="card-header card-header-custom">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-heart-pulse-fill text-danger me-2"></i>Status Kesehatan Finansial
                </h6>
            </div>
            <div class="card-body p-4 text-center">
                <span class="badge {{ $tabungan->badge_class }} py-2 px-3 rounded-pill fs-6 mb-3">
                    {{ $tabungan->status_keuangan }}
                </span>

                @if($tabungan->sisa_target <= 0)
                    <div class="alert alert-success border-0 rounded-4 text-start small mb-0">
                        <i class="bi bi-trophy-fill text-warning me-1"></i>
                        <strong>Selamat!</strong> Saldo celengan ini telah melampaui atau mencapai target impianmu. Kamu siap merealisasikannya!
                    </div>
                @elseif($tabungan->tabungan_per_periode > 0)
                    <div class="text-start p-3 bg-light rounded-4 mb-3">
                        <small class="text-muted d-block">Estimasi Waktu Tercapai:</small>
                        <h4 class="fw-bold text-dark mb-1">
                            ~{{ $tabungan->jumlah_periode_tambahan }} {{ ucfirst($tabungan->periode_pemasukan) }} lagi
                        </h4>
                        <small class="text-success fw-semibold">
                            <i class="bi bi-check-circle me-1"></i>Dengan konsistensi menabung {{ \App\Models\TabunganMahasiswa::formatRupiah($tabungan->tabungan_per_periode) }} per {{ $tabungan->periode_pemasukan }}.
                        </small>
                    </div>
                @else
                    <div class="alert alert-danger border-0 rounded-4 text-start small mb-0">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        Pengeluaranmu saat ini melebihi atau sama dengan uang saku. Kurangi pos pengeluaran makan di luar atau gaya hidup agar ada uang yang bisa disisihkan ke celengan.
                    </div>
                @endif
            </div>
        </div>

        <!-- Tombol Aksi Cepat Setor -->
        <div class="card card-custom mb-4 border-0 p-4 text-center" style="background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);">
            <h5 class="fw-bold text-dark mb-1">Punya Uang Tambahan?</h5>
            <p class="text-muted small mb-3">Masukkan sisa uang jajan atau honor proyek ke celengan ini untuk mempercepat target!</p>
            <button type="button" class="btn btn-mint w-100 py-2" data-bs-toggle="modal" data-bs-target="#modalNabungCepatDetail">
                <i class="bi bi-piggy-bank me-1"></i> Setor ke Celengan Sekarang
            </button>
        </div>

        <!-- Edukasi Mahasiswa -->
        <div class="card card-custom p-4">
            <h6 class="fw-bold text-dark mb-2">
                <i class="bi bi-lightbulb-fill text-warning me-1"></i> Strategi Cerdas
            </h6>
            <ul class="text-muted small ps-3 mb-0">
                <li class="mb-2">Sisihkan uang tabungan di <strong>hari pertama</strong> menerima uang saku, bukan menunggu sisa di akhir periode.</li>
                <li class="mb-2">Manfaatkan diskon mahasiswa untuk transportasi dan kebutuhan belajar.</li>
                <li>Buat celengan terpisah untuk kebutuhan mendadak agar tabungan impian tidak terpakai.</li>
            </ul>
        </div>
    </div>
</div>

<!-- Riwayat Transaksi Terakhir -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header card-header-custom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-clock-history text-primary me-2"></i>Riwayat Transaksi Terakhir
                </h5>
                <a href="{{ route('tabungan.riwayat', $tabungan->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-right me-1"></i>Lihat Semua Riwayat
                </a>
            </div>
            <div class="card-body p-0">
                @if($tabungan->riwayatTabungans->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 fw-semibold text-muted small">Tanggal</th>
                                    <th class="fw-semibold text-muted small">Jenis</th>
                                    <th class="fw-semibold text-muted small">Nominal</th>
                                    <th class="fw-semibold text-muted small">Saldo</th>
                                    <th class="fw-semibold text-muted small">Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tabungan->riwayatTabungans->take(5) as $riwayat)
                                <tr>
                                    <td class="ps-4 small">
                                        {{ $riwayat->tanggal_transaksi ? $riwayat->tanggal_transaksi->format('d M Y, H:i') : '-' }}
                                    </td>
                                    <td>
                                        <span class="badge {{ $riwayat->jenis_badge }} rounded-pill px-2 py-1 small">
                                            <i class="bi {{ $riwayat->jenis_icon }} me-1"></i>{{ $riwayat->jenis_label }}
                                        </span>
                                    </td>
                                    <td class="fw-bold {{ $riwayat->is_positif ? 'text-success' : 'text-danger' }}">
                                        {{ $riwayat->is_positif ? '+' : '-' }}{{ $riwayat->format_nominal }}
                                    </td>
                                    <td class="small text-muted">
                                        {{ $riwayat->format_saldo_sebelumnya }} → <strong class="text-dark">{{ $riwayat->format_saldo_sesudahnya }}</strong>
                                    </td>
                                    <td class="small text-muted" style="max-width: 200px;">
                                        {{ Str::limit($riwayat->catatan, 40) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($tabungan->riwayatTabungans->count() > 5)
                        <div class="text-center p-3 border-top">
                            <a href="{{ route('tabungan.riwayat', $tabungan->id) }}" class="text-decoration-none text-primary fw-semibold small">
                                <i class="bi bi-arrow-right-circle me-1"></i>Lihat {{ $tabungan->riwayatTabungans->count() - 5 }} transaksi lainnya...
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center p-4">
                        <i class="bi bi-clock-history fs-1 text-muted d-block mb-2" style="opacity: 0.3;"></i>
                        <p class="text-muted small mb-2">Belum ada riwayat transaksi untuk celengan ini.</p>
                        <button type="button" class="btn btn-mint btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalNabungCepatDetail">
                            <i class="bi bi-cash-coin me-1"></i>Setor Tabungan Pertama
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Setor Tabungan ke Celengan Ini -->
<div class="modal fade" id="modalNabungCepatDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="{{ route('tabungan.tambahSaldo', $tabungan->id) }}" method="POST">
                @csrf
                <div class="modal-header border-0 p-4" style="background: linear-gradient(135deg, var(--accent-mint) 0%, #059669 100%); color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-white bg-opacity-20 p-2 fs-3 text-white">
                            <i class="bi bi-piggy-bank"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0">Setor ke Celengan Impian</h5>
                            <small class="text-white-50">{{ $tabungan->tujuan_tabungan ?? $tabungan->nama_mahasiswa }}</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="card border-0 rounded-4 p-3 mb-3 bg-white shadow-sm">
                        <small class="text-muted d-block">Saldo Tabungan Saat Ini:</small>
                        <h4 class="fw-bold text-dark mb-1">{{ \App\Models\TabunganMahasiswa::formatRupiah($tabungan->saldo_setelah_satu_periode) }}</h4>
                        <small class="text-muted">Target yang ingin dicapai: {{ \App\Models\TabunganMahasiswa::formatRupiah($tabungan->target_tabungan) }}</small>
                    </div>

                    <label class="form-label fw-bold text-dark small">Nominal Uang yang Ingin Disetor:</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-white border-end-0 fw-bold text-success">Rp</span>
                        <input type="number" name="nominal_tabungan" id="inputNominalDetail" class="form-control form-control-lg border-start-0 fw-bold" placeholder="50000" min="1000" required>
                    </div>

                    <div class="d-flex gap-2 flex-wrap mb-3">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="document.getElementById('inputNominalDetail').value = 20000">+Rp 20.000</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="document.getElementById('inputNominalDetail').value = 50000">+Rp 50.000</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="document.getElementById('inputNominalDetail').value = 100000">+Rp 100.000</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="document.getElementById('inputNominalDetail').value = 500000">+Rp 500.000</button>
                    </div>

                    <label class="form-label fw-bold text-dark small">Catatan (opsional):</label>
                    <input type="text" name="catatan_setoran" class="form-control rounded-3" placeholder="Contoh: Sisa uang jajan minggu ini">
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

<!-- Modal Tarik Dana dari Celengan -->
<div class="modal fade" id="modalTarikDanaDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="{{ route('tabungan.tarikSaldo', $tabungan->id) }}" method="POST">
                @csrf
                <div class="modal-header border-0 p-4" style="background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); color: #ffffff;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-white bg-opacity-20 p-2 fs-3 text-white">
                            <i class="bi bi-box-arrow-up"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0">Tarik Dana dari Celengan</h5>
                            <small class="text-white-50">{{ $tabungan->tujuan_tabungan ?? $tabungan->nama_mahasiswa }}</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="card border-0 rounded-4 p-3 mb-3 bg-white shadow-sm">
                        <small class="text-muted d-block">Saldo Tersedia untuk Penarikan:</small>
                        <h4 class="fw-bold text-dark mb-1">{{ \App\Models\TabunganMahasiswa::formatRupiah($tabungan->saldo_awal) }}</h4>
                        <small class="text-danger">
                            <i class="bi bi-exclamation-triangle me-1"></i>Dana yang ditarik akan mengurangi saldo celengan Anda.
                        </small>
                    </div>

                    <label class="form-label fw-bold text-dark small">Nominal Penarikan:</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-white border-end-0 fw-bold text-danger">Rp</span>
                        <input type="number" name="nominal_penarikan" id="inputNominalTarikDetail" class="form-control form-control-lg border-start-0 fw-bold" placeholder="50000" min="1000" max="{{ $tabungan->saldo_awal }}" required>
                    </div>

                    <div class="d-flex gap-2 flex-wrap mb-3">
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" onclick="document.getElementById('inputNominalTarikDetail').value = 20000">Rp 20.000</button>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" onclick="document.getElementById('inputNominalTarikDetail').value = 50000">Rp 50.000</button>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" onclick="document.getElementById('inputNominalTarikDetail').value = 100000">Rp 100.000</button>
                    </div>

                    <label class="form-label fw-bold text-dark small">Alasan Penarikan (opsional):</label>
                    <input type="text" name="catatan_penarikan" class="form-control rounded-3" placeholder="Contoh: Bayar UKT semester ini">
                </div>
                <div class="modal-footer border-0 p-3 bg-light d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">
                        <i class="bi bi-box-arrow-up me-1"></i> Konfirmasi Penarikan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

