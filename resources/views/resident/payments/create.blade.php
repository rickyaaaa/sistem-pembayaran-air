<x-app-layout>
    <x-slot name="title">Bayar Tagihan</x-slot>

    <div class="row justify-content-center animate-in">
        <div class="col-lg-6">
            <!-- Bill Summary -->
            <div class="card mb-3" style="background: linear-gradient(135deg, #2563eb, #0ea5e9); color: white; border: none;">
                <div class="card-body">
                    <h6 class="opacity-75 mb-1">Tagihan {{ $bill->period }}</h6>
                    <div class="display-6 fw-bold">Rp {{ number_format($bill->amount, 0, ',', '.') }}</div>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="card">
                <div class="card-header"><i class="bi bi-credit-card me-2"></i> Form Pembayaran</div>
                <div class="card-body">
                    <div class="alert alert-info py-2 mb-3" style="font-size:0.8125rem;">
                        <i class="bi bi-info-circle me-1"></i>
                        Lakukan transfer ke rekening SAB Springville, lalu upload bukti transfer di bawah ini.
                    </div>

                    <form method="POST" action="{{ route('resident.payments.store', $bill) }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="payment_date" class="form-label">Tanggal Pembayaran <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('payment_date') is-invalid @enderror"
                                   id="payment_date" name="payment_date"
                                   value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                                   max="{{ now()->format('Y-m-d') }}" required>
                            @error('payment_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="amount_paid" class="form-label">Jumlah yang Dibayar (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('amount_paid') is-invalid @enderror"
                                       id="amount_paid" name="amount_paid"
                                       value="{{ old('amount_paid', $bill->amount) }}"
                                       min="1" required>
                            </div>
                            @error('amount_paid')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="proof_file" class="form-label">Bukti Transfer <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('proof_file') is-invalid @enderror"
                                   id="proof_file" name="proof_file"
                                   accept=".jpg,.jpeg,.png,.pdf" required>
                            <div class="form-text">Format: JPG, PNG, atau PDF. Maksimal 2MB.</div>
                            @error('proof_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Preview -->
                        <div class="mb-3" id="previewContainer" style="display:none;">
                            <img id="imagePreview" src="" alt="Preview" class="proof-preview">
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary" onclick="return confirm('Kirim bukti pembayaran?')">
                                <i class="bi bi-send me-1"></i> Kirim Pembayaran
                            </button>
                            <a href="{{ route('resident.bills.index') }}" class="btn btn-outline-secondary">Batal</a>
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
</x-app-layout>
