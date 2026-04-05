<x-public-layout>
    <x-slot name="title">Bayar Tagihan</x-slot>

    <div class="mb-3">
        <a href="{{ route('resident.bills.index', ['house_number' => $bill->resident->house_number ?? '']) }}"
           class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Tagihan
        </a>
    </div>

    <div class="row justify-content-center animate-in">
        <div class="col-lg-6">
            {{-- Bill Summary Card --}}
            <div class="card mb-3 border-0"
                 style="background:linear-gradient(135deg,#1d4ed8,#0891b2);color:#fff;border-radius:14px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <i class="bi bi-house-door-fill" style="font-size:1.4rem;opacity:.8;"></i>
                        <div style="font-size:.875rem;opacity:.85;">
                            @if($bill->resident)
                                Blok {{ $bill->resident->block }} No. {{ $bill->resident->house_number }}
                            @endif
                        </div>
                    </div>
                    <h2 class="h6 opacity-75 mb-1">Tagihan {{ $bill->period }}</h2>
                    <div class="display-6 fw-bold">Rp {{ number_format($bill->amount, 0, ',', '.') }}</div>
                </div>
            </div>

            {{-- Payment Form --}}
            <div class="card border-0 shadow-sm" style="border-radius:14px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                    <h2 class="h6 fw-bold mb-0"><i class="bi bi-credit-card me-2 text-primary"></i>Form Pembayaran</h2>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="alert alert-info py-2 mb-4" style="font-size:.8125rem;">
                        <i class="bi bi-info-circle me-1"></i>
                        Lakukan transfer ke rekening SAB Springville, lalu upload bukti transfer di bawah ini.
                    </div>

                    <form method="POST" action="{{ route('resident.payments.store', $bill) }}"
                          enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="payment_date" class="form-label fw-semibold">
                                Tanggal Pembayaran <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                   class="form-control @error('payment_date') is-invalid @enderror"
                                   id="payment_date" name="payment_date"
                                   value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                                   max="{{ now()->format('Y-m-d') }}" required>
                            @error('payment_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="amount_paid" class="form-label fw-semibold">
                                Jumlah yang Dibayar (Rp) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number"
                                       class="form-control @error('amount_paid') is-invalid @enderror"
                                       id="amount_paid" name="amount_paid"
                                       value="{{ old('amount_paid', $bill->amount) }}"
                                       min="1" required>
                                @error('amount_paid')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="proof_file" class="form-label fw-semibold">
                                Bukti Transfer <span class="text-danger">*</span>
                            </label>
                            <input type="file"
                                   class="form-control @error('proof_file') is-invalid @enderror"
                                   id="proof_file" name="proof_file"
                                   accept=".jpg,.jpeg,.png,.pdf" required>
                            <div class="form-text">Format: JPG, PNG, atau PDF. Maksimal 2MB.</div>
                            @error('proof_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Preview --}}
                        <div class="mb-3" id="previewContainer" style="display:none;">
                            <img id="imagePreview" src="" alt="Preview"
                                 class="img-fluid rounded-2 border" style="max-height:200px;">
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary"
                                    onclick="return confirm('Kirim bukti pembayaran?')">
                                <i class="bi bi-send me-1"></i>Kirim Pembayaran
                            </button>
                            <a href="{{ route('resident.bills.index', ['house_number' => $bill->resident->house_number ?? '']) }}"
                               class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('proof_file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('previewContainer');
            const img = document.getElementById('imagePreview');

            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });
    </script>
    @endpush
</x-public-layout>
