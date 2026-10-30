@extends('layouts.app')

@section('title', 'Buka Celengan & Rencana Tabungan Baru - TabunganKu')

@section('content')
<!-- Header Navigasi -->
<div class="row mb-4 align-items-center">
    <div class="col-md-7">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2 small">
                <li class="breadcrumb-item"><a href="{{ route('tabungan.index') }}" class="text-decoration-none">TabunganKu</a></li>
                <li class="breadcrumb-item active" aria-current="page">Buka Celengan Impian</li>
            </ol>
        </nav>
        <h2 class="fw-bold text-dark mb-1 brand-font">
            <i class="bi bi-piggy-bank-fill text-success me-2"></i>Buka Celengan Impian Baru
        </h2>
        <p class="text-muted mb-0"><span class="badge bg-light text-secondary border me-1">Form Perhitungan Tabungan Mahasiswa</span> Tentukan target impianmu, alokasikan uang saku, dan raih impian perkuliahan secara terencana.</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
        <a href="{{ route('tabungan.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Dashboard
        </a>
    </div>
</div>

<form action="{{ route('tabungan.store') }}" method="POST" id="formTabungan">
    @csrf

    <div class="row g-4">
        <!-- Kolom Form Input (Kiri) -->
        <div class="col-lg-7">
            <!-- Pilihan Cepat / Preset Impian Populer Mahasiswa -->
            <div class="card card-custom mb-4 border-0">
                <div class="card-body p-4">
                    <small class="text-muted fw-bold d-block text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                        ⚡ Pilih Template Impian Mahasiswa (Klik untuk Isi Cepat):
                    </small>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" onclick="applyPreset('Beli Laptop Kuliah', 'Gadget & Kuliah', 7000000, 'Untuk kebutuhan kuliah cloud computing dan tugas akhir.')">
                            💻 Beli Laptop Kuliah
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1" onclick="applyPreset('Biaya UKT Semester Genap', 'Pendidikan', 4500000, 'Persiapan pembayaran kuliah tepat waktu.')">
                            🎓 Bayar UKT Kuliah
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1" onclick="applyPreset('Dana Darurat Kos', 'Dana Darurat', 2000000, 'Simpanan jaga-jaga kebutuhan mendadak.')">
                            🛡️ Dana Darurat Kos
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3 py-1 text-dark" onclick="applyPreset('Servis & Ban Motor', 'Kendaraan', 1500000, 'Perawatan motor harian kampus.')">
                            🛵 Servis Kendaraan
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3 py-1 text-dark" onclick="applyPreset('Liburan & Healing Semester', 'Liburan & Hiburan', 1200000, 'Refreshing setelah ujian selesai.')">
                            🏖️ Liburan & Healing
                        </button>
                    </div>
                </div>
            </div>

            <!-- Kartu 1: Target Celengan & Pemilik -->
            <div class="card card-custom mb-4">
                <div class="card-header card-header-custom">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-bullseye text-primary me-2"></i>1. Rencana & Target Impian
                    </h5>
                    <small class="text-muted">Tentukan tujuan dan nominal uang yang ingin dikumpulkan.</small>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <!-- Tujuan Tabungan -->
                        <div class="col-12">
                            <label for="tujuan_tabungan" class="form-label fw-semibold text-dark">
                                Nama Impian / Celengan <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0">
                                    <i class="bi bi-star"></i>
                                </span>
                                <input type="text" class="form-control border-start-0 @error('tujuan_tabungan') is-invalid @enderror" id="tujuan_tabungan" name="tujuan_tabungan" placeholder="Contoh: Beli Laptop Baru" value="{{ old('tujuan_tabungan', 'Tabungan Masa Depan') }}" required>
                            </div>
                            @error('tujuan_tabungan')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Kategori Tabungan -->
                        <div class="col-md-6">
                            <label for="kategori" class="form-label fw-semibold text-dark">
                                Kategori Impian
                            </label>
                            <select class="form-select bg-light" id="kategori" name="kategori">
                                <option value="Gadget & Kuliah">💻 Gadget & Kuliah</option>
                                <option value="Pendidikan">🎓 Pendidikan / UKT</option>
                                <option value="Dana Darurat">🛡️ Dana Darurat</option>
                                <option value="Kendaraan">🛵 Kendaraan & Transportasi</option>
                                <option value="Liburan & Hiburan">🏖️ Liburan & Hiburan</option>
                                <option value="Umum" selected>🐷 Tabungan Umum</option>
                            </select>
                        </div>

                        <!-- Target Tabungan (Rp) -->
                        <div class="col-md-6">
                            <label for="target_tabungan" class="form-label fw-semibold text-dark">
                                Target Nominal yang Dituju <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-success fw-bold border-end-0">Rp</span>
                                <input type="number" class="form-control border-start-0 fw-bold @error('target_tabungan') is-invalid @enderror" id="target_tabungan" name="target_tabungan" placeholder="0" value="{{ old('target_tabungan', ) }}" min="0" step="10000" required>
                            </div>
                        </div>

                        <!-- Saldo Awal Saat Ini -->
                        <div class="col-md-6">
                            <label for="saldo_awal" class="form-label fw-semibold text-dark">
                                Saldo Awal yang Sudah Ada <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0">Rp</span>
                                <input type="number" class="form-control border-start-0 @error('saldo_awal') is-invalid @enderror" id="saldo_awal" name="saldo_awal" placeholder="0" value="{{ old('saldo_awal', ) }}" min="0" step="10000" required>
                            </div>
                            <small class="text-muted">Isi 0 jika baru mulai menabung dari nol.</small>
                        </div>

                        <!-- Tanggal Mulai -->
                        <div class="col-md-6">
                            <label for="tanggal_pencatatan" class="form-label fw-semibold text-dark">Tanggal Mulai Celengan</label>
                            <input type="date" class="form-control bg-light" id="tanggal_pencatatan" name="tanggal_pencatatan" value="{{ old('tanggal_pencatatan', date('Y-m-d')) }}">
                        </div>

                        <!-- Catatan / Motivasi -->
                        <div class="col-12">
                            <label for="catatan_impian" class="form-label fw-semibold text-dark">Catatan Motivasi Menabung</label>
                            <textarea class="form-control bg-light" id="catatan_impian" name="catatan_impian" rows="2" placeholder="Tuliskan alasan atau target tanggal pencapaian impianmu...">{{ old('catatan_impian') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kartu 2: Alokasi Pemasukan & Pengeluaran Mahasiswa -->
            <div class="card card-custom mb-4">
                <div class="card-header card-header-custom">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-wallet2 text-primary me-2"></i>2. Arus Kas & Pemasukan Rutin
                    </h5>
                    <small class="text-muted">Berapa uang saku yang diterima dan berapa kebutuhan rutin yang harus dikeluarkan.</small>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <!-- Pemasukan Rutin -->
                        <div class="col-md-6">
                            <label for="pemasukan_per_periode" class="form-label fw-semibold text-dark">
                                Pemasukan Uang Saku Rutin <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-success fw-bold border-end-0">Rp</span>
                                <input type="number" class="form-control border-start-0 fw-bold @error('pemasukan_per_periode') is-invalid @enderror" id="pemasukan_per_periode" name="pemasukan_per_periode" placeholder="0" value="{{ old('pemasukan_per_periode', ) }}" min="0" step="10000" required>
                            </div>
                        </div>

                        <!-- Periode Pemasukan -->
                        <div class="col-md-6">
                            <label for="periode_pemasukan" class="form-label fw-semibold text-dark">
                                Frekuensi Pemasukan <span class="text-danger">*</span>
                            </label>
                            <select class="form-select bg-light" id="periode_pemasukan" name="periode_pemasukan" required>
                                <option value="bulanan" {{ old('periode_pemasukan', 'bulanan') == 'bulanan' ? 'selected' : '' }}>Bulanan (Setiap Bulan)</option>
                                <option value="mingguan" {{ old('periode_pemasukan') == 'mingguan' ? 'selected' : '' }}>Mingguan (Setiap Minggu)</option>
                                <option value="harian" {{ old('periode_pemasukan') == 'harian' ? 'selected' : '' }}>Harian (Setiap Hari)</option>
                            </select>
                        </div>

                        <!-- Pengeluaran Makan -->
                        <div class="col-md-4">
                            <label for="pengeluaran_makan" class="form-label small fw-semibold text-dark">
                                🍜 Makan & Minum <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="number" class="form-control" id="pengeluaran_makan" name="pengeluaran_makan" value="{{ old('pengeluaran_makan', ) }}" min="0" step="5000" required>
                            </div>
                        </div>

                        <!-- Pengeluaran Transportasi -->
                        <div class="col-md-4">
                            <label for="pengeluaran_transportasi" class="form-label small fw-semibold text-dark">
                                🛵 Bensin / Transport <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="number" class="form-control" id="pengeluaran_transportasi" name="pengeluaran_transportasi" value="{{ old('pengeluaran_transportasi', ) }}" min="0" step="5000" required>
                            </div>
                        </div>

                        <!-- Pengeluaran Lainnya -->
                        <div class="col-md-4">
                            <label for="pengeluaran_lainnya" class="form-label small fw-semibold text-dark">
                                📱 Pulsa & Lainnya <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="number" class="form-control" id="pengeluaran_lainnya" name="pengeluaran_lainnya" value="{{ old('pengeluaran_lainnya', ) }}" min="0" step="5000" required>
                            </div>
                        </div>

                        <!-- Pengeluaran Tidak Terduga -->
                        <div class="col-12">
                            <div class="alert alert-warning border-warning py-2 px-3 mb-0 d-flex align-items-start gap-2" style="background: rgba(255,193,7,0.08); border-radius: 10px;">
                                <i class="bi bi-exclamation-triangle-fill text-warning mt-1" style="font-size: 1rem;"></i>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold text-dark small mb-1">⚡ Pengeluaran Tidak Terduga (Opsional)</div>
                                    <div class="text-muted" style="font-size: 0.8rem;">Perkiraan biaya mendadak seperti sakit, perbaikan alat, atau kebutuhan mendesak lainnya yang belum masuk kategori di atas.</div>
                                </div>
                            </div>
                            <div class="input-group input-group-sm mt-2">
                                <span class="input-group-text bg-warning bg-opacity-25 text-dark fw-bold border-end-0" style="min-width:42px;">Rp</span>
                                <input type="number" class="form-control border-start-0" id="pengeluaran_tidak_terduga" name="pengeluaran_tidak_terduga" value="{{ old('pengeluaran_tidak_terduga', 0) }}" min="0" step="5000" placeholder="0 (isi jika ada estimasi)">
                                <span class="input-group-text bg-light text-muted small">/ periode</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Submit -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <a href="{{ route('tabungan.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    Batal
                </a>
                <button type="submit" class="btn btn-mint px-5 py-2 fs-6">
                    <i class="bi bi-check-circle-fill me-1"></i> Simpan & Mulai Celengan
                </button>
            </div>
        </div>

        <!-- Kolom Kanan: LIVE PREVIEW KARTU CELENGAN DIGITAL (Sticky) -->
        <div class="col-lg-5">
            <div class="sticky-top" style="top: 90px; z-index: 10;">
                <div class="card card-custom border-0 shadow-lg p-4" style="background: linear-gradient(135deg, #0b192c 0%, #1e3e62 100%); color: #ffffff;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-1 small">
                            <i class="bi bi-eye-fill me-1"></i> Live Preview Celengan
                        </span>
                        <span id="previewKategoriBadge" class="badge bg-success bg-opacity-75 rounded-pill px-2 py-1 small">
                            Umum
                        </span>
                    </div>

                    <small class="text-white-50 d-block" id="previewNamaMahasiswa">{{ Auth::user()->name ?? 'Mahasiswa' }}</small>
                    <h3 class="fw-bold text-white mb-2 brand-font" id="previewTujuan">Tabungan Masa Depan</h3>

                    <!-- Box Progress Saldo -->
                    <div class="p-3 rounded-4 mb-3" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(8px);">
                        <div class="d-flex justify-content-between text-white-50 small mb-1">
                            <span>Saldo Ditargetkan:</span>
                            <strong class="text-white" id="previewTarget">Rp 5.000.000</strong>
                        </div>
                        <div class="d-flex justify-content-between text-white-50 small mb-2">
                            <span>Saldo Setelah 1 Periode:</span>
                            <strong class="text-warning fs-5 fw-bold" id="previewSaldoSetelah">Rp 1.300.000</strong>
                        </div>

                        <!-- Progress Bar Live -->
                        <div class="progress" style="height: 10px; background-color: rgba(244, 240, 240, 0.2); border-radius: 10px;">
                            <div class="progress-bar bg-success" id="previewProgressBar" role="progressbar" style="width: 26%;"></div>
                        </div>
                        <div class="d-flex justify-content-between small text-white-50 mt-1">
                            <span id="previewProgressPersen">26% Terkumpul</span>
                            <span id="previewSisaTarget">Sisa Rp 3.700.000</span>
                        </div>
                    </div>

                    <!-- Analisis & Estimasi Waktu -->
                    <div class="p-3 rounded-4 mb-3" style="background: rgba(0,0,0,0.2);">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small text-white-50">Uang Disisihkan / Periode:</span>
                            <strong class="text-success fs-6 fw-bold" id="previewNabungPerPeriode">+Rp 800.000</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small text-white-50">Estimasi Target Tercapai:</span>
                            <strong class="text-white small" id="previewEstimasi">~5 Bulan lagi</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-white-50">Status Arus Kas:</span>
                            <span class="badge bg-success text-white" id="previewStatus">Surplus Sehat</span>
                        </div>
                    </div>

                    <div class="small text-white-50 text-center">
                        <i class="bi bi-lightbulb-fill text-warning me-1"></i> Perhitungan otomatis diperbarui saat Anda mengetik data.
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
    // Preset Populer Mahasiswa
    function applyPreset(tujuan, kategori, target, catatan) {
        document.getElementById('tujuan_tabungan').value = tujuan;
        document.getElementById('kategori').value = kategori;
        document.getElementById('target_tabungan').value = target;
        document.getElementById('catatan_impian').value = catatan;
        updateLivePreview();
    }

    function updateLivePreview() {
        let nama = @json(Auth::user()->name ?? 'Mahasiswa');
        let tujuan = document.getElementById('tujuan_tabungan').value || 'Tabungan Impian';
        let kategori = document.getElementById('kategori').value || 'Umum';
        let target = parseFloat(document.getElementById('target_tabungan').value || 0);
        let saldoAwal = parseFloat(document.getElementById('saldo_awal').value || 0);
        let pemasukan = parseFloat(document.getElementById('pemasukan_per_periode').value || 0);
        let periode = document.getElementById('periode_pemasukan').value || 'bulan';
        let makan = parseFloat(document.getElementById('pengeluaran_makan').value || 0);
        let trans = parseFloat(document.getElementById('pengeluaran_transportasi').value || 0);
        let lain = parseFloat(document.getElementById('pengeluaran_lainnya').value || 0);
        let tidakTerduga = parseFloat(document.getElementById('pengeluaran_tidak_terduga').value || 0);

        let totalPengeluaran = makan + trans + lain + tidakTerduga;
        let tabunganPerPeriode = pemasukan - totalPengeluaran;
        let saldoSetelah = saldoAwal + tabunganPerPeriode;
        let sisaTarget = target - saldoSetelah;
        if (sisaTarget < 0) sisaTarget = 0;

        let persen = target > 0 ? (saldoSetelah / target) * 100 : 100;
        persen = Math.min(100, Math.max(0, Math.round(persen)));

        // Update DOM
        document.getElementById('previewNamaMahasiswa').innerText = nama;
        document.getElementById('previewTujuan').innerText = tujuan;
        document.getElementById('previewKategoriBadge').innerText = kategori;
        document.getElementById('previewTarget').innerText = formatRupiahJs(target);
        document.getElementById('previewSaldoSetelah').innerText = formatRupiahJs(saldoSetelah);
        document.getElementById('previewProgressBar').style.width = persen + '%';
        document.getElementById('previewProgressPersen').innerText = persen + '% Terkumpul';
        document.getElementById('previewSisaTarget').innerText = 'Sisa ' + formatRupiahJs(sisaTarget);

        let elNabung = document.getElementById('previewNabungPerPeriode');
        if (tabunganPerPeriode >= 0) {
            elNabung.innerText = '+' + formatRupiahJs(tabunganPerPeriode) + ' / ' + periode;
            elNabung.className = 'text-success fs-6 fw-bold';
        } else {
            elNabung.innerText = formatRupiahJs(tabunganPerPeriode) + ' / ' + periode;
            elNabung.className = 'text-danger fs-6 fw-bold';
        }

        let elStatus = document.getElementById('previewStatus');
        let elEstimasi = document.getElementById('previewEstimasi');

        if (saldoSetelah >= target && target > 0) {
            elStatus.innerText = 'Target Tercapai!';
            elStatus.className = 'badge bg-success text-white';
            elEstimasi.innerText = 'Tercapai pada periode ini';
        } else if (tabunganPerPeriode <= 0) {
            elStatus.innerText = 'Defisit / Belum Ada Tabungan';
            elStatus.className = 'badge bg-danger text-white';
            elEstimasi.innerText = 'Perlu kurangi pengeluaran';
        } else {
            elStatus.innerText = 'Surplus Sehat';
            elStatus.className = 'badge bg-info text-dark';
            let periodeButuh = Math.ceil(sisaTarget / tabunganPerPeriode);
            elEstimasi.innerText = '~' + periodeButuh + ' ' + periode + ' lagi';
        }
    }

    // Pasang Event Listener untuk Live Preview
    const formInputs = [
        'tujuan_tabungan', 'kategori', 'target_tabungan',
        'saldo_awal', 'pemasukan_per_periode', 'periode_pemasukan',
        'pengeluaran_makan', 'pengeluaran_transportasi', 'pengeluaran_lainnya',
        'pengeluaran_tidak_terduga'
    ];

    formInputs.forEach(id => {
        let el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', updateLivePreview);
            el.addEventListener('change', updateLivePreview);
        }
    });

    // Jalankan sekali saat halaman dimuat
    updateLivePreview();
</script>
@endsection
