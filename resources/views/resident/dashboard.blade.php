<x-public-layout>
    <x-slot name="title">Beranda Warga</x-slot>

    {{-- ===== HERO / SEARCH SECTION ===== --}}
<div class="card border-0 mb-4 overflow-hidden animate-in"
     style="background: linear-gradient(135deg, #1d4ed8 0%, #0891b2 100%); color: #fff; border-radius: 16px;">
    <div class="card-body p-3 p-md-5">
        <div class="row align-items-center g-3">
            <div class="col-12 col-lg-7">

                {{-- Logo + Judul (mobile: row, desktop: row juga) --}}
                <div class="d-flex align-items-center gap-3 mSb-3">
                    {{-- Logo hanya tampil di mobile di sini --}}
                    <div class="d-flex d-lg-none flex-shrink-0">
                        <img src="{{ asset('images/logo.jpg') }}" alt="SAB Swadaya"
                             style="height:52px;width:52px;object-fit:cover;border-radius:10px;
                                    background:rgba(255,255,255,.15);padding:4px;opacity:.92;">
                    </div>
                    {{-- Icon droplet hanya desktop --}}
                    <div class="rounded-circle d-none d-lg-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:54px;height:54px;background:rgba(255,255,255,.18);">
                        <i class="bi bi-droplet-fill" style="font-size:1.6rem;"></i>
                    </div>
                    <div>
                        <h1 class="fw-bold mb-0" style="font-size:clamp(1rem, 4vw, 1.35rem); line-height:1.3;">
                            Portal Informasi SAB Swadaya
                        </h1>
                        <p class="mb-0 opacity-75" style="font-size:.8rem;">
                            Perum The Spring Ville — Tahun {{ $year }}
                        </p>
                    </div>
                </div>

                <p class="opacity-85 mb-3" style="font-size:.875rem;max-width:480px;">
                    Cek tagihan air, riwayat pembayaran, dan laporan keuangan SAB secara transparan tanpa perlu login.
                </p>

                {{-- House Number Search --}}
                <form action="{{ route('resident.bills.index') }}" method="GET">
                    <div class="d-flex gap-2">
                        <div class="input-group">
                            <span class="input-group-text bg-white text-primary border-0 fw-semibold px-2 px-md-3"
                                  style="font-size:.85rem;">
                                <i class="bi bi-house-door me-1"></i>
                                <span class="d-none d-sm-inline">No. Rumah</span>
                            </span>
                            <input type="text"
                                   name="house_number"
                                   class="form-control border-0"
                                   placeholder="Contoh: A3, C18, D1"
                                   style="font-size:.9rem;"
                                   autocomplete="off"
                                   required>
                            <button type="submit" class="btn btn-warning fw-semibold px-3">
                                <i class="bi bi-search"></i>
                                <span class="d-none d-sm-inline ms-1">Cek Tagihan</span>
                            </button>
                        </div>
                    </div>
                </form>

            </div>

            {{-- Logo desktop (tetap di kanan) --}}
            <div class="col-lg-5 d-none d-lg-flex justify-content-end">
                <img src="{{ asset('images/logo.jpg') }}" alt="SAB Swadaya"
                     style="height:160px;width:auto;object-fit:contain;border-radius:12px;
                            background:rgba(255,255,255,.15);padding:8px;opacity:.92;">
            </div>
        </div>
    </div>
