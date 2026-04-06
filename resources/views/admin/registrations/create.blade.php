<x-app-layout>
    <x-slot name="title">Catat Pemasukan</x-slot>

    <div class="row justify-content-center animate-in">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><i class="bi bi-cash-coin me-2"></i> Form Pemasukan Dana</div>
                <div class="card-body px-4 py-4">
                    <form method="POST" action="{{ route('admin.registrations.store') }}" id="incomeForm">
                        @csrf

                        <div class="row align-items-center mb-4">
                            <label for="resident_id" class="col-sm-3 col-form-label text-muted fw-semibold">Pilih Blok:</label>
                            <div class="col-sm-9">
                                <select name="resident_id" id="resident_id"
                                        class="form-select @error('resident_id') is-invalid @enderror" onchange="updateResidentName(this)">
                                    <option value="" data-name="Warga akan terisi otomatis">-- Pilih Blok --</option>
                                    @foreach($residents as $resident)
                                        <option value="{{ $resident->id }}" data-name="{{ $resident->name }}" {{ old('resident_id') == $resident->id ? 'selected' : '' }}>
                                            {{ strtoupper($resident->block_number) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('resident_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        
                        <div class="row align-items-center mb-4">
                            <label class="col-sm-3 col-form-label text-muted fw-semibold">Nama Warga:</label>
                            <div class="col-sm-9">
                                <input type="text" id="resident_name" class="form-control bg-light text-muted" value="Warga akan terisi otomatis" readonly>
                            </div>
                        </div>

                        <div class="row align-items-center mb-4">
                            <label for="category" class="col-sm-3 col-form-label text-muted fw-semibold">Kategori:</label>
                            <div class="col-sm-9">
                                <select name="category" id="category" class="form-select @error('category') is-invalid @enderror"
                                        required onchange="toggleIuranFields(this.value)">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->value }}" {{ old('category', 'iuran') === $cat->value ? 'selected' : '' }}>
                                            {{ $cat->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div id="iuranFields" style="display: none;">
                            <div class="row align-items-center mb-4">
                                <label class="col-sm-3 col-form-label text-muted fw-semibold">Tahun Iuran:</label>
                                <div class="col-sm-9">
                                    <select name="iuran_year" class="form-select">
                                        @php
                                            $currentYear = date('Y');
                                        @endphp
                                        @for($y = $currentYear + 1; $y >= $currentYear - 3; $y--)
                                            <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <label class="col-sm-3 col-form-label text-muted fw-semibold">Periode (Iuran):</label>
                                <div class="col-sm-9">
                                    <div class="row g-3 mt-1">
                                        @php $monthsArr = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp
                                        @foreach($monthsArr as $m)
                                        <div class="col-md-4 col-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="months[]" value="{{ $m }}" id="month_{{ $loop->index }}">
                                                <label class="form-check-label text-muted" for="month_{{ $loop->index }}" style="font-size: .9rem;">
                                                    {{ $m }} <span class="year-label">{{ date('Y') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row align-items-center mb-4">
                            <label for="payment_date" class="col-sm-3 col-form-label text-muted fw-semibold">Tanggal Bayar:</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control @error('payment_date') is-invalid @enderror"
                                       id="payment_date" name="payment_date"
                                       value="{{ old('payment_date', now()->format('Y-m-d')) }}" required>
                                @error('payment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row align-items-center mb-4">
                            <label for="amount" class="col-sm-3 col-form-label text-muted fw-semibold">Jumlah:</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <span class="input-group-text bg-light">Rp</span>
                                    <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                           id="amount" name="amount" value="{{ old('amount') }}" min="0" required>
                                </div>
                                @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row align-items-center mb-5">
                            <label for="notes" class="col-sm-3 col-form-label text-muted fw-semibold">Catatan (opsional):</label>
                            <div class="col-sm-9">
                                <textarea class="form-control @error('notes') is-invalid @enderror"
                                          id="notes" name="notes" rows="2" placeholder="Catatan tambahan">{{ old('notes') }}</textarea>
                                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-3"></div>
                            <div class="col-sm-9">
                                <button type="submit" class="btn btn-primary px-4 py-2" style="background:#5c67f2; border:none;">Simpan</button>
                                <a href="{{ route('admin.registrations.index') }}" class="btn btn-light px-4 py-2 ms-2 text-muted fw-semibold">Batal</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function updateResidentName(select) {
            const selectedOption = select.options[select.selectedIndex];
            document.getElementById('resident_name').value = selectedOption.getAttribute('data-name') || 'Warga akan terisi otomatis';
        }

        function toggleIuranFields(category) {
            const iuranFields = document.getElementById('iuranFields');
            if (category === 'iuran') {
                iuranFields.style.display = 'block';
            } else {
                iuranFields.style.display = 'none';
            }
        }
        
        document.querySelector('select[name="iuran_year"]').addEventListener('change', function(e) {
            const yearStr = e.target.value;
            document.querySelectorAll('.year-label').forEach(el => el.textContent = yearStr);
        });

        // Run on load
        toggleIuranFields(document.getElementById('category').value);
        updateResidentName(document.getElementById('resident_id'));
    </script>
    @endpush
</x-app-layout>
