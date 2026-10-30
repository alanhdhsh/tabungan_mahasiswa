@extends('layouts.sidebar')

@section('title', 'Dashboard — Tabungin')

@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            Selamat datang, {{ Auth::user()->name ?? 'Pengguna' }}! 👋
        </h1>
        <p class="page-subtitle">
            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
        </p>
    </div>
</div>

{{-- ========================
     STAT CARDS
======================== --}}
<div class="row g-3 mb-4">
    {{-- Saldo Total --}}
    <div class="col-6 col-lg-3">
        <div class="card-stat">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="stat-label">Saldo Total Tabungan</span>
                <div class="stat-icon" style="background:#DCFCE7; color:#16A34A;">
                    <i class="bi bi-wallet2"></i>
                </div>
            </div>
            <div class="stat-value" style="color:#16A34A;">
                Rp {{ number_format($saldoTotal ?? 0, 0, ',', '.') }}
            </div>
            <small style="color:#737373; font-size:0.75rem;">Seluruh celengan aktif</small>
        </div>
    </div>

    {{-- Total Setoran --}}
    <div class="col-6 col-lg-3">
        <div class="card-stat">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="stat-label">Setoran Bulan Ini</span>
                <div class="stat-icon" style="background:#DCFCE7; color:#16A34A;">
                    <i class="bi bi-arrow-down-circle-fill"></i>
                </div>
            </div>
            <div class="stat-value" style="color:#16A34A;">
                Rp {{ number_format($totalSetoran ?? 0, 0, ',', '.') }}
            </div>
            <small style="color:#737373; font-size:0.75rem;">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</small>
        </div>
    </div>

    {{-- Total Penarikan --}}
    <div class="col-6 col-lg-3">
        <div class="card-stat">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="stat-label">Penarikan Bulan Ini</span>
                <div class="stat-icon" style="background:#FEE2E2; color:#DC2626;">
                    <i class="bi bi-arrow-up-circle-fill"></i>
                </div>
            </div>
            <div class="stat-value" style="color:#DC2626;">
                Rp {{ number_format($totalPenarikan ?? 0, 0, ',', '.') }}
            </div>
            <small style="color:#737373; font-size:0.75rem;">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</small>
        </div>
    </div>

    {{-- Total Pengeluaran --}}
    <div class="col-6 col-lg-3">
        <div class="card-stat">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="stat-label">Pengeluaran Bulan Ini</span>
                <div class="stat-icon" style="background:#FEE2E2; color:#DC2626;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
            </div>
            <div class="stat-value" style="color:#DC2626;">
                Rp {{ number_format($totalPengeluaran ?? 0, 0, ',', '.') }}
            </div>
            <small style="color:#737373; font-size:0.75rem;">Tak terduga</small>
        </div>
    </div>
</div>

