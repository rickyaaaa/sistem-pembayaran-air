<x-app-layout>
    <x-slot name="title">Tagihan Saya</x-slot>

    <!-- Filter -->
    <form method="GET" class="filter-bar animate-in">
        <div class="form-group" style="flex:0 0 auto;">
            <label class="form-label">Tahun</label>
            <select name="year" class="form-select" onchange="this.form.submit()">
                <option value="">Semua</option>
                @foreach($availableYears as $y)
                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="flex:0 0 auto;">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">Semua</option>
                <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Belum Bayar</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Lunas</option>
            </select>
        </div>
    </form>

    <div class="table-wrapper animate-in">
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
                    @forelse($bills as $bill)
                        <tr>
                            <td class="fw-semibold">{{ $bill->period }}</td>
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
                            <td colspan="4">
                                <div class="empty-state">
                                    <i class="bi bi-receipt"></i>
                                    <h5>Belum ada tagihan</h5>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($bills->hasPages())
            <div class="p-3 d-flex justify-content-center">{{ $bills->withQueryString()->links() }}</div>
        @endif
    </div>
</x-app-layout>
