<x-app-layout>
    <x-slot name="title">Edit Tagihan</x-slot>

    <div class="row justify-content-center animate-in">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-pencil me-2"></i> Edit Tagihan - {{ strtoupper($bill->resident->block_number) }}
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Warga</label>
                        <input type="text" class="form-control" value="{{ $bill->resident->user->name }} ({{ strtoupper($bill->resident->block_number) }})" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Periode</label>
                        <input type="text" class="form-control" value="{{ $bill->period }}" disabled>
                    </div>

                    <form method="POST" action="{{ route('admin.bills.update', $bill) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="amount" class="form-label">Jumlah Tagihan (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                       id="amount" name="amount" value="{{ old('amount', $bill->amount) }}" min="0" step="1000" required>
                            </div>
                            @error('amount')
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
</x-app-layout>
