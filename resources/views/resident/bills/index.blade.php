<x-public-layout>
    <x-slot name="title">Cek Tagihan</x-slot>

    {{-- ===== Search Bar ===== --}}
    <div class="card border-0 shadow-sm mb-4 animate-in" style="border-radius:14px;overflow:hidden;">
        <div class="card-body p-4" style="background:linear-gradient(135deg,#1d4ed8,#0891b2);color:#fff;">
            <div class="d-flex align-items-start justify-content-between mb-3">
                <div>
                    <h1 class="h5 fw-bold mb-1"><i class="bi bi-receipt me-2"></i>Cek Tagihan Air</h1>
                    <p class="opacity-75 mb-0" style="font-size:.875rem;">Masukkan nomor rumah Anda untuk melihat tagihan</p>
                </div>
                <a href="{{ route('resident.dashboard') }}" class="btn btn-sm btn-outline-light text-white d-flex align-items-center gap-1 flex-shrink-0">
                    <i class="bi bi-arrow-left"></i>
                    <span class="d-none d-sm-inline">Beranda</span>
                </a>
            </div>

            <form method="GET" action="{{ route('resident.bills.index') }}" class="d-flex gap-2 flex-wrap">
                <div class="input-group" style="max-width:440px;">
                    <span class="input-group-text bg-white text-primary border-0 fw-semibold">
                        <i class="bi bi-house-door me-1"></i>No. Rumah
                    </span>
                    <input type="text" name="house_number" class="form-control border-0"
                           placeholder="Contoh: A3, C18, D1"
                           value="{{ $houseNumber ?? '' }}"
                           autocomplete="off" required>
                    <button type="submit" class="btn btn-warning fw-semibold px-4">
                        <i class="bi bi-search me-1"></i>Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($searched)
        @if(!$resident)
            {{-- Not Found --}}
            <div class="card border-0 shadow-sm animate-in text-center py-5" style="border-radius:14px;">
                <div class="card-body">
                    <i class="bi bi-house-x" style="font-size:3.5rem;color:#94a3b8;"></i>
                    <h2 class="h5 mt-3 fw-bold text-dark">Nomor Rumah Tidak Ditemukan</h2>
                    <p class="text-muted">Nomor rumah <strong>"{{ $houseNumber }}"</strong> tidak terdaftar atau tidak aktif.<br>
                       Silakan periksa kembali nomor rumah Anda.</p>
                    <a href="{{ route('resident.bills.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left me-1"></i>Cari Lagi
                    </a>
                </div>
            </div>
        @else
            {{-- Resident Info Badge --}}
            <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-white rounded-3 shadow-sm animate-in">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:46px;height:46px;background:#eff6ff;">
                    <i class="bi bi-house-door-fill text-primary" style="font-size:1.3rem;"></i>
                </div>
                <div>
                    <div class="fw-bold" style="font-size:.9375rem;">
                        Blok {{ $resident->block }} No. {{ $resident->house_number }}
                        @if($resident->block_number)
                            <span class="text-muted fw-normal">({{ strtoupper($resident->block_number) }})</span>
                        @endif
                    </div>
                    <div class="text-muted" style="font-size:.8rem;">
                        <i class="bi bi-person me-1"></i>{{ $resident->name ?? '-' }}
                        @if($resident->phone_number)
                            &bull; <i class="bi bi-telephone me-1"></i>{{ $resident->phone_number }}
                        @endif
                    </div>
                </div>

                {{-- Filters inline --}}
                <form method="GET" action="{{ route('resident.bills.index') }}"
                      class="d-flex gap-2 ms-auto align-items-center flex-wrap">
                    <input type="hidden" name="house_number" value="{{ $houseNumber }}">
                    <select name="year" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                        <option value="">Semua Tahun</option>
                        @foreach($availableYears as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="unpaid"  {{ request('status') === 'unpaid'  ? 'selected' : '' }}>Belum Bayar</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="paid"    {{ request('status') === 'paid'    ? 'selected' : '' }}>Lunas</option>
                    </select>
                </form>
            </div>

            {{-- Bills Table --}}
            <div class="card border-0 shadow-sm animate-in" style="border-radius:14px;">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background:#f8fafc;">
                            <tr>
                                <th class="px-4 py-3">Periode</th>
                                <th class="text-end py-3">Jumlah Tagihan</th>
                                <th class="py-3">Status</th>
                                <th class="py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bills as $bill)
                                <tr>
                                    <td class="fw-semibold px-4 py-3">{{ $bill->period }}</td>
                                    <td class="text-end py-3">Rp {{ number_format($bill->amount, 0, ',', '.') }}</td>
                                    <td class="py-3"><x-status-badge :status="$bill->status" /></td>
                                    <td class="py-3">
                                        @if($bill->status->value === 'unpaid')
                                            <a href="{{ route('resident.payments.create', $bill) }}?house_number={{ urlencode($houseNumber ?? '') }}"
                                               class="btn btn-sm btn-primary">
                                                <i class="bi bi-credit-card me-1"></i>Bayar
                                            </a>
                                        @else
                                            <a href="{{ route('resident.bills.show', $bill) }}?house_number={{ urlencode($houseNumber ?? '') }}"
                                               class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-eye me-1"></i>Detail
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox" style="font-size:2.5rem;opacity:.4;"></i>
                                        <p class="mt-2 mb-0">Tidak ada tagihan ditemukan</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($bills instanceof \Illuminate\Pagination\LengthAwarePaginator && $bills->hasPages())
                    <div class="card-footer bg-white border-0 d-flex justify-content-center py-3">
                        {{ $bills->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        @endif
    @else
        {{-- Initial state before search --}}
        <div class="card border-0 shadow-sm text-center py-5 animate-in" style="border-radius:14px;">
            <div class="card-body">
                <i class="bi bi-search" style="font-size:3rem;color:#94a3b8;"></i>
                <h2 class="h5 mt-3 fw-semibold text-muted">Masukkan nomor rumah untuk mulai pencarian</h2>
                <p class="text-muted" style="font-size:.875rem;">Contoh: A3, C18, D1</p>
            </div>
        </div>
    @endif
</x-public-layout>
