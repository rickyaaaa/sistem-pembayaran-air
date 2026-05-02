<x-app-layout>
    <x-slot name="title">Buat Tagihan</x-slot>

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <style>
        .ts-control { padding: 0.375rem 0.75rem; border-color: #dee2e6; }
    </style>
    @endpush

    <div class="row justify-content-center animate-in">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-receipt me-2"></i> Form Buat Tagihan
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.bills.store') }}" id="billForm">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Tipe Pembuatan <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="typeBulk" value="bulk" {{ old('type') === 'bulk' ? 'checked' : '' }} onchange="toggleResidentSelect()">
                                    <label class="form-check-label" for="typeBulk">Semua Warga Aktif</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="typeSingle" value="single" {{ old('type', 'single') === 'single' ? 'checked' : '' }} onchange="toggleResidentSelect()">
                                    <label class="form-check-label" for="typeSingle">Per Warga</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3" id="residentSelect" style="display: {{ old('type', 'single') === 'single' ? 'block' : 'none' }};">
                            <label for="resident_id" class="form-label">Pilih Warga <span class="text-danger">*</span></label>
                            <select name="resident_id" id="resident_id" class="form-select @error('resident_id') is-invalid @enderror">
                                <option value="">-- Pilih Warga --</option>
                                @foreach($residents as $resident)
                                    <option value="{{ $resident->id }}" {{ old('resident_id') == $resident->id ? 'selected' : '' }}>
                                        {{ strtoupper($resident->block_number) }} - {{ $resident->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('resident_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label for="month" class="form-label">Bulan <span class="text-danger">*</span></label>
                                <select name="month" id="month" class="form-select @error('month') is-invalid @enderror">
                                    @php $months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ old('month', now()->month) == $m ? 'selected' : '' }}>{{ $months[$m-1] }}</option>
                                    @endfor
                                </select>
                                @error('month')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="year" class="form-label">Tahun <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('year') is-invalid @enderror"
                                       id="year" name="year" value="{{ old('year', now()->year) }}" min="2020" max="2099" required>
                                @error('year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Jumlah Tagihan (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                       id="amount" name="amount" value="{{ old('amount') }}" min="0" step="1000" required>
                            </div>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Buat Tagihan
                            </button>
                            <a href="{{ route('admin.bills.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new TomSelect("#resident_id", {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                }
            });
        });

        function toggleResidentSelect() {
            const isSingle = document.getElementById('typeSingle').checked;
            document.getElementById('residentSelect').style.display = isSingle ? 'block' : 'none';
        }
    </script>
    @endpush
</x-app-layout>
