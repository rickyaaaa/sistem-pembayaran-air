<x-app-layout>
    <x-slot name="title">Iuran Pendaftaran</x-slot>

    <div class="d-flex justify-content-between align-items-center mb-3 animate-in">
        <div></div>
        <a href="{{ route('admin.registrations.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Pemasukan
        </a>
    </div>

    <!-- Filter -->
    <form method="GET" class="filter-bar mb-3">
        <div class="d-flex gap-2 align-items-end">
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
                <label class="form-label">Kategori</label>
                <select name="category" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->value }}" {{ request('category') === $cat->value ? 'selected' : '' }}>
                            {{ $cat->label() }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="flex:0 0 auto;">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                <a href="{{ route('admin.registrations.index') }}" class="btn btn-outline-secondary ms-1"><i class="bi bi-x-lg"></i></a>
            </div>
        </div>
    </form>

    <div class="table-wrapper animate-in">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kategori</th>
                        <th>No. Blok</th>
                        <th>Nama</th>
                        <th>Tgl. Bayar</th>
                        <th class="text-end">Jumlah</th>
                        <th>Catatan</th>
                        <th>Dicatat Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($registrations as $i => $reg)
                        <tr>
                            <td>{{ $registrations->firstItem() + $i }}</td>
                            <td>
                                <span class="badge {{ $reg->category->badgeClass() }}">
                                    {{ $reg->category->label() }}
                                </span>
                            </td>
                            <td><span class="fw-semibold">{{ $reg->resident ? strtoupper($reg->resident->block_number) : '-' }}</span></td>
                            <td>{{ $reg->resident->name ?? '-' }}</td>
                            <td>{{ $reg->payment_date->format('d/m/Y') }}</td>
                            <td class="text-end fw-semibold">Rp {{ number_format($reg->amount, 0, ',', '.') }}</td>
                            <td>{{ $reg->notes ?? '-' }}</td>
                            <td>{{ $reg->creator->name }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.registrations.destroy', $reg) }}" onsubmit="return confirm('Hapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <i class="bi bi-person-plus"></i>
                                    <h5>Belum ada data pendaftaran</h5>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($registrations->hasPages())
            <div class="p-3 d-flex justify-content-center">{{ $registrations->withQueryString()->links() }}</div>
        @endif
    </div>
</x-app-layout>
