<x-app-layout>
    <x-slot name="title">Laporan Keuangan</x-slot>

    <!-- Year Filter -->
    <div class="d-flex justify-content-end mb-3 animate-in">
        <form method="GET" class="d-flex align-items-center gap-2">
            <label class="form-label mb-0 text-muted" style="font-size:0.8125rem;">Tahun:</label>
            <select name="year" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                @foreach($availableYears as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4 animate-in">
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-primary">
                <div class="stat-icon"><i class="bi bi-arrow-down-circle"></i></div>
                <div class="stat-label">Total Pemasukan</div>
                <div class="stat-value stat-value-sm">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-info">
                <div class="stat-icon"><i class="bi bi-person-plus"></i></div>
                <div class="stat-label">Iuran Pendaftaran</div>
                <div class="stat-value stat-value-sm">Rp {{ number_format($totalRegistrations, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-danger">
                <div class="stat-icon"><i class="bi bi-arrow-up-circle"></i></div>
                <div class="stat-label">Total Pengeluaran</div>
                <div class="stat-value stat-value-sm">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-success">
                <div class="stat-icon"><i class="bi bi-piggy-bank"></i></div>
                <div class="stat-label">Saldo Akhir</div>
                <div class="stat-value stat-value-sm">Rp {{ number_format($endingBalance, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <!-- Bill Collection Rate -->
    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><i class="bi bi-pie-chart me-2"></i> Tingkat Koleksi Tagihan</div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <span class="display-4 fw-bold text-primary">{{ $billStats['collection_rate'] }}%</span>
                    </div>
                    <div class="d-flex justify-content-around text-center">
                        <div>
                            <div class="fw-bold text-success">{{ $billStats['paid'] }}</div>
                            <small class="text-muted">Lunas</small>
                        </div>
                        <div>
                            <div class="fw-bold text-warning">{{ $billStats['pending'] }}</div>
                            <small class="text-muted">Pending</small>
                        </div>
                        <div>
                            <div class="fw-bold text-danger">{{ $billStats['unpaid'] }}</div>
                            <small class="text-muted">Belum</small>
                        </div>
                        <div>
                            <div class="fw-bold">{{ $billStats['total'] }}</div>
                            <small class="text-muted">Total</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expense by Category -->
            @if($expensesByCategory->isNotEmpty())
                <div class="card mt-3">
                    <div class="card-header"><i class="bi bi-tags me-2"></i> Pengeluaran per Kategori</div>
                    <div class="card-body p-0">
                        @foreach($expensesByCategory as $cat)
                            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                                <span class="badge bg-secondary">{{ $cat->category }}</span>
                                <span class="fw-semibold" style="font-size:0.8125rem;">Rp {{ number_format($cat->total, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Monthly Breakdown -->
        <div class="col-lg-8">
            <div class="table-wrapper">
                <div class="card-header">
                    <span><i class="bi bi-calendar3 me-2"></i>Ringkasan Bulanan {{ $year }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Bulan</th>
                                <th class="text-end">Pemasukan</th>
                                <th class="text-end">Pendaftaran</th>
                                <th class="text-end">Pengeluaran</th>
                                <th class="text-end">Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                                $totalMIncome = 0;
                                $totalMReg = 0;
                                $totalMExpense = 0;
                            @endphp
                            @for($m = 1; $m <= 12; $m++)
                                @php
                                    $totalMIncome += $monthlyData[$m]['income'];
                                    $totalMReg += $monthlyData[$m]['registrations'];
                                    $totalMExpense += $monthlyData[$m]['expenses'];
                                @endphp
                                <tr>
                                    <td>{{ $months[$m-1] }}</td>
                                    <td class="text-end">Rp {{ number_format($monthlyData[$m]['income'], 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($monthlyData[$m]['registrations'], 0, ',', '.') }}</td>
                                    <td class="text-end text-danger">Rp {{ number_format($monthlyData[$m]['expenses'], 0, ',', '.') }}</td>
                                    <td class="text-end fw-semibold {{ $monthlyData[$m]['balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        Rp {{ number_format($monthlyData[$m]['balance'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endfor
                            <tr class="table-light fw-bold">
                                <td>Total</td>
                                <td class="text-end text-primary">Rp {{ number_format($totalMIncome, 0, ',', '.') }}</td>
                                <td class="text-end text-info">Rp {{ number_format($totalMReg, 0, ',', '.') }}</td>
                                <td class="text-end text-danger">Rp {{ number_format($totalMExpense, 0, ',', '.') }}</td>
                                <td class="text-end {{ $endingBalance >= 0 ? 'text-success' : 'text-danger' }}">Rp {{ number_format($endingBalance, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
