<x-app-layout>
    <x-slot name="title">Detail Tagihan</x-slot>

    <div class="row g-4 animate-in">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><i class="bi bi-receipt me-2"></i> Detail Tagihan</div>
                <div class="card-body">
                    <div class="detail-row">
                        <div class="detail-label">Periode</div>
                        <div class="detail-value fw-semibold">{{ $bill->period }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Tagihan</div>
                        <div class="detail-value fw-bold text-primary">Rp {{ number_format($bill->amount, 0, ',', '.') }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Status</div>
                        <div class="detail-value">{!! $bill->status_badge !!}</div>
                    </div>
                </div>
            </div>

            @if($bill->status === 'unpaid')
                <div class="mt-3">
                    <a href="{{ route('resident.payments.create', $bill) }}" class="btn btn-primary">
                        <i class="bi bi-credit-card me-1"></i> Bayar Sekarang
                    </a>
                </div>
            @endif
        </div>

        <!-- Payment History for this Bill -->
        <div class="col-lg-6">
            <div class="table-wrapper">
                <div class="card-header"><i class="bi bi-clock-history me-2"></i> Riwayat Pembayaran</div>
                @if($bill->payments->isNotEmpty())
                    <div class="card-body p-0">
                        @foreach($bill->payments as $payment)
                            <div class="p-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <div class="fw-semibold" style="font-size:0.875rem;">Rp {{ number_format($payment->amount_paid, 0, ',', '.') }}</div>
                                        <div class="text-muted" style="font-size:0.75rem;">{{ $payment->payment_date->format('d F Y') }}</div>
                                    </div>
                                    {!! $payment->status_badge !!}
                                </div>
                                @if($payment->notes)
                                    <div class="alert alert-danger py-1 px-2 mb-0" style="font-size:0.75rem;">
                                        <i class="bi bi-info-circle me-1"></i> {{ $payment->notes }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="card-body text-center text-muted">
                        Belum ada pembayaran
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('resident.bills.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</x-app-layout>
