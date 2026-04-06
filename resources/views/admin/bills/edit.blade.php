<x-app-layout>
    <x-slot name="title">Edit Tagihan</x-slot>

    <div class="row justify-content-center animate-in">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-pencil me-2"></i> Edit Tagihan — {{ strtoupper($bill->resident->block_number) }}
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Warga</label>
                        <input type="text" class="form-control" value="{{ $bill->resident->name }} ({{ strtoupper($bill->resident->block_number) }})" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Periode</label>
                        <input type="text" class="form-control" value="{{ $bill->period }}" disabled>
                    </div>

                    <form method="POST" action="{{ route('admin.bills.update', $bill) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="amount" class="form-label fw-semibold">
                                Jumlah Tagihan (Rp) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number"
                                       class="form-control @error('amount') is-invalid @enderror"
                                       id="amount" name="amount"
                                       value="{{ old('amount', $bill->amount) }}"
                                       min="0" step="1000" required
                                       oninput="autoStatusCheck(this.value)">
                            </div>
                            <div class="form-text text-info" id="zeroHint" style="display:none;">
                                <i class="bi bi-info-circle me-1"></i>Nominal 0 akan otomatis ditandai <strong>Lunas</strong> (bebas iuran).
                            </div>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label fw-semibold">Status Tagihan <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="unpaid" {{ old('status', $bill->status->value) === 'unpaid' ? 'selected' : '' }}>
                                    ❌ Belum Bayar
                                </option>
                                <option value="pending" {{ old('status', $bill->status->value) === 'pending' ? 'selected' : '' }}>
                                    ⏳ Menunggu Konfirmasi
                                </option>
                                <option value="paid" {{ old('status', $bill->status->value) === 'paid' ? 'selected' : '' }}>
                                    ✅ Lunas
                                </option>
                            </select>
                            <div class="form-text">
                                Tandai <strong>Lunas</strong> jika warga sudah bayar manual (transfer via WA, dll).
                                System akan otomatis membuat catatan pembayaran.
                            </div>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Simpan
                            </button>
                            <a href="{{ route('admin.bills.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function autoStatusCheck(val) {
            const hint = document.getElementById('zeroHint');
            const statusSelect = document.getElementById('status');
            if (parseFloat(val) === 0) {
                hint.style.display = 'block';
                statusSelect.value = 'paid';
                statusSelect.style.pointerEvents = 'none';
                statusSelect.classList.add('bg-light');
            } else {
                hint.style.display = 'none';
                statusSelect.style.pointerEvents = 'auto';
                statusSelect.classList.remove('bg-light');
            }
        }
        // Run on page load in case of old() value
        autoStatusCheck(document.getElementById('amount').value);
    </script>
    @endpush
</x-app-layout>
