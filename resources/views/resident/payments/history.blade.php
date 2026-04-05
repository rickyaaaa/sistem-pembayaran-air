<x-app-layout>
    <x-slot name="title">Riwayat Pembayaran</x-slot>

    <div class="table-wrapper animate-in">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Periode</th>
                        <th>Tgl Bayar</th>
                        <th class="text-end">Jumlah</th>
                        <th>Status</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $i => $payment)
                        <tr>
                            <td>{{ $payments->firstItem() + $i }}</td>
                            <td class="fw-semibold">{{ $payment->bill->period ?? '-' }}</td>
                            <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                            <td class="text-end">Rp {{ number_format($payment->amount_paid, 0, ',', '.') }}</td>
                            <td>{!! $payment->status_badge !!}</td>
                            <td>
                                @if($payment->notes)
                                    <span class="text-danger" style="font-size:0.8125rem;">{{ $payment->notes }}</span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="bi bi-clock-history"></i>
                                    <h5>Belum ada riwayat</h5>
                                    <p>Riwayat pembayaran akan muncul di sini</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
            <div class="p-3 d-flex justify-content-center">{{ $payments->links() }}</div>
        @endif
    </div>
</x-app-layout>
