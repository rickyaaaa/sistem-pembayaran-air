<x-app-layout>
    <x-slot name="title">Edit Pengeluaran</x-slot>

    <div class="row justify-content-center animate-in">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><i class="bi bi-pencil me-2"></i> Edit Pengeluaran</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.expenses.update', $expense) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="date" class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', $expense->date->format('Y-m-d')) }}" required>
                            @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Jumlah (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', $expense->amount) }}" min="0" required>
                            </div>
                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="2" required>{{ old('description', $expense->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">Kategori <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('category') is-invalid @enderror" id="category" name="category" value="{{ old('category', $expense->category) }}" list="categoryList" required>
                            <datalist id="categoryList">
                                <option value="Perawatan">
                                <option value="Operasional">
                                <option value="Perbaikan">
                                <option value="Material">
                                <option value="Lainnya">
                            </datalist>
                            @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="proof_file" class="form-label">Bukti (opsional)</label>
                            @if($expense->proof_file)
                                <div class="mb-2">
                                    <span class="badge bg-info"><i class="bi bi-file-earmark me-1"></i>File saat ini ada</span>
                                </div>
                            @endif
                            <input type="file" class="form-control @error('proof_file') is-invalid @enderror" id="proof_file" name="proof_file" accept=".jpg,.jpeg,.png,.pdf">
                            <div class="form-text">Upload file baru untuk mengganti. Format: JPG, PNG, PDF. Maks 2MB.</div>
                            @error('proof_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        @if(auth()->user()->isPengurus())
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
                            <a href="{{ route('admin.expenses.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
