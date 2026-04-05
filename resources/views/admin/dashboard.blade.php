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
                <div class="stat-value stat-value-sm">Rp {{ number_format($totalIncome + $totalRegistrations, 0, ',', '.') }}</div>
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
            <div class="card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-primary bg-opacity-10">
                        <i class="bi bi-people text-primary" style="font-size:1.25rem;"></i>
                    </div>
                    <div>
                        <div style="font-size:0.75rem;" class="text-muted">Total Warga Aktif</div>
                        <div class="fw-bold">{{ $totalResidents }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-danger bg-opacity-10">
                        <i class="bi bi-exclamation-triangle text-danger" style="font-size:1.25rem;"></i>
                    </div>
                    <div>
                        <div style="font-size:0.75rem;" class="text-muted">Belum Bayar (Bln Ini)</div>
                        <div class="fw-bold">{{ $unpaidBills }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Income Table -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="table-wrapper">
                <div class="card-header">
                    <span><i class="bi bi-graph-up me-2"></i>Pemasukan Bulanan {{ $year }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Bulan</th>
                                <th class="text-end">Pemasukan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                            @endphp
                            @for($m = 1; $m <= 12; $m++)
                                <tr>
                                    <td>{{ $months[$m-1] }}</td>
                                    <td class="text-end fw-semibold">Rp {{ number_format($monthlyIncome[$m], 0, ',', '.') }}</td>
                                </tr>
                            @endfor
                            <tr class="table-light">
                                <td class="fw-bold">Total</td>
                                <td class="text-end fw-bold text-primary">Rp {{ number_format(array_sum($monthlyIncome), 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="col-lg-4">
            <div class="table-wrapper">
                <div class="card-header">
                    <span><i class="bi bi-clock-history me-2"></i>Pembayaran Terbaru</span>
                </div>
                <div class="card-body p-0">
                    @forelse($recentPayments as $payment)
                        <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
                            <div class="flex-grow-1">
                                <div class="fw-semibold" style="font-size:0.8125rem;">{{ $payment->resident->user->name ?? '-' }}</div>
                                <div class="text-muted" style="font-size:0.75rem;">{{ $payment->bill->period ?? '-' }}</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-semibold" style="font-size:0.8125rem;">Rp {{ number_format($payment->amount_paid, 0, ',', '.') }}</div>
                                {!! $payment->status_badge !!}
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4" style="font-size:0.8125rem;">
                            Belum ada pembayaran
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
