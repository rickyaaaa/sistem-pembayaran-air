<x-public-layout>
    <x-slot name="title">Detail Tagihan</x-slot>

    <div class="mb-3">
        <a href="{{ route('resident.bills.index', ['house_number' => $houseNumber ?? '']) }}"
           class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Tagihan
        </a>
    </div>

    <div class="row g-4 animate-in">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius:14px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                    <h2 class="h6 fw-bold mb-0"><i class="bi bi-receipt me-2 text-primary"></i>Detail Tagihan</h2>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="text-muted" style="font-size:.75rem;">PERIODE</div>
                        <div class="fw-bold fs-5">{{ $bill->period }}</div>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="text-muted" style="font-size:.75rem;">JUMLAH TAGIHAN</div>
                        <div class="fw-bold fs-4 text-primary">Rp {{ number_format($bill->amount, 0, ',', '.') }}</div>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="text-muted" style="font-size:.75rem;">STATUS</div>
                        <div class="mt-1"><x-status-badge :status="$bill->status" /></div>
                    </div>
                    @if($bill->resident)
                        <div>
                            <div class="text-muted" style="font-size:.75rem;">NOMOR RUMAH</div>
                            <div class="fw-semibold">
                                Blok {{ $bill->resident->block }} No. {{ $bill->resident->house_number }}
                            </div>
                        </div>
                    @endif

                    @if($bill->status->value === 'unpaid')
                        <div class="mt-4">
                            <a href="{{ route('resident.payments.create', $bill) }}?house_number={{ urlencode($houseNumber ?? '') }}"
                               class="btn btn-primary">
                                <i class="bi bi-credit-card me-1"></i>Bayar Sekarang
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Payment History for this Bill --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius:14px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                    <h2 class="h6 fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Pembayaran</h2>
                </div>
                <div class="card-body p-0">
                    @if($bill->payments->isNotEmpty())
                        @foreach($bill->payments as $payment)
                            <div class="p-4 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <div class="fw-semibold">Rp {{ number_format($payment->amount_paid, 0, ',', '.') }}</div>
                                        <div class="text-muted" style="font-size:.8rem;">
                                            {{ $payment->payment_date->format('d F Y') }}
                                        </div>
                                    </div>
                                    <x-status-badge :status="$payment->status" />
                                </div>
                                @if($payment->notes)
                                    <div class="alert alert-danger py-1 px-2 mb-0" style="font-size:.78rem;">
                                        <i class="bi bi-info-circle me-1"></i>{{ $payment->notes }}
                                    </div>
                                @endif
                                @if($payment->proof_file)
                                    <span class="d-inline-flex align-items-center gap-1 mt-2 text-success" style="font-size:.78rem;">
                                        <i class="bi bi-check-circle-fill"></i>Bukti sudah dikirim
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-clock" style="font-size:2rem;opacity:.35;"></i>
                            <p class="mt-2 mb-0">Belum ada pembayaran</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-public-layout>
