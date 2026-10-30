@extends('layouts.app')

@section('title', 'Riwayat Transaksi - ' . ($tabungan->tujuan_tabungan ?? $tabungan->nama_mahasiswa))

@section('styles')
<style>
    /* Timeline Styles */
    .timeline-container {
        position: relative;
        padding-left: 40px;
    }

    .timeline-container::before {
        content: '';
        position: absolute;
        left: 18px;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(180deg, var(--accent-mint) 0%, #e2e8f0 30%, #e2e8f0 70%, var(--accent-mint) 100%);
        border-radius: 4px;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
        animation: fadeInUp 0.4s ease forwards;
        opacity: 0;
    }

    .timeline-item:last-child {
        padding-bottom: 0;
    }

    .timeline-item:nth-child(1) { animation-delay: 0.05s; }
    .timeline-item:nth-child(2) { animation-delay: 0.1s; }
    .timeline-item:nth-child(3) { animation-delay: 0.15s; }
    .timeline-item:nth-child(4) { animation-delay: 0.2s; }
    .timeline-item:nth-child(5) { animation-delay: 0.25s; }
    .timeline-item:nth-child(6) { animation-delay: 0.3s; }
    .timeline-item:nth-child(7) { animation-delay: 0.35s; }
    .timeline-item:nth-child(8) { animation-delay: 0.4s; }
    .timeline-item:nth-child(9) { animation-delay: 0.45s; }
    .timeline-item:nth-child(10) { animation-delay: 0.5s; }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(16px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .timeline-dot {
        position: absolute;
        left: -40px;
        top: 6px;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        z-index: 2;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border: 3px solid #ffffff;
        transition: transform 0.3s ease;
    }

    .timeline-item:hover .timeline-dot {
        transform: scale(1.15);
    }

    .timeline-dot.dot-setoran_awal {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: #ffffff;
    }

    .timeline-dot.dot-setoran_tambahan {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
    }

    .timeline-dot.dot-alokasi_rutin {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        color: #ffffff;
    }

    .timeline-dot.dot-penarikan {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: #ffffff;
    }

    .timeline-dot.dot-edit_data {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #ffffff;
    }

    .timeline-card {
        background: #ffffff;
        border: 1px solid var(--card-border);
        border-radius: 18px;
        padding: 1.25rem 1.5rem;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.03);
    }

    .timeline-card:hover {
        box-shadow: 0 10px 28px rgba(0, 0, 0, 0.08);
        transform: translateY(-3px);
        border-color: #cbd5e1;
    }

    .timeline-card.card-penarikan {
        border-left: 4px solid #ef4444;
    }

    .timeline-card.card-setoran_awal {
        border-left: 4px solid #3b82f6;
    }

    .timeline-card.card-setoran_tambahan {
        border-left: 4px solid #10b981;
    }

    .timeline-card.card-alokasi_rutin {
        border-left: 4px solid #06b6d4;
    }

    .timeline-card.card-edit_data {
        border-left: 4px solid #f59e0b;
    }

    /* Stat Summary Cards */
    .riwayat-stat {
        border-radius: 20px;
        padding: 1.25rem 1.5rem;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        position: relative;
        overflow: hidden;
    }

    .riwayat-stat:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12) !important;
    }

    .riwayat-stat::after {
        content: '';
        position: absolute;
        top: -30%;
        right: -20%;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.15);
        pointer-events: none;
    }

    .riwayat-stat .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
    }

    /* Filter Bar */
    .filter-bar {
        background: #ffffff;
        border: 1px solid var(--card-border);
        border-radius: 18px;
        padding: 1.25rem;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.03);
    }

    .filter-bar .form-control,
    .filter-bar .form-select {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background-color: #f8fafc;
        font-size: 0.875rem;
        padding: 0.55rem 0.9rem;
        transition: all 0.2s ease;
    }

    .filter-bar .form-control:focus,
    .filter-bar .form-select:focus {
        border-color: var(--accent-mint);
        box-shadow: 0 0 0 3px var(--accent-mint-glow);
        background-color: #ffffff;
    }

    /* Nominal Highlight */
    .nominal-positif {
        color: #059669;
        font-weight: 700;
    }

    .nominal-negatif {
        color: #dc2626;
        font-weight: 700;
    }

    /* Date Group Header */
    .date-group-header {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 1.2px;
        padding: 0.4rem 0;
        margin-bottom: 0.75rem;
        position: relative;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
    }

    .empty-state-icon {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: #94a3b8;
        margin: 0 auto 1.5rem auto;
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    /* Saldo Flow Arrow */
    .saldo-flow {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.82rem;
        color: var(--text-muted);
    }

    .saldo-flow .arrow-icon {
        color: var(--accent-mint);
        font-size: 1.1rem;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .timeline-container {
            padding-left: 32px;
        }
        .timeline-container::before {
            left: 14px;
        }
        .timeline-dot {
            left: -32px;
            width: 30px;
            height: 30px;
            font-size: 0.82rem;
        }
        .timeline-card {
            padding: 1rem 1.15rem;
        }
    }
</style>
@endsection

@section('content')
<!-- Header Navigasi -->
<div class="row align-items-center mb-4 no-print">
    <div class="col-md-7">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2 small">
                <li class="breadcrumb-item"><a href="{{ route('tabungan.index') }}" class="text-decoration-none">Dashboard Celengan</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tabungan.show', $tabungan->id) }}" class="text-decoration-none">{{ $tabungan->tujuan_tabungan ?? $tabungan->nama_mahasiswa }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Riwayat Transaksi</li>
            </ol>
        </nav>
        <h2 class="fw-bold text-dark mb-1 brand-font">
            <i class="bi bi-clock-history text-primary me-2"></i>Riwayat Transaksi Lengkap
        </h2>
        <p class="text-muted mb-0">
            Seluruh catatan mutasi & pergerakan saldo celengan <strong>"{{ $tabungan->tujuan_tabungan ?? $tabungan->nama_mahasiswa }}"</strong>
        </p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0 d-flex flex-wrap gap-2 justify-content-md-end">
        <a href="{{ route('tabungan.show', $tabungan->id) }}" class="btn btn-outline-secondary rounded-pill px-3 py-2">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Detail
        </a>
        <button type="button" class="btn btn-mint rounded-pill px-3 py-2" data-bs-toggle="modal" data-bs-target="#modalSetorRiwayat">
            <i class="bi bi-cash-coin me-1"></i> Setor
        </button>
        <button type="button" class="btn btn-outline-danger rounded-pill px-3 py-2" data-bs-toggle="modal" data-bs-target="#modalTarikRiwayat">
            <i class="bi bi-box-arrow-up me-1"></i> Tarik Dana
        </button>
    </div>
</div>

<!-- Statistik Ringkasan Riwayat -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="riwayat-stat text-white shadow" style="background: linear-gradient(135deg, #0b192c 0%, #1e3e62 100%);">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>
                <div>
                    <div class="small text-white-50">Total Transaksi</div>
                    <h4 class="fw-bold mb-0 brand-font">{{ $jumlahTransaksi }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="riwayat-stat text-white shadow" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon">
                    <i class="bi bi-arrow-down-circle"></i>
                </div>
                <div>
                    <div class="small text-white-50">Total Masuk</div>
                    <h5 class="fw-bold mb-0 brand-font">{{ \App\Models\TabunganMahasiswa::formatRupiah($totalSetoran) }}</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="riwayat-stat text-white shadow" style="background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon">
                    <i class="bi bi-arrow-up-circle"></i>
                </div>
                <div>
                    <div class="small text-white-50">Total Keluar</div>
                    <h5 class="fw-bold mb-0 brand-font">{{ \App\Models\TabunganMahasiswa::formatRupiah($totalPenarikan) }}</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="riwayat-stat text-white shadow" style="background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%);">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div>
                    <div class="small text-white-50">Saldo Sekarang</div>
                    <h5 class="fw-bold mb-0 brand-font">{{ \App\Models\TabunganMahasiswa::formatRupiah($tabungan->saldo_setelah_satu_periode) }}</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter & Pencarian -->
<div class="filter-bar mb-4 no-print">
    <form method="GET" action="{{ route('tabungan.riwayat', $tabungan->id) }}">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">
                    <i class="bi bi-funnel me-1"></i>Jenis Transaksi
                </label>
                <select name="jenis" class="form-select">
                    <option value="semua">Semua Jenis</option>
                    <option value="setoran_awal" {{ request('jenis') == 'setoran_awal' ? 'selected' : '' }}>Setoran Awal</option>
                    <option value="setoran_tambahan" {{ request('jenis') == 'setoran_tambahan' ? 'selected' : '' }}>Setoran Tambahan</option>
                    <option value="alokasi_rutin" {{ request('jenis') == 'alokasi_rutin' ? 'selected' : '' }}>Alokasi Rutin</option>
                    <option value="penarikan" {{ request('jenis') == 'penarikan' ? 'selected' : '' }}>Penarikan Dana</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">
                    <i class="bi bi-calendar3 me-1"></i>Dari Tanggal
                </label>
                <input type="date" name="dari_tanggal" class="form-control" value="{{ request('dari_tanggal') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">
                    <i class="bi bi-calendar3 me-1"></i>Sampai Tanggal
                </label>
                <input type="date" name="sampai_tanggal" class="form-control" value="{{ request('sampai_tanggal') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">
                    <i class="bi bi-search me-1"></i>Cari Catatan
                </label>
                <input type="text" name="cari" class="form-control" placeholder="Cari catatan transaksi..." value="{{ request('cari') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-navy w-100 py-2">
                    <i class="bi bi-filter me-1"></i>Filter
                </button>
                <a href="{{ route('tabungan.riwayat', $tabungan->id) }}" class="btn btn-outline-secondary py-2" title="Reset Filter">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<div class="row g-4">
    <!-- Timeline Riwayat Transaksi -->
    <div class="col-lg-8">
        <div class="card card-custom">
            <div class="card-header card-header-custom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-list-columns-reverse text-primary me-2"></i>Daftar Mutasi Transaksi
                </h5>
                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                    {{ $riwayatTransaksi->total() }} transaksi ditemukan
                </span>
            </div>
            <div class="card-body p-4">
                @if($riwayatTransaksi->count() > 0)
                    <div class="timeline-container">
                        @php $lastDate = null; @endphp
                        @foreach($riwayatTransaksi as $index => $riwayat)
                            @php
                                $currentDate = $riwayat->tanggal_transaksi
                                    ? $riwayat->tanggal_transaksi->format('d M Y')
                                    : ($riwayat->created_at ? $riwayat->created_at->format('d M Y') : '-');
                            @endphp

                            {{-- Date Group Header --}}
                            @if($currentDate !== $lastDate)
                                <div class="date-group-header {{ $index > 0 ? 'mt-3' : '' }}">
                                    <i class="bi bi-calendar2-event me-1"></i>
                                    {{ $currentDate }}
                                </div>
                                @php $lastDate = $currentDate; @endphp
                            @endif

                            <div class="timeline-item">
                                <div class="timeline-dot dot-{{ $riwayat->jenis }}">
                                    <i class="bi {{ $riwayat->jenis_icon }}"></i>
                                </div>

                                <div class="timeline-card card-{{ $riwayat->jenis }}">
                                    <div class="d-flex flex-wrap justify-content-between align-items-start mb-2">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge {{ $riwayat->jenis_badge }} rounded-pill px-3 py-1 small">
                                                {{ $riwayat->jenis_label }}
                                            </span>
                                            <span class="text-muted small">
                                                <i class="bi bi-clock me-1"></i>
                                                {{ $riwayat->tanggal_transaksi ? $riwayat->tanggal_transaksi->format('H:i') : ($riwayat->created_at ? $riwayat->created_at->format('H:i') : '-') }}
                                            </span>
                                        </div>
                                        <div>
                                            @if($riwayat->is_positif)
                                                <span class="nominal-positif fs-5">
                                                    +{{ $riwayat->format_nominal }}
                                                </span>
                                            @else
                                                <span class="nominal-negatif fs-5">
                                                    -{{ $riwayat->format_nominal }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    @if($riwayat->catatan)
                                        <p class="text-muted small mb-2">
                                            <i class="bi bi-chat-left-text me-1"></i>{{ $riwayat->catatan }}
                                        </p>
                                    @endif

                                    <div class="saldo-flow">
                                        <span class="fw-semibold">{{ $riwayat->format_saldo_sebelumnya }}</span>
                                        <i class="bi bi-arrow-right-short arrow-icon"></i>
                                        <span class="fw-bold text-dark">{{ $riwayat->format_saldo_sesudahnya }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $riwayatTransaksi->withQueryString()->links('pagination::bootstrap-5') }}
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Belum Ada Riwayat Transaksi</h5>
                        <p class="text-muted mb-3">
                            @if(request()->hasAny(['jenis', 'dari_tanggal', 'sampai_tanggal', 'cari']))
                                Tidak ditemukan transaksi yang sesuai dengan filter pencarian. Coba ubah kriteria filter.
                            @else
                                Celengan ini belum memiliki catatan mutasi. Mulai setor tabungan untuk melihat riwayat di sini!
                            @endif
                        </p>
                        @if(request()->hasAny(['jenis', 'dari_tanggal', 'sampai_tanggal', 'cari']))
                            <a href="{{ route('tabungan.riwayat', $tabungan->id) }}" class="btn btn-outline-primary rounded-pill px-4">
                                <i class="bi bi-x-circle me-1"></i>Hapus Filter
                            </a>
                        @else
                            <button type="button" class="btn btn-mint rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalSetorRiwayat">
                                <i class="bi bi-cash-coin me-1"></i>Setor Tabungan Pertama
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar: Ringkasan Celengan & Aksi Cepat -->
    <div class="col-lg-4">
        <!-- Kartu Ringkasan Celengan Mini -->
        <div class="fintech-card mb-4" style="padding: 1.4rem;">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="chip-icon" style="width: 34px; height: 24px;"></span>
                <span class="badge {{ $tabungan->kategori_badge }} rounded-pill px-2 py-1 small">
                    <i class="bi {{ $tabungan->kategori_icon }} me-1"></i>{{ $tabungan->kategori ?? 'Umum' }}
                </span>
            </div>
            <small class="text-white-50 d-block mb-0">{{ $tabungan->nama_mahasiswa }}</small>
            <h5 class="fw-bold text-white mb-2">{{ $tabungan->tujuan_tabungan ?? 'Celengan Impian' }}</h5>
            <div class="d-flex justify-content-between text-white small mb-2">
                <span class="text-white-50">Saldo Saat Ini</span>
                <strong>{{ \App\Models\TabunganMahasiswa::formatRupiah($tabungan->saldo_setelah_satu_periode) }}</strong>
            </div>
            <div class="progress" style="height: 8px; background-color: rgba(255,255,255,0.2); border-radius: 8px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $tabungan->persentase_progress }}%;" aria-valuenow="{{ $tabungan->persentase_progress }}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="d-flex justify-content-between text-white-50 small mt-1">
                <span>{{ $tabungan->persentase_progress }}% tercapai</span>
                <span>Target: {{ \App\Models\TabunganMahasiswa::formatRupiah($tabungan->target_tabungan) }}</span>
            </div>
        </div>

        <!-- Transaksi Terakhir -->
        @if($transaksiTerakhir)
        <div class="card card-custom mb-4">
            <div class="card-header card-header-custom">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-lightning-charge-fill text-warning me-2"></i>Transaksi Terakhir
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="timeline-dot dot-{{ $transaksiTerakhir->jenis }}" style="position: relative; left: 0; top: 0;">
                        <i class="bi {{ $transaksiTerakhir->jenis_icon }}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <span class="badge {{ $transaksiTerakhir->jenis_badge }} rounded-pill small">{{ $transaksiTerakhir->jenis_label }}</span>
                        <div class="fw-bold mt-1 {{ $transaksiTerakhir->is_positif ? 'text-success' : 'text-danger' }}">
                            {{ $transaksiTerakhir->is_positif ? '+' : '-' }}{{ $transaksiTerakhir->format_nominal }}
                        </div>
                    </div>
                </div>
                @if($transaksiTerakhir->catatan)
                    <p class="text-muted small mb-1">
                        <i class="bi bi-chat-left-text me-1"></i>{{ $transaksiTerakhir->catatan }}
                    </p>
                @endif
                <small class="text-muted">
                    <i class="bi bi-calendar3 me-1"></i>
                    {{ $transaksiTerakhir->tanggal_transaksi ? $transaksiTerakhir->tanggal_transaksi->format('d M Y, H:i') : '-' }}
                </small>
            </div>
        </div>
        @endif

        <!-- Aksi Cepat -->
        <div class="card card-custom mb-4 border-0 p-4" style="background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);">
            <h6 class="fw-bold text-dark mb-3">
                <i class="bi bi-lightning-fill text-warning me-1"></i>Aksi Cepat
            </h6>
            <div class="d-grid gap-2">
                <button type="button" class="btn btn-mint py-2" data-bs-toggle="modal" data-bs-target="#modalSetorRiwayat">
                    <i class="bi bi-piggy-bank me-1"></i> Setor ke Celengan
                </button>
                <button type="button" class="btn btn-outline-danger py-2 rounded-pill" data-bs-toggle="modal" data-bs-target="#modalTarikRiwayat">
                    <i class="bi bi-box-arrow-up me-1"></i> Tarik Dana
                </button>
                <a href="{{ route('tabungan.show', $tabungan->id) }}" class="btn btn-navy py-2">
                    <i class="bi bi-journal-check me-1"></i> Lihat Buku Tabungan
                </a>
            </div>
        </div>

        <!-- Legenda Jenis Transaksi -->
        <div class="card card-custom p-4">
            <h6 class="fw-bold text-dark mb-3">
                <i class="bi bi-info-circle-fill text-primary me-1"></i>Legenda Transaksi
            </h6>
            <div class="d-flex flex-column gap-2">
                <div class="d-flex align-items-center gap-2 small">
                    <div class="timeline-dot dot-setoran_awal" style="position: relative; left: 0; top: 0; width: 28px; height: 28px; font-size: 0.75rem; border-width: 2px;">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <span class="text-muted">Setoran Awal — pembukaan celengan</span>
                </div>
                <div class="d-flex align-items-center gap-2 small">
                    <div class="timeline-dot dot-setoran_tambahan" style="position: relative; left: 0; top: 0; width: 28px; height: 28px; font-size: 0.75rem; border-width: 2px;">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <span class="text-muted">Setoran Tambahan — menabung manual</span>
                </div>
                <div class="d-flex align-items-center gap-2 small">
                    <div class="timeline-dot dot-alokasi_rutin" style="position: relative; left: 0; top: 0; width: 28px; height: 28px; font-size: 0.75rem; border-width: 2px;">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                    <span class="text-muted">Alokasi Rutin — otomatis per periode</span>
                </div>
                <div class="d-flex align-items-center gap-2 small">
                    <div class="timeline-dot dot-penarikan" style="position: relative; left: 0; top: 0; width: 28px; height: 28px; font-size: 0.75rem; border-width: 2px;">
                        <i class="bi bi-box-arrow-up"></i>
                    </div>
                    <span class="text-muted">Penarikan — mengambil dana</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Setor Tabungan -->
<div class="modal fade" id="modalSetorRiwayat" tabindex="-1" aria-hidden="true">
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
                        <small class="text-muted">Target: {{ \App\Models\TabunganMahasiswa::formatRupiah($tabungan->target_tabungan) }}</small>
                    </div>

                    <label class="form-label fw-bold text-dark small">Nominal Setoran:</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-white border-end-0 fw-bold text-success">Rp</span>
                        <input type="number" name="nominal_tabungan" id="inputNominalSetorRiwayat" class="form-control form-control-lg border-start-0 fw-bold" placeholder="50000" min="1000" required>
                    </div>
                    <div class="d-flex gap-2 flex-wrap mb-3">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="document.getElementById('inputNominalSetorRiwayat').value = 20000">+Rp 20.000</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="document.getElementById('inputNominalSetorRiwayat').value = 50000">+Rp 50.000</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="document.getElementById('inputNominalSetorRiwayat').value = 100000">+Rp 100.000</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="document.getElementById('inputNominalSetorRiwayat').value = 500000">+Rp 500.000</button>
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

<!-- Modal Tarik Dana -->
<div class="modal fade" id="modalTarikRiwayat" tabindex="-1" aria-hidden="true">
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
                        <input type="number" name="nominal_penarikan" id="inputNominalTarikRiwayat" class="form-control form-control-lg border-start-0 fw-bold" placeholder="50000" min="1000" max="{{ $tabungan->saldo_awal }}" required>
                    </div>
                    <div class="d-flex gap-2 flex-wrap mb-3">
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" onclick="document.getElementById('inputNominalTarikRiwayat').value = 20000">Rp 20.000</button>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" onclick="document.getElementById('inputNominalTarikRiwayat').value = 50000">Rp 50.000</button>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" onclick="document.getElementById('inputNominalTarikRiwayat').value = 100000">Rp 100.000</button>
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
