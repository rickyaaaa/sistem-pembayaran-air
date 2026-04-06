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
                                <div class="fw-semibold" style="font-size:0.8125rem;">{{ $payment->resident->name ?? '-' }}
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
        <div class="card-header">
            <span><i class="bi bi-grid me-2"></i>Matrix Pemasukan Per Blok {{ $year }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Blok</th>
                        @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'] as $mn)
                            <th class="text-end">{{ $mn }}</th>
                        @endforeach
                        <th class="text-end pe-3">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($blockMonthlyIncome as $blok => $months)
                        <tr>
                            <td class="fw-semibold ps-3">{{ $blok }}</td>
                            @for($m = 1; $m <= 12; $m++)
                                <td class="text-end" style="font-size:.8rem;">
                                    {{ $months[$m] > 0 ? number_format($months[$m] / 1000, 0) . 'k' : '-' }}
                                </td>
                            @endfor
                            <td class="text-end fw-semibold pe-3 text-primary">
                                Rp {{ number_format(array_sum($months), 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>