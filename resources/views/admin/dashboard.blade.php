<x-app-layout>
    <x-slot name="title">Dashboard Admin</x-slot>

    <!-- Year Filter -->
    <div class="d-flex justify-content-end mb-3">
        <form method="GET" class="d-flex align-items-center gap-2">
            <label class="form-label mb-0 text-muted" style="font-size:0.8125rem;">Tahun:</label>
            <select name="year" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                @foreach($availableYears as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Stats Row -->
    <div class="row g-3 mb-4 animate-in">
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-primary">
                <div class="stat-icon"><i class="bi bi-wallet2"></i></div>
                <div class="stat-label">Total Pemasukan</div>
                <div class="stat-value stat-value-sm">Rp
                    {{ number_format($totalIncome + $totalRegistrations, 0, ',', '.') }}
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-danger">
                <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
                <div class="stat-label">Total Pengeluaran</div>
                <div class="stat-value stat-value-sm">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-success">
                <div class="stat-icon"><i class="bi bi-piggy-bank"></i></div>
                <div class="stat-label">Saldo</div>
                <div class="stat-value stat-value-sm">Rp {{ number_format($currentBalance, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-warning">
                <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
                <div class="stat-label">Menunggu Konfirmasi</div>
                <div class="stat-value">{{ $pendingPayments }}</div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="p-3 bg-white rounded shadow-sm border-start border-4 border-primary">
                <div class="text-muted small">Warga Aktif</div>
                <div class="h5 mb-0 fw-bold">{{ $totalResidents }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="p-3 bg-white rounded shadow-sm border-start border-4 border-info">
                <div class="text-muted small">Tagihan Blm Lunas (Bulan Ini)</div>
                <div class="h5 mb-0 fw-bold text-info">{{ $unpaidBills }} / {{ $totalResidents }}</div>
            </div>
        </div>
    </div>

    <!-- Charts & Summary Row -->
    <div class="row g-4 mb-4 animate-in">
        <div class="col-lg-8">
            <div class="table-wrapper">
                <div class="card-header">
                    <span><i class="bi bi-bar-chart me-2"></i>Rekap Pemasukan Bulanan {{ $year }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr class="table-light">
                                <th>Bulan</th>
                                <th class="text-end">Total Pemasukan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $monthsList = [
                                    1 => 'Januari',
                                    2 => 'Februari',
                                    3 => 'Maret',
                                    4 => 'April',
                                    5 => 'Mei',
                                    6 => 'Juni',
                                    7 => 'Juli',
                                    8 => 'Agustus',
                                    9 => 'September',
                                    10 => 'Oktober',
                                    11 => 'November',
                                    12 => 'Desember'
                                ];
                            @endphp
                            @foreach($monthsList as $num => $name)
                                <tr>
                                    <td>{{ $name }}</td>
                                    <td class="text-end fw-semibold">Rp
                                        {{ number_format($monthlyIncome[$num], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="table-light">
                                <td class="fw-bold">Total</td>
                                <td class="text-end fw-bold text-primary">Rp
                                    {{ number_format(array_sum($monthlyIncome), 0, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="table-wrapper">
                <div class="card-header">
                    <span><i class="bi bi-clock-history me-2"></i>Pembayaran Terbaru</span>
                </div>
                <div class="card-body p-0">
                    @forelse($recentPayments as $payment)
                        <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
                            <div class="flex-grow-1">
                                <div class="fw-semibold" style="font-size:0.8125rem;">Blok {{ $payment->resident ? strtoupper($payment->resident->block_number) : '-' }}
                                </div>
                                <div class="text-muted" style="font-size:0.75rem;">{{ $payment->bill->period ?? '-' }}</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-semibold" style="font-size:0.8125rem;">Rp
                                    {{ number_format($payment->amount_paid, 0, ',', '.') }}
                                </div>
                                <x-status-badge :status="$payment->status" />
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">Belum ada pembayaran.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Pemasukan & Pengeluaran -->
    <div class="row g-4 mb-4 animate-in">
        <div class="col-lg-6">
            <div class="table-wrapper h-100">
                <div class="card-header">
                    <span><i class="bi bi-box-arrow-in-down-right me-2 text-success"></i>Pemasukan Terbaru</span>
                </div>
                <div class="card-body p-0">
                    @foreach($registrations->take(5) as $reg)
                        <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
                            <div class="flex-grow-1">
                                <div class="fw-semibold" style="font-size:0.8125rem;">
                                    @if($reg->resident)
                                        {{ str_replace(' ', '', $reg->resident->block_number) }} - {{ $reg->resident->name }}
                                    @else
                                        Tanpa Warga
                                    @endif
                                </div>
                                <div class="text-muted" style="font-size:0.75rem;">
                                    {{ $reg->payment_date->format('d/m/Y') }}
                                    @if($reg->category)
                                        · <span class="badge {{ $reg->category->badgeClass() }}"
                                            style="font-size:0.65rem;">{{ $reg->category->label() }}</span>
                                    @endif
                                </div>
                                @if($reg->notes)
                                    <div class="mt-1 small text-info" style="font-size:0.7rem;">
                                        <i class="bi bi-info-circle me-1"></i>{{ $reg->notes }}
                                    </div>
                                @endif
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-success" style="font-size:0.8125rem;">Rp
                                    {{ number_format($reg->amount, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if($registrations->isEmpty())
                        <div class="text-center text-muted py-4">Belum ada pemasukan.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="table-wrapper h-100">
                <div class="card-header">
                    <span><i class="bi bi-cash-stack me-2 text-danger"></i>Pengeluaran Terakhir</span>
                </div>
                <div class="card-body p-0">
                    @foreach($expenses->take(5) as $expense)
                        <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
                            <div class="flex-grow-1">
                                <div class="fw-semibold" style="font-size:0.8125rem;">{{ $expense->description }}</div>
                                <div class="text-muted" style="font-size:0.75rem;">{{ $expense->date->format('d/m/Y') }}
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-danger" style="font-size:0.8125rem;">Rp
                                    {{ number_format($expense->amount, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if($expenses->isEmpty())
                        <div class="text-center text-muted py-4">Belum ada pengeluaran.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Matrix -->
    <div class="table-wrapper mt-4 animate-in">
        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <span><i class="bi bi-grid me-2"></i>Matrix Pemasukan Per Blok {{ $year }}</span>
            <form id="matrix-filter-form" method="GET" action="{{ route('admin.dashboard') }}" class="d-flex flex-wrap align-items-center gap-2">
                <input type="hidden" name="year" value="{{ $year }}">
                
                <input type="text" name="block_search" value="{{ request('block_search') }}" 
                    class="form-select form-select-sm" style="width: 100px;" placeholder="Cari Blok...">
                
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
                    <a href="{{ route('admin.dashboard', ['year' => $year]) }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                @endif
                <div class="spinner-border spinner-border-sm text-primary d-none ms-1" id="matrix-spinner" role="status"></div>
            </form>
        </div>
        <div class="table-responsive" id="matrix-container">
            @include('admin.partials.dashboard_matrix_table')
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const matrixForm = document.getElementById('matrix-filter-form');
            if (matrixForm) {
                matrixForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const btn = this.querySelector('button[type="submit"]');
                    const spinner = document.getElementById('matrix-spinner');
                    btn.disabled = true;
                    spinner.classList.remove('d-none');

                    const url = new URL(this.action || window.location.href);
                    const formData = new FormData(this);
                    formData.forEach((value, key) => url.searchParams.set(key, value));
                    url.searchParams.set('partial', 'matrix');

                    fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById('matrix-container').innerHTML = html;
                        btn.disabled = false;
                        spinner.classList.add('d-none');
                    })
                    .catch(err => {
                        console.error(err);
                        btn.disabled = false;
                        spinner.classList.add('d-none');
                        window.location.href = url.toString().replace('&partial=matrix', '');
                    });
                });
            }
        });
    </script>
</x-app-layout>