</div>

    {{-- ===== YEAR FILTER ===== --}}
    <form method="GET" class="d-flex align-items-center gap-2 mb-3">
        <label class="form-label mb-0 text-muted" style="font-size:0.8125rem;">Tahun:</label>
        <select name="year" class="form-select form-select-sm" style="width: auto; min-width: 90px; padding-right: 2rem;" onchange="this.form.submit()">
            @foreach($availableYears as $y)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
    </form>

    {{-- ===== FINANCIAL SUMMARY CARDS ===== --}}
    <h2 class="h6 fw-bold text-muted text-uppercase letter-spacing mb-3" style="letter-spacing:.08em;font-size:.72rem;">
        <i class="bi bi-bar-chart-line me-1"></i> Ringkasan Keuangan {{ $year }}
    </h2>

    <div class="row g-3 mb-4 animate-in">
        {{-- Income --}}
        <div class="col-sm-6 col-md-3">
            <div class="card h-100 border-0 shadow-sm" style="border-radius:14px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted" style="font-size:.78rem;font-weight:600;letter-spacing:.05em;text-transform:uppercase;">Pemasukan</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width:36px;height:36px;background:#dcfce7;">
                            <i class="bi bi-arrow-down-circle-fill text-success"></i>
                        </div>
                    </div>
                    <div class="fw-bold" style="font-size:1.1rem;color:#16a34a;">
                        Rp {{ number_format($totalIncome + $totalRegistrations, 0, ',', '.') }}
                    </div>
                    <div class="text-muted" style="font-size:.75rem;">Iuran + Pemasukan Lainnya</div>
                </div>
            </div>
        </div>

        {{-- Expenses --}}
        <div class="col-sm-6 col-md-3">
            <div class="card h-100 border-0 shadow-sm" style="border-radius:14px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted" style="font-size:.78rem;font-weight:600;letter-spacing:.05em;text-transform:uppercase;">Pengeluaran</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width:36px;height:36px;background:#fee2e2;">
                            <i class="bi bi-arrow-up-circle-fill text-danger"></i>
                        </div>
                    </div>
                    <div class="fw-bold" style="font-size:1.1rem;color:#dc2626;">
                        Rp {{ number_format($totalExpenses, 0, ',', '.') }}
                    </div>
                    <div class="text-muted" style="font-size:.75rem;">Operasional SAB</div>
                </div>
            </div>
        </div>

        {{-- Balance --}}
        <div class="col-sm-6 col-md-3">
            <div class="card h-100 border-0 shadow-sm" style="border-radius:14px; background: {{ $currentBalance >= 0 ? 'linear-gradient(135deg,#f0fdf4,#dcfce7)' : 'linear-gradient(135deg,#fff1f2,#fee2e2)' }};">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted" style="font-size:.78rem;font-weight:600;letter-spacing:.05em;text-transform:uppercase;">Saldo</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width:36px;height:36px;background:rgba(255,255,255,.7);">
                            <i class="bi bi-wallet2 {{ $currentBalance >= 0 ? 'text-success' : 'text-danger' }}"></i>
                        </div>
                    </div>
                    <div class="fw-bold" style="font-size:1.1rem;color:{{ $currentBalance >= 0 ? '#15803d' : '#dc2626' }};">
                        Rp {{ number_format(abs($currentBalance), 0, ',', '.') }}
                    </div>
                    <div class="text-muted" style="font-size:.75rem;">{{ $currentBalance >= 0 ? 'Surplus' : 'Defisit' }}</div>
                </div>
            </div>
        </div>

        {{-- Quick Link --}}
        <div class="col-sm-6 col-md-3">
            <div class="card h-100 border-0 shadow-sm" style="border-radius:14px;background:linear-gradient(135deg,#eff6ff,#dbeafe);">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div>
                        <span class="text-muted" style="font-size:.78rem;font-weight:600;letter-spacing:.05em;text-transform:uppercase;">Cek Tagihan</span>
                        <p class="text-muted mt-1 mb-3" style="font-size:.8rem;">Masukkan nomor rumah untuk lihat tagihan Anda</p>
                    </div>
                    <a href="{{ route('resident.bills.index') }}" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-search me-1"></i> Cari Tagihan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1 animate-in">
        {{-- ===== LAST 5 PEMASUKAN ===== --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius:14px;">
                <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between py-3 px-4"
                     style="border-radius:14px 14px 0 0;border-bottom:1px solid #f1f5f9;">
                    <span class="fw-semibold" style="font-size:.9375rem;">
                        <i class="bi bi-box-arrow-in-down-right me-2 text-success"></i>Pembayaran Terbaru
                    </span>
                    <span class="badge bg-success bg-opacity-10 text-success" style="font-size:.72rem;">{{ $year }}</span>
                </div>
                <div class="card-body p-0">
                    @forelse($recentPayments as $payment)
                        <div class="d-flex align-items-center gap-3 px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}"
                             style="transition:background .15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width:40px;height:40px;background:#dcfce7;">
                                <i class="bi bi-wallet2 text-success"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-semibold text-truncate" style="font-size:.875rem;">
                                    Blok {{ strtoupper($payment->resident->block_number) }}
                                </div>
                                <div class="text-muted" style="font-size:.75rem;">
                                    {{ $payment->bill->period }}
                                </div>
                            </div>
                            <div class="text-end flex-shrink-0">
                                <div class="fw-bold text-success" style="font-size:.9rem;">
                                    Rp {{ number_format($payment->amount_paid, 0, ',', '.') }}
                                </div>
                                <div class="mt-1">
                                    <x-status-badge :status="$payment->status" />
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-inbox" style="font-size:2rem;opacity:.4;"></i>
                            <p class="mt-2 mb-0">Belum ada pembayaran terbaru</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ===== LAST 5 EXPENSES ===== --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius:14px;">
                <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between py-3 px-4"
                     style="border-radius:14px 14px 0 0;border-bottom:1px solid #f1f5f9;">
                    <span class="fw-semibold" style="font-size:.9375rem;">
                        <i class="bi bi-cash-stack me-2 text-primary"></i>Pengeluaran Terakhir
                    </span>
                    <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size:.72rem;">{{ $year }}</span>
                </div>
                <div class="card-body p-0">
            @forelse($recentExpenses as $expense)
                <div class="d-flex align-items-center gap-3 px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}"
                     style="transition:background .15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:40px;height:40px;background:#eff6ff;">
                        <i class="bi bi-receipt text-primary"></i>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="fw-semibold text-truncate" style="font-size:.875rem;">{{ $expense->description }}</div>
                        <div class="text-muted" style="font-size:.75rem;">
                            {{ $expense->date->format('d F Y') }}
                            @if($expense->category)
                                &bull; <span class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size:.68rem;">{{ $expense->category }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-end flex-shrink-0">
                        <div class="fw-bold text-danger" style="font-size:.9rem;">
                            Rp {{ number_format($expense->amount, 0, ',', '.') }}
                        </div>
                        @if($expense->proof_file && $expense->proof_file !== 'manual')
                            <a href="{{ route('resident.expenses.proof', $expense) }}?token={{ sha1($expense->id . config('app.key')) }}"
                               target="_blank"
                               class="text-success text-decoration-none"
                               style="font-size:.72rem;">
                                <i class="bi bi-file-earmark-image me-1"></i>Lihat Bukti
                            </a>
                        @elseif($expense->proof_file)
                            <span class="text-success" style="font-size:.72rem;">
                                <i class="bi bi-check-circle-fill me-1"></i>Ada Bukti
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-5">
                    <i class="bi bi-inbox" style="font-size:2rem;opacity:.4;"></i>
                    <p class="mt-2 mb-0">Belum ada pengeluaran</p>
                </div>
            @endforelse
        </div>
    </div>
    </div>

    {{-- ===== BLOCK MONTHLY INCOME MATRIX ===== --}}
    <div class="card border-0 shadow-sm animate-in mt-4" style="border-radius:14px;" id="matrix-section">
        <div class="card-header bg-white border-0 d-flex flex-column flex-md-row align-items-md-center justify-content-between py-3 px-4 gap-3"
             style="border-radius:14px 14px 0 0;border-bottom:1px solid #f1f5f9;">
            <span class="fw-semibold" style="font-size:.9375rem;">
                <i class="bi bi-grid me-2 text-primary"></i>Pemasukan per No. Blok per Bulan
            </span>
            <form id="resident-matrix-filter-form" method="GET" action="{{ route('resident.dashboard') }}" class="d-flex flex-wrap align-items-center gap-2 m-0">
                <input type="hidden" name="year" value="{{ $year }}">
                <input type="text" name="block_search" value="{{ request('block_search') }}" 
                    class="form-control form-control-sm" style="width: 100px;" placeholder="Cari Blok...">
                
                <select name="unpaid_month" class="form-select form-select-sm" style="width: auto;">
                    <option value="">Status Bayar: Semua</option>
                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $idx => $mName)
                        <option value="{{ $idx + 1 }}" {{ request('unpaid_month') == ($idx + 1) ? 'selected' : '' }}>
                            Belum Lunas: {{ $mName }}
                        </option>
                    @endforeach
                </select>
                
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                @if(request('block_search') || request('unpaid_month'))
                    <a href="{{ route('resident.dashboard', ['year' => $year]) }}#matrix-section" class="btn btn-sm btn-outline-secondary">Reset</a>
                @endif
                <div class="spinner-border spinner-border-sm text-primary d-none ms-1" id="resident-matrix-spinner" role="status"></div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" id="resident-matrix-container">
                @include('admin.partials.dashboard_matrix_table')
            </div>
        </div>
    </div>

    <script>
        // Animate cards on scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('visible');
                }
            });
        }, { threshold: 0.1 });
        document.querySelectorAll('.animate-in').forEach(el => observer.observe(el));

        document.addEventListener('DOMContentLoaded', function() {
            const matrixForm = document.getElementById('resident-matrix-filter-form');
            if (matrixForm) {
                matrixForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const btn = this.querySelector('button[type="submit"]');
                    const spinner = document.getElementById('resident-matrix-spinner');
                    btn.disabled = true;
                    spinner.classList.remove('d-none');

                    const url = new URL(this.action || window.location.href);
                    const formData = new FormData(this);
                    formData.forEach((value, key) => url.searchParams.set(key, value));
                    url.searchParams.set('partial', 'matrix');

                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById('resident-matrix-container').innerHTML = html;
                        btn.disabled = false;
                        spinner.classList.add('d-none');
                    })
                    .catch(err => {
                        console.error(err);
                        window.location.href = url.toString().replace('&partial=matrix', '');
                    });
                });
            }
        });
    </script>
</x-public-layout>
