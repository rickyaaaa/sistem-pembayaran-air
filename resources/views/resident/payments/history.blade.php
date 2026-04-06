<x-public-layout>
    <x-slot name="title">Riwayat Pembayaran</x-slot>

    {{-- Search --}}
    <div class="card border-0 shadow-sm mb-4 animate-in"
         style="border-radius:14px;overflow:hidden;">
        <div class="card-body p-4" style="background:linear-gradient(135deg,#1d4ed8,#0891b2);color:#fff;">
            <h1 class="h5 fw-bold mb-1"><i class="bi bi-clock-history me-2"></i>Riwayat Pembayaran</h1>
            <p class="opacity-75 mb-3" style="font-size:.875rem;">Masukkan nomor rumah untuk melihat riwayat pembayaran</p>
            <form method="GET" action="{{ route('resident.payments.history') }}" class="d-flex gap-2 flex-wrap">
                <div class="input-group" style="max-width:380px;">
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

    @if($houseNumber)
        @if(!$resident)
            <div class="card border-0 shadow-sm text-center py-5 animate-in" style="border-radius:14px;">
                <div class="card-body">
                    <i class="bi bi-house-x" style="font-size:3.5rem;color:#94a3b8;"></i>
                    <h2 class="h5 mt-3 fw-bold">Nomor Rumah Tidak Ditemukan</h2>
                    <p class="text-muted">Nomor rumah <strong>"{{ $houseNumber }}"</strong> tidak terdaftar atau tidak aktif.</p>
                </div>
            </div>
        @else
            {{-- Resident info badge --}}
            <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-white rounded-3 shadow-sm animate-in">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:40px;height:40px;background:#eff6ff;">
                    <i class="bi bi-house-door-fill text-primary"></i>
                </div>
                <div>
                    <div class="fw-bold">Blok {{ $resident->block }} No. {{ $resident->house_number }}</div>
                    <div class="text-muted" style="font-size:.8rem;">{{ $resident->name ?? '-' }}</div>
                </div>
            </div>

            <div class="card border-0 shadow-sm animate-in" style="border-radius:14px;">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background:#f8fafc;">
                            <tr>
                                <th class="px-4 py-3">#</th>
                                <th class="py-3">Periode</th>
                                <th class="py-3">Tgl Bayar</th>
                                <th class="text-end py-3">Jumlah</th>
                                <th class="py-3">Status</th>
                                <th class="py-3">Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $i => $payment)
                                <tr>
                                    <td class="px-4 py-3">{{ $payments->firstItem() + $i }}</td>
                                    <td class="fw-semibold py-3">{{ $payment->bill->period ?? '-' }}</td>
                                    <td class="py-3">{{ $payment->payment_date->format('d/m/Y') }}</td>
                                    <td class="text-end py-3">Rp {{ number_format($payment->amount_paid, 0, ',', '.') }}</td>
                                    <td class="py-3"><x-status-badge :status="$payment->status" /></td>
                                    <td class="py-3">
                                        @if($payment->notes)
                                            <span class="text-danger" style="font-size:.8125rem;">{{ $payment->notes }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-clock-history" style="font-size:2.5rem;opacity:.4;"></i>
                                        <p class="mt-2 mb-0">Belum ada riwayat pembayaran</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($payments instanceof \Illuminate\Pagination\LengthAwarePaginator && $payments->hasPages())
                    <div class="card-footer bg-white border-0 d-flex justify-content-center py-3">
                        {{ $payments->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        @endif
    @else
        <div class="card border-0 shadow-sm text-center py-5 animate-in" style="border-radius:14px;">
            <div class="card-body">
                <i class="bi bi-search" style="font-size:3rem;color:#94a3b8;"></i>
                <h2 class="h5 mt-3 fw-semibold text-muted">Masukkan nomor rumah untuk melihat riwayat</h2>
            </div>
        </div>
    @endif
</x-public-layout>
