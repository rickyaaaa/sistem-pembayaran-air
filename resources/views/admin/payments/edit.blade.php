<x-app-layout>
    <x-slot name="title">Edit Pembayaran</x-slot>

    <div class="row justify-content-center animate-in">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-pencil me-2"></i> Edit Pembayaran
                </div>
                <div class="card-body">
                    <!-- Info Badge -->
                    <div class="alert alert-info py-2" style="font-size:0.85rem;">
                        <strong>ID:</strong> #{{ $payment->id }} &nbsp;|&nbsp; 
                        <strong>Warga:</strong> {{ $payment->resident->name }} (Blok {{ strtoupper($payment->resident->block_number) }}) &nbsp;|&nbsp; 
                        <strong>Status:</strong> <span class="badge {{ $payment->status->badgeClass() }}">{{ $payment->status->label() }}</span>
                    </div>

                    <form method="POST" action="{{ route('admin.payments.update', $payment) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="payment_date" class="form-label">Tanggal Pembayaran <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('payment_date') is-invalid @enderror" id="payment_date" name="payment_date" value="{{ old('payment_date', $payment->payment_date->format('Y-m-d')) }}" required>
                            @error('payment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="amount_paid" class="form-label">Jumlah Dibayar (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('amount_paid') is-invalid @enderror" id="amount_paid" name="amount_paid" value="{{ old('amount_paid', $payment->amount_paid) }}" min="0" step="0.01" required>
                            </div>
                            @error('amount_paid')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="payer_name" class="form-label">Nama Pengirim</label>
                            <input type="text" class="form-control @error('payer_name') is-invalid @enderror" id="payer_name" name="payer_name" value="{{ old('payer_name', $payment->payer_name) }}" placeholder="Contoh: Budi Santoso">
                            @error('payer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="payer_phone" class="form-label">No. HP Pengirim</label>
                            <input type="text" class="form-control @error('payer_phone') is-invalid @enderror" id="payer_phone" name="payer_phone" value="{{ old('payer_phone', $payment->payer_phone) }}" placeholder="08xxxxxxxxxx">
                            @error('payer_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2" placeholder="Tambahkan catatan jika diperlukan...">{{ old('notes', $payment->notes) }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="proof_file" class="form-label">Bukti (opsional)</label>
                            @if($payment->proof_file && $payment->proof_file !== 'manual')
                                <div class="mb-2 d-flex align-items-center gap-2">
                                    <span class="badge bg-info"><i class="bi bi-file-earmark me-1"></i>File saat ini ada</span>
                                    <a href="{{ route('admin.payments.proof', basename($payment->proof_file)) }}" target="_blank" class="btn btn-sm btn-outline-primary" style="padding: 0.1rem 0.5rem; font-size: 0.8rem;">
                                        <i class="bi bi-eye me-1"></i>Lihat Berkas
                                    </a>
                                </div>
                            @endif
                            <input type="file" class="form-control @error('proof_file') is-invalid @enderror" id="proof_file" name="proof_file" accept=".jpg,.jpeg,.png,.pdf">
                            <div class="form-text">Upload file baru untuk mengganti. Format: JPG, PNG, PDF. Maks 2MB.</div>
                            @error('proof_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        @if(auth()->check() && auth()->user()->isPengurus())
                            <div class="alert alert-info py-2" style="font-size:.85rem;">
                                <i class="bi bi-info-circle me-1"></i>
                                Sebagai Pengurus, perubahan ini akan dikirim sebagai <strong>permintaan persetujuan</strong> ke Admin.
                            </div>
                            <div class="mb-3">
                                <label for="reason" class="form-label fw-semibold">Alasan Perubahan</label>
                                <input type="text" name="reason" class="form-control @error('reason') is-invalid @enderror"
                                       placeholder="Contoh: Salah input nominal" required>
                                @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        @endif

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Simpan</button>
                            <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>

            @if($payment->proof_file && $payment->proof_file !== 'manual')
                <div class="card mt-4">
                    <div class="card-header"><i class="bi bi-image me-2"></i> Bukti Pembayaran Terlampir</div>
                    <div class="card-body text-center bg-light">
                        <img src="{{ route('admin.payments.proof', basename($payment->proof_file)) }}" alt="Bukti" class="img-fluid rounded shadow-sm" style="max-height: 300px;">
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
