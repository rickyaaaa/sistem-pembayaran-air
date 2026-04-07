<x-app-layout>
    <x-slot name="title">Konfirmasi Pembayaran</x-slot>

    <!-- Filter -->
    <form method="GET" class="filter-bar animate-in">
        <div class="form-group">
            <label class="form-label">Cari</label>
            <input type="text" name="search" class="form-control" placeholder="Nama / No. Blok" value="{{ request('search') }}">
        </div>
        <div class="form-group" style="flex:0 0 auto;">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">Semua</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>
        <div style="flex:0 0 auto;">
            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
        </div>
    </form>

    <div class="table-wrapper animate-in">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No. Blok</th>
                        <th>Nama</th>
                        <th>Periode</th>
                        <th>Tgl Bayar</th>
                        <th class="text-end">Jumlah</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $i => $payment)
                        <tr>
                            <td>{{ $payments->firstItem() + $i }}</td>
                            <td><span class="fw-semibold">{{ strtoupper($payment->resident->block_number) }}</span></td>
                            <td>{{ $payment->resident->name }}</td>
                            <td>{{ $payment->bill->period }}</td>
                            <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                            <td class="text-end">Rp {{ number_format($payment->amount_paid, 0, ',', '.') }}</td>
                            <td><x-status-badge :status="$payment->status" /></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-sm btn-outline-primary" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.payments.edit', $payment) }}" class="btn btn-sm btn-outline-info" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.payments.destroy', $payment) }}" method="POST"
                                          onsubmit="return confirm('Hapus pembayaran ini?')" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <i class="bi bi-credit-card-2-front"></i>
                                    <h5>Belum ada pembayaran</h5>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
            <div class="p-3 d-flex justify-content-center">
                {{ $payments->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