{{-- ========================
     BUDGET ALERT + PROGRESS
======================== --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-lg-6">
        {{-- Peringatan Anggaran --}}
        @if(isset($statusAnggaran))
            @if($statusAnggaran === 'terlampaui')
                <div class="d-flex align-items-start gap-2 p-3 rounded-3 mb-3"
                     style="background:#FEE2E2; border:1px solid #FECACA; color:#991B1B;">
                    <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                    <div>
                        <strong>⚠️ Anggaran bulan ini telah terlampaui!</strong>
                        <div style="font-size:0.85rem; margin-top:0.2rem;">
                            Pengeluaran sudah melebihi batas anggaran yang ditetapkan. Tinjau kembali pengeluaran Anda.
                        </div>
                    </div>
                </div>
            @elseif($statusAnggaran === 'peringatan')
                <div class="d-flex align-items-start gap-2 p-3 rounded-3 mb-3"
                     style="background:#FFFBEB; border:1px solid #FDE68A; color:#92400E;">
                    <i class="bi bi-exclamation-circle-fill mt-1"></i>
                    <div>
                        <strong>⚠️ Peringatan Anggaran</strong>
                        <div style="font-size:0.85rem; margin-top:0.2rem;">
                            Pengeluaran sudah mencapai <strong>{{ $persenAnggaran ?? 0 }}%</strong> dari anggaran bulan ini.
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="d-flex align-items-start gap-2 p-3 rounded-3 mb-3"
                 style="background:#EFF6FF; border:1px solid #BFDBFE; color:#1E40AF;">
                <i class="bi bi-info-circle-fill mt-1"></i>
                <div>
                    Belum ada anggaran bulan ini.
                    <a href="{{ url('/anggaran') }}" style="color:#1E40AF; font-weight:600;">Atur sekarang →</a>
                </div>
            </div>
        @endif

        {{-- Sisa Anggaran Progress --}}
        @isset($sisaAnggaran)
        <div class="card-stat">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="stat-label">Sisa Anggaran Bulan Ini</span>
                <span style="font-size:0.85rem; font-weight:700;
                    color: {{ ($persenAnggaran ?? 0) >= 100 ? '#DC2626' : (($persenAnggaran ?? 0) >= 80 ? '#D97706' : '#16A34A') }};">
                    {{ $persenAnggaran ?? 0 }}% terpakai
                </span>
            </div>
            <div class="stat-value mb-2"
                 style="color: {{ ($sisaAnggaran ?? 0) < 0 ? '#DC2626' : '#16A34A' }}">
                Rp {{ number_format($sisaAnggaran ?? 0, 0, ',', '.') }}
            </div>
            <div class="progress-slim">
                <div class="progress-bar"
                     style="width: {{ min($persenAnggaran ?? 0, 100) }}%;
                            background: {{ ($persenAnggaran ?? 0) >= 100 ? '#DC2626' : (($persenAnggaran ?? 0) >= 80 ? '#D97706' : '#16A34A') }};">
                </div>
            </div>
        </div>
        @endisset
    </div>

    {{-- Chart: Pemasukan vs Pengeluaran --}}
    <div class="col-12 col-lg-6">
        <div class="card-custom h-100">
            <div class="card-header">
                <i class="bi bi-bar-chart-fill text-success me-1"></i>
                Pemasukan vs Pengeluaran — 6 Bulan Terakhir
            </div>
            <div class="card-body p-3">
                <canvas id="chartKeuangan" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- ========================
     TARGET TABUNGAN AKTIF
======================== --}}
@isset($targetAktif)
@if(count($targetAktif) > 0)
<div class="mb-4">
    <h6 class="fw-700 mb-3" style="font-weight:700; color:#111111;">
        <i class="bi bi-bullseye text-success me-1"></i> Target Tabungan Aktif
    </h6>
    <div class="row g-3">
        @foreach($targetAktif as $target)
        @php
            $persen = $target->nominal_target > 0
                ? min(round(($target->dana_terkumpul / $target->nominal_target) * 100), 100)
                : 0;
            $sisa = $target->nominal_target - $target->dana_terkumpul;
        @endphp
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card-stat">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div style="font-weight:700; font-size:0.95rem; color:#111111;">
                            {{ $target->nama_target }}
                        </div>
                        @if($target->tanggal_target)
                        <small style="color:#737373; font-size:0.75rem;">
                            Target: {{ \Carbon\Carbon::parse($target->tanggal_target)->translatedFormat('d M Y') }}
                        </small>
                        @endif
                    </div>
                    <span style="background:#DCFCE7; color:#16A34A; font-size:0.72rem; font-weight:700; padding:0.2rem 0.55rem; border-radius:20px;">
                        {{ $persen }}%
                    </span>
                </div>
                <div class="progress-slim mb-2">
                    <div class="progress-bar"
                         style="width:{{ $persen }}%; background:#16A34A;"></div>
                </div>
                <div class="d-flex justify-content-between" style="font-size:0.8rem; color:#737373;">
                    <span>Terkumpul: <strong style="color:#111;">Rp {{ number_format($target->dana_terkumpul, 0, ',', '.') }}</strong></span>
                    <span>Kurang: <strong style="color:#DC2626;">Rp {{ number_format(max($sisa,0), 0, ',', '.') }}</strong></span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
