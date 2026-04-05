<x-app-layout>
    <x-slot name="title">Catat Pendaftaran</x-slot>

    <div class="row justify-content-center animate-in">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><i class="bi bi-person-plus me-2"></i> Form Iuran Pendaftaran</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.registrations.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="resident_id" class="form-label">Warga <span class="text-danger">*</span></label>
                            <select name="resident_id" id="resident_id" class="form-select @error('resident_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Warga --</option>
                                @foreach($residents as $resident)
                                    <option value="{{ $resident->id }}" {{ old('resident_id') == $resident->id ? 'selected' : '' }}>
                                        {{ strtoupper($resident->block_number) }} - {{ $resident->user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('resident_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="payment_date" class="form-label">Tanggal Bayar <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('payment_date') is-invalid @enderror" id="payment_date" name="payment_date" value="{{ old('payment_date', now()->format('Y-m-d')) }}" required>
                            @error('payment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Jumlah (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount') }}" min="0" required>
                            </div>
                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Simpan</button>
                            <a href="{{ route('admin.registrations.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
