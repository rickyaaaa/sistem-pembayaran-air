<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <!-- Welcome Card -->
    <div class="card mb-4 animate-in" style="background: linear-gradient(135deg, #2563eb, #0ea5e9); color: white; border: none;">
        <div class="card-body py-4">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-white bg-opacity-25 p-3">
                    <i class="bi bi-person-circle" style="font-size: 2rem;"></i>
                </div>
                <div>
                    <h4 class="mb-1 fw-bold">Selamat datang, {{ Auth::user()->name }}!</h4>
                    <p class="mb-0 opacity-75">
                        <i class="bi bi-house-door me-1"></i> Blok {{ $resident->block }} No. {{ $resident->house_number }}
                        ({{ strtoupper($resident->block_number) }})
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4 animate-in">
        <div class="col-sm-4">
            <div class="stat-card stat-danger">
                <div class="stat-icon"><i class="bi bi-exclamation-circle"></i></div>
                <div class="stat-label">Total Belum Dibayar</div>
                <div class="stat-value stat-value-sm">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card stat-warning">
                <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
                <div class="stat-label">Menunggu Konfirmasi</div>
                <div class="stat-value">{{ $totalPending }}</div>
            </div>
        </div>
        <div class="col-sm-4">
            @if($currentBill)
                <div class="stat-card {{ $currentBill->status === 'paid' ? 'stat-success' : ($currentBill->status === 'pending' ? 'stat-warning' : 'stat-info') }}">
                    <div class="stat-icon"><i class="bi bi-calendar-month"></i></div>
                    <div class="stat-label">Tagihan Bulan Ini</div>
                    <div class="stat-value stat-value-sm">Rp {{ number_format($currentBill->amount, 0, ',', '.') }}</div>
                </div>
            @else
                <div class="stat-card stat-info">
                    <div class="stat-icon"><i class="bi bi-calendar-month"></i></div>
                    <div class="stat-label">Tagihan Bulan Ini</div>
                    <div class="stat-value stat-value-sm">-</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Current Bill Action -->
    @if($currentBill && $currentBill->status === 'unpaid')
        <div class="card mb-4 animate-in border-start border-danger border-3">
            <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h6 class="fw-bold mb-1 text-danger"><i class="bi bi-exclamation-triangle me-1"></i> Tagihan {{ $currentBill->period }}</h6>
                    <p class="mb-0 text-muted">Segera lakukan pembayaran sebesar <strong>Rp {{ number_format($currentBill->amount, 0, ',', '.') }}</strong></p>
                </div>
                <a href="{{ route('resident.payments.create', $currentBill) }}" class="btn btn-danger">
                    <i class="bi bi-credit-card me-1"></i> Bayar Sekarang
                </a>
            </div>
        </div>
    @endif

    @if($currentBill && $currentBill->status === 'pending')
        <div class="card mb-4 animate-in border-start border-warning border-3">
            <div class="card-body">
                <h6 class="fw-bold mb-1 text-warning"><i class="bi bi-hourglass-split me-1"></i> Menunggu Konfirmasi</h6>
                <p class="mb-0 text-muted">Pembayaran untuk {{ $currentBill->period }} sedang menunggu konfirmasi admin.</p>
            </div>
        </div>
    @endif

    @if($currentBill && $currentBill->status === 'paid')
        <div class="card mb-4 animate-in border-start border-success border-3">
            <div class="card-body">
                <h6 class="fw-bold mb-1 text-success"><i class="bi bi-check-circle me-1"></i> Lunas</h6>
                <p class="mb-0 text-muted">Tagihan {{ $currentBill->period }} sudah lunas. Terima kasih!</p>
            </div>
        </div>
    @endif

    <!-- Recent Bills -->
    <div class="table-wrapper animate-in">
        <div class="card-header">
            <span><i class="bi bi-receipt me-2"></i>Tagihan Terakhir</span>
            <a href="{{ route('resident.bills.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Periode</th>
                        <th class="text-end">Jumlah</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentBills as $bill)
                        <tr>
                            <td>{{ $bill->period }}</td>
                            <td class="text-end">Rp {{ number_format($bill->amount, 0, ',', '.') }}</td>
                            <td>{!! $bill->status_badge !!}</td>
                            <td>
                                @if($bill->status === 'unpaid')
                                    <a href="{{ route('resident.payments.create', $bill) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-credit-card me-1"></i> Bayar
                                    </a>
                                @else
                                    <a href="{{ route('resident.bills.show', $bill) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-eye me-1"></i> Detail
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Belum ada tagihan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