@endisset

{{-- ========================
     TRANSAKSI TERBARU
======================== --}}
<div class="card-custom">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-clock-history text-success me-1"></i> Transaksi Terbaru</span>
        <a href="{{ url('/riwayat-transaksi') }}" style="font-size:0.8rem; color:#16A34A; text-decoration:none; font-weight:600;">
            Lihat Semua →
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Keterangan</th>
                    <th class="text-end">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @isset($transaksiTerbaru)
                    @forelse($transaksiTerbaru as $trx)
                    <tr>
                        <td>
                            <span style="font-size:0.8rem; color:#737373;">
                                {{ \Carbon\Carbon::parse($trx->created_at ?? $trx->tanggal ?? now())->format('d M Y') }}
                            </span>
                        </td>
                        <td>
                            @php
                                $jenis = $trx->jenis ?? $trx->tipe ?? '-';
                                $isPositif = in_array(strtolower($jenis), ['setoran', 'pemasukan']);
                            @endphp
                            <span style="
                                font-size:0.78rem; font-weight:600; padding:0.2rem 0.6rem; border-radius:20px;
                                background: {{ $isPositif ? '#DCFCE7' : '#FEE2E2' }};
                                color: {{ $isPositif ? '#16A34A' : '#DC2626' }};">
                                {{ ucfirst($jenis) }}
                            </span>
                        </td>
                        <td style="font-size:0.875rem; color:#737373;">
                            {{ $trx->keterangan ?? '-' }}
                        </td>
                        <td class="text-end" style="font-weight:700;
                            color: {{ $isPositif ? '#16A34A' : '#DC2626' }};">
                            {{ $isPositif ? '+' : '-' }}Rp {{ number_format($trx->nominal ?? $trx->jumlah ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center" style="color:#737373; padding:2rem;">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            Belum ada transaksi.
                        </td>
                    </tr>
                    @endforelse
                @else
                <tr>
                    <td colspan="4" class="text-center" style="color:#737373; padding:2rem;">
                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                        Belum ada transaksi.
                    </td>
                </tr>
                @endisset
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Chart Pemasukan vs Pengeluaran
    const ctx = document.getElementById('chartKeuangan');
    if (ctx) {
        @php
            $grafik = $dataGrafik ?? [
                'labels' => ['Jan','Feb','Mar','Apr','Mei','Jun'],
                'setoran' => [0,0,0,0,0,0],
                'pengeluaran' => [0,0,0,0,0,0],
            ];
        @endphp
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($grafik['labels']),
                datasets: [
                    {
                        label: 'Setoran / Pemasukan',
                        data: @json($grafik['setoran']),
                        backgroundColor: 'rgba(22, 163, 74, 0.75)',
                        borderRadius: 6,
                    },
                    {
                        label: 'Pengeluaran',
                        data: @json($grafik['pengeluaran']),
                        backgroundColor: 'rgba(220, 38, 38, 0.75)',
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: { font: { family: 'Plus Jakarta Sans', size: 12 } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return ' Rp ' + ctx.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Plus Jakarta Sans', size: 11 } }
                    },
                    y: {
                        grid: { color: '#F0F0F0' },
                        ticks: {
                            font: { family: 'Plus Jakarta Sans', size: 11 },
                            callback: function(v) {
                                if (v >= 1000000) return 'Rp ' + (v/1000000).toFixed(1) + 'jt';
                                if (v >= 1000) return 'Rp ' + (v/1000).toFixed(0) + 'rb';
                                return 'Rp ' + v;
                            }
                        }
                    }
                }
            }
        });
    }
</script>
@endsection
