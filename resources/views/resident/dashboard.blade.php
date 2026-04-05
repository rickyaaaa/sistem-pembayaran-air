<x-public-layout>
    <x-slot name="title">Beranda Warga</x-slot>

    {{-- ===== HERO / SEARCH SECTION ===== --}}
    <div class="card border-0 mb-4 overflow-hidden animate-in"
         style="background: linear-gradient(135deg, #1d4ed8 0%, #0891b2 100%); color: #fff; border-radius: 16px;">
        <div class="card-body p-4 p-md-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width:54px;height:54px;background:rgba(255,255,255,.18);">
                            <i class="bi bi-droplet-fill" style="font-size:1.6rem;"></i>
                        </div>
                        <div>
                            <h1 class="h4 fw-bold mb-0">Portal Informasi SAB Swadaya</h1>
                            <p class="mb-0 opacity-75" style="font-size:.875rem;">
                                Perum The Spring Ville — Tahun {{ $year }}
                            </p>
                        </div>
                    </div>
                    <p class="opacity-85 mb-4" style="font-size:.9375rem;max-width:480px;">
                        Cek tagihan air, riwayat pembayaran, dan laporan keuangan SAB secara transparan tanpa perlu login.
                    </p>

                    {{-- House Number Search --}}
                    <form action="{{ route('resident.bills.index') }}" method="GET" class="d-flex gap-2 flex-wrap">
                        <div class="input-group" style="max-width:380px;">
                            <span class="input-group-text bg-white text-primary border-0 fw-semibold">
                                <i class="bi bi-house-door me-1"></i> No. Rumah
                            </span>
                            <input type="text"
                                   name="house_number"
                                   class="form-control border-0"
                                   placeholder="Contoh: A-01, B-12 ..."
                                   style="font-size:.9375rem;"
                                   autocomplete="off"
                                   required>
                            <button type="submit" class="btn btn-warning fw-semibold px-4">
                                <i class="bi bi-search me-1"></i> Cek Tagihan
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-lg-5 d-none d-lg-flex justify-content-end">
                    <img src="{{ asset('images/logo.jpg') }}" alt="SAB Swadaya"
                         style="height:160px;width:auto;object-fit:contain;border-radius:12px;
                                background:rgba(255,255,255,.15);padding:8px;opacity:.92;">
                </div>
            </div>
        </div>
    </div>

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
                    <div class="text-muted" style="font-size:.75rem;">Iuran + Pendaftaran</div>
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

    {{-- ===== LAST 5 EXPENSES ===== --}}
    <div class="card border-0 shadow-sm animate-in" style="border-radius:14px;">
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
                        @if($expense->proof_file)
                            <a href="{{ Storage::url($expense->proof_file) }}"
                               target="_blank"
                               class="text-primary text-decoration-none"
                               style="font-size:.72rem;">
                                <i class="bi bi-file-earmark-image me-1"></i>Lihat Bukti
                            </a>
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

    @push('scripts')
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
    </script>
    @endpush
</x-public-layout>
