<x-app-layout>
    <x-slot name="title">Tagihan</x-slot>

    <div class="d-flex justify-content-between align-items-center mb-3 animate-in">
        <div></div>
        <a href="{{ route('admin.bills.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Buat Tagihan
        </a>
    </div>

    <!-- Filter -->
    <form method="GET" class="filter-bar">
        <div class="form-group">
            <label class="form-label">Cari</label>
            <input type="text" name="search" class="form-control" placeholder="Nama / No. Blok" value="{{ request('search') }}">
        </div>
        <div class="form-group" style="flex:0 0 auto;">
            <label class="form-label">Blok</label>
            <select name="block" class="form-select">
                <option value="">Semua Blok</option>
                @foreach($availableBlocks as $blk)
                    <option value="{{ $blk }}" {{ request('block') === $blk ? 'selected' : '' }}>{{ $blk }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="flex:0 0 auto;">
            <label class="form-label">Bulan</label>
            <select name="month" class="form-select">
                <option value="">Semua</option>
                @php $months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ $months[$m-1] }}</option>
                @endfor
            </select>
        </div>
        <div class="form-group" style="flex:0 0 auto;">
            <label class="form-label">Tahun</label>
            <select name="year" class="form-select">
                <option value="">Semua</option>
                @foreach($availableYears as $y)
                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="flex:0 0 auto;">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">Semua</option>
                <option value="unpaid"  {{ request('status') === 'unpaid'  ? 'selected' : '' }}>Belum Bayar</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                <option value="paid"    {{ request('status') === 'paid'    ? 'selected' : '' }}>Lunas</option>
            </select>
        </div>
        <div style="flex:0 0 auto;">
            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
            <a href="{{ route('admin.bills.index') }}" class="btn btn-outline-secondary ms-1" title="Reset filter"><i class="bi bi-x-lg"></i></a>
        </div>
    </form>

    {{-- Summary bar --}}
    @if(request()->hasAny(['month', 'year', 'block', 'status']))
        <div class="d-flex gap-3 mb-3 align-items-center" style="font-size:.85rem;">
            <span class="text-muted">Menampilkan {{ $bills->total() }} tagihan</span>
            @php
                $unpaidCount = $bills->getCollection()->where('status.value', 'unpaid')->count();
            @endphp
            @if($unpaidCount > 0)
                <span class="badge bg-danger">{{ $unpaidCount }} belum bayar (halaman ini)</span>
            @endif
        </div>
    @endif

    <div class="table-wrapper animate-in">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No. Blok</th>
                        <th>Nama</th>
                        <th>Periode</th>
                        <th class="text-end">Jumlah</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bills as $i => $bill)
                        <tr class="{{ $bill->status->value === 'unpaid' ? 'table-danger bg-opacity-25' : '' }}">
                            <td>{{ $bills->firstItem() + $i }}</td>
                            <td><span class="fw-semibold">{{ strtoupper($bill->resident->block_number) }}</span></td>
                            <td>{{ $bill->resident->name }}</td>
                            <td>{{ $bill->period }}</td>
                            <td class="text-end">
                                @if($bill->amount == 0)
                                    <span class="text-muted">Bebas Iuran</span>
                                @else
                                    Rp {{ number_format($bill->amount, 0, ',', '.') }}
                                @endif
                            </td>
                            <td><x-status-badge :status="$bill->status" /></td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    @if($bill->status->value !== 'paid')

                                        <a href="{{ route('admin.bills.edit', $bill) }}"
                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.bills.destroy', $bill) }}"
                                              onsubmit="return confirm('Hapus tagihan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted" style="font-size:0.75rem;">Lunas</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="bi bi-receipt"></i>
                                    <h5>Belum ada tagihan</h5>
                                    <p>Buat tagihan baru untuk warga</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($bills->hasPages())
            <div class="p-3 d-flex justify-content-center">
                {{ $bills->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
