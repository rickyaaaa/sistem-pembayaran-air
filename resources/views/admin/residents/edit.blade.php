<x-app-layout>
    <x-slot name="title">Edit Warga</x-slot>

    <div class="row justify-content-center animate-in">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-pencil me-2"></i> Edit Data Warga - {{ strtoupper($resident->block_number) }}
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.residents.update', $resident) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">No. Blok</label>
                            <input type="text" class="form-control" value="{{ strtoupper($resident->block_number) }}" disabled>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $resident->user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone_number" class="form-label">No. Telepon</label>
                            <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                                   id="phone_number" name="phone_number" value="{{ old('phone_number', $resident->phone_number) }}">
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="is_active" class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="is_active" id="is_active" class="form-select @error('is_active') is-invalid @enderror">
                                <option value="1" {{ old('is_active', $resident->is_active) == 1 ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ old('is_active', $resident->is_active) == 0 ? 'selected' : '' }}>Non-Aktif</option>
                            </select>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('admin.residents.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
