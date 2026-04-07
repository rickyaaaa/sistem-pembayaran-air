<x-app-layout>
    <x-slot name="title">Detail Pembayaran</x-slot>

    <div class="row g-4 animate-in">
        <!-- Payment Details -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle me-2"></i> Detail Pembayaran
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <div class="detail-label">No. Blok</div>
                        <div class="detail-value fw-semibold">{{ strtoupper($payment->resident->block_number) }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Nama Warga</div>
                        <div class="detail-value">{{ $payment->resident->name }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Periode</div>
                        <div class="detail-value">{{ $payment->bill->period }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Tagihan</div>
                        <div class="detail-value">Rp {{ number_format($payment->bill->amount, 0, ',', '.') }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Jumlah Dibayar</div>
                        <div class="detail-value fw-bold text-primary">Rp {{ number_format($payment->amount_paid, 0, ',', '.') }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Tgl. Pembayaran</div>
                        <div class="detail-value">{{ $payment->payment_date->format('d F Y') }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Status</div>
                        <div class="detail-value"><x-status-badge :status="$payment->status" /></div>
                    </div>
                    @if($payment->notes)
                        <div class="detail-row">
                            <div class="detail-label">Catatan</div>
                            <div class="detail-value text-danger">{{ $payment->notes }}</div>
                        </div>
                    @endif
                    @if($payment->confirmedBy)
                        <div class="detail-row">
                            <div class="detail-label">Dikonfirmasi oleh</div>
                            <div class="detail-value">{{ $payment->confirmedBy->name }} ({{ $payment->confirmed_at?->format('d/m/Y H:i') }})</div>
                        </div>
                    @endif
                    <div class="detail-row">
                        <div class="detail-label">Tgl. Submit</div>
                        <div class="detail-value">{{ $payment->created_at->format('d F Y, H:i') }}</div>
                    </div>
                    @if($payment->payer_name)
                        <div class="detail-row">
                            <div class="detail-label">Nama Penyetor</div>
                            <div class="detail-value fw-semibold">{{ $payment->payer_name }}</div>
                        </div>
                    @endif
                    @if($payment->payer_phone)
                        <div class="detail-row">
                            <div class="detail-label">No. HP Penyetor</div>
                            <div class="detail-value">{{ $payment->payer_phone }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            @if($payment->status->value === 'pending')
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="d-flex gap-2 mb-3">
                            <form method="POST" action="{{ route('admin.payments.confirm', $payment) }}" id="confirmPaymentForm">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-lg me-1"></i> Konfirmasi
                                </button>
                            </form>

                            <button type="button" class="btn btn-danger" data-bs-toggle="collapse" data-bs-target="#rejectForm">
                                <i class="bi bi-x-lg me-1"></i> Tolak
                            </button>
                        </div>

                        <div class="collapse" id="rejectForm">
                            <form method="POST" action="{{ route('admin.payments.reject', $payment) }}" id="rejectPaymentForm">
                                @csrf
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror"
                                              id="notes" name="notes" rows="3" required placeholder="Jelaskan alasan penolakan...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="bi bi-x-lg me-1"></i> Konfirmasi Tolak
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Proof Image -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-image me-2"></i> Bukti Pembayaran
                </div>
                <div class="card-body text-center">
                    @if($payment->proof_file)
                        @php
                            $ext = strtolower(pathinfo($payment->proof_file, PATHINFO_EXTENSION));
                        @endphp
                        @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                            <img src="{{ route('admin.payments.proof', $payment) }}" alt="Bukti Pembayaran" class="proof-preview">
                        @elseif($ext === 'pdf')
                            <div class="mb-3">
                                <i class="bi bi-file-earmark-pdf text-danger" style="font-size:3rem;"></i>
                                <p class="mt-2">File PDF</p>
                            </div>
                        @endif
                        <div class="mt-3">
                            <a href="{{ route('admin.payments.proof', $payment) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-download me-1"></i> Buka File
                            </a>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="bi bi-image"></i>
                            <p>Bukti tidak tersedia</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <a href="{{ route('admin.payments.edit', $payment) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>

        <form action="{{ route('admin.payments.destroy', $payment) }}" method="POST"
              onsubmit="return confirm('Apakah Anda yakin ingin menghapus pembayaran ini? Jika sudah dikonfirmasi, status tagihan akan kembali menjadi Belum Bayar.')"
              class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger">
                <i class="bi bi-trash me-1"></i> Hapus
            </button>
        </form>

        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>
</x-app-layout>
