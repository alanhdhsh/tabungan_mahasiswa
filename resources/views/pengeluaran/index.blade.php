@extends('layouts.sidebar')

@section('title', 'Pengeluaran Tidak Terduga — Tabungin')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Pengeluaran Tidak Terduga</h1>
        <p class="page-subtitle">Kelola catatan pengeluaran mendadak Anda</p>
    </div>
    <a href="{{ url('/pengeluaran/create') }}" class="btn-green">
        <i class="bi bi-plus-lg"></i> Tambah Pengeluaran
    </a>
</div>

{{-- ========================
     FILTER
======================== --}}
<div class="card-custom mb-4">
    <div class="card-header">
        <i class="bi bi-funnel-fill me-1"></i> Filter Pengeluaran
    </div>
    <div class="card-body p-3">
        <form method="GET" action="{{ url('/pengeluaran') }}">
            <div class="row g-2">
                <div class="col-12 col-md-4">
                    <label class="form-label">Kategori</label>
                    <select name="kategori" class="form-select">
                        <option value="">Semua Kategori</option>
                        <option value="kesehatan"       {{ request('kategori') === 'kesehatan'          ? 'selected' : '' }}>Kesehatan</option>
                        <option value="pendidikan"      {{ request('kategori') === 'pendidikan'         ? 'selected' : '' }}>Pendidikan</option>
                        <option value="transportasi"    {{ request('kategori') === 'transportasi'       ? 'selected' : '' }}>Transportasi</option>
                        <option value="perbaikan_perangkat" {{ request('kategori') === 'perbaikan_perangkat' ? 'selected' : '' }}>Perbaikan Perangkat</option>
                        <option value="kebutuhan_pribadi"   {{ request('kategori') === 'kebutuhan_pribadi'   ? 'selected' : '' }}>Kebutuhan Pribadi</option>
                        <option value="lainnya"         {{ request('kategori') === 'lainnya'            ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">Sumber Dana</label>
                    <select name="sumber_dana" class="form-select">
                        <option value="">Semua Sumber</option>
                        <option value="tabungan"  {{ request('sumber_dana') === 'tabungan'  ? 'selected' : '' }}>Dari Tabungan</option>
                        <option value="luar"      {{ request('sumber_dana') === 'luar'      ? 'selected' : '' }}>Dari Luar Tabungan</option>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">Bulan & Tahun</label>
                    <input type="month" name="bulan" class="form-control"
                           value="{{ request('bulan', date('Y-m')) }}">
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn-green">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ url('/pengeluaran') }}" class="btn btn-outline-secondary btn-sm rounded-2">
                    <i class="bi bi-x-lg"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

{{-- ========================
     RINGKASAN BULAN INI
======================== --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-4">
        <div class="card-stat">
            <div class="stat-label">Total Pengeluaran</div>
            <div class="stat-value" style="color:#DC2626;">
                Rp {{ number_format($totalBulanIni ?? 0, 0, ',', '.') }}
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="card-stat">
            <div class="stat-label">Dari Tabungan</div>
            <div class="stat-value" style="color:#DC2626;">
                Rp {{ number_format($totalDariTabungan ?? 0, 0, ',', '.') }}
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="card-stat">
            <div class="stat-label">Dari Luar Tabungan</div>
            <div class="stat-value" style="color:#737373;">
                Rp {{ number_format($totalLuarTabungan ?? 0, 0, ',', '.') }}
            </div>
        </div>
    </div>
</div>

{{-- ========================
     TABEL PENGELUARAN
======================== --}}
<div class="card-custom">
    <div class="card-header">
        <i class="bi bi-table me-1"></i> Daftar Pengeluaran
    </div>
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Keterangan</th>
                    <th>Sumber Dana</th>
                    <th class="text-end">Nominal</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @isset($pengeluaran)
                    @forelse($pengeluaran as $item)
                    <tr>
                        <td style="font-size:0.85rem; color:#737373; white-space:nowrap;">
                            {{ \Carbon\Carbon::parse($item->tanggal_pengeluaran ?? $item->created_at)->format('d M Y') }}
                        </td>
                        <td>
                            @php
                                $katLabel = [
                                    'kesehatan'          => ['label'=>'Kesehatan',          'icon'=>'bi-heart-pulse'],
                                    'pendidikan'         => ['label'=>'Pendidikan',         'icon'=>'bi-book'],
                                    'transportasi'       => ['label'=>'Transportasi',       'icon'=>'bi-bus-front'],
                                    'perbaikan_perangkat'=> ['label'=>'Perbaikan Perangkat','icon'=>'bi-tools'],
                                    'kebutuhan_pribadi'  => ['label'=>'Kebutuhan Pribadi',  'icon'=>'bi-person'],
                                    'lainnya'            => ['label'=>'Lainnya',            'icon'=>'bi-three-dots'],
                                ];
                                $kat = $katLabel[$item->kategori] ?? ['label'=>ucfirst($item->kategori),'icon'=>'bi-tag'];
                            @endphp
                            <span style="font-size:0.8rem; font-weight:600;">
                                <i class="bi {{ $kat['icon'] }} me-1"></i>{{ $kat['label'] }}
                            </span>
                        </td>
                        <td style="font-size:0.875rem; color:#737373; max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                            {{ $item->keterangan ?? '-' }}
                        </td>
                        <td>
                            @if(($item->sumber_dana ?? '') === 'tabungan')
                                <span class="badge-tabungan">Dari Tabungan</span>
                            @else
                                <span class="badge-luar">Luar Tabungan</span>
                            @endif
                        </td>
                        <td class="text-end" style="font-weight:700; color:#DC2626; white-space:nowrap;">
                            -Rp {{ number_format($item->nominal ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="text-center" style="white-space:nowrap;">
                            <a href="{{ url('/pengeluaran/'.$item->id.'/edit') }}"
                               class="btn btn-sm btn-outline-secondary rounded-2 me-1"
                               style="font-size:0.78rem;">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <form method="POST" action="{{ url('/pengeluaran/'.$item->id) }}"
                                  class="d-inline"
                                  onsubmit="return confirm('Hapus pengeluaran ini? Tindakan tidak dapat dibatalkan.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-2"
                                        style="font-size:0.78rem;">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center" style="color:#737373; padding:2.5rem;">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            Belum ada catatan pengeluaran.
                        </td>
                    </tr>
                    @endforelse
                @else
                <tr>
                    <td colspan="6" class="text-center" style="color:#737373; padding:2.5rem;">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                        Belum ada catatan pengeluaran.
                    </td>
                </tr>
                @endisset
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @isset($pengeluaran)
    @if(method_exists($pengeluaran, 'links'))
    <div class="p-3 border-top" style="border-color:#E5E5E5!important;">
        {{ $pengeluaran->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
    @endif
    @endisset
</div>
@endsection
