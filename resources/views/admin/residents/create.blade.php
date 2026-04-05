<x-app-layout>
    <x-slot name="title">Tambah Warga</x-slot>

    <div class="row justify-content-center animate-in">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-person-plus me-2"></i> Form Tambah Warga
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.residents.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label for="block" class="form-label">Blok <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('block') is-invalid @enderror"
                                       id="block" name="block" value="{{ old('block') }}" placeholder="Contoh: A" required>
                                @error('block')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="house_number" class="form-label">No. Rumah <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('house_number') is-invalid @enderror"
                                       id="house_number" name="house_number" value="{{ old('house_number') }}" placeholder="Contoh: 1" required>
                                @error('house_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        @error('block_number')
                            <div class="alert alert-danger py-2" style="font-size:0.8125rem;">
                                <i class="bi bi-exclamation-triangle me-1"></i> {{ $message }}
                            </div>
                        @enderror

                        <div class="mb-3">
                            <label for="phone_number" class="form-label">No. Telepon</label>
                            <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                                   id="phone_number" name="phone_number" value="{{ old('phone_number') }}">
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info py-2" style="font-size:0.8125rem;">
                            <i class="bi bi-info-circle me-1"></i>
                            Username dan password default akan otomatis dibuat dari nomor blok (contoh: <strong>a1</strong>).
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Simpan
                            </button>
                            <a href="{{ route('admin.residents.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
