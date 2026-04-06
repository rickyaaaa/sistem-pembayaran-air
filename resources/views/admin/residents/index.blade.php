<x-app-layout>
    <x-slot name="title">Data Warga</x-slot>

    <div class="d-flex justify-content-between align-items-center mb-3 animate-in">
        <div></div>
        <a href="{{ route('admin.residents.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Tambah Warga
        </a>
    </div>

    <!-- Filter -->
    <form method="GET" class="filter-bar flex-wrap align-items-end mb-3 mt-2 gap-2" style="display:flex;">
        <div class="form-group mb-0">
            <label class="form-label mb-1" style="font-size:0.8rem;">Cari Blok / Nama</label>
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Nama / No. Blok" value="{{ request('search') }}">
        </div>
        <div class="form-group mb-0" style="flex:0 0 auto;">
            <label class="form-label mb-1" style="font-size:0.8rem;">Tahun Lunas</label>
            <input type="number" name="year" class="form-control form-control-sm" placeholder="Tahun" value="{{ request('year', now()->year) }}" style="width: 80px;">
        </div>
        <div class="form-group mb-0" style="flex:0 0 auto;">
            <label class="form-label mb-1" style="font-size:0.8rem;">Belum Lunas Di</label>
            <select name="unpaid_month" class="form-select form-select-sm" style="width: auto;">
                <option value="">Bebas (Semua)</option>
                @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $idx => $mName)
                    <option value="{{ $idx + 1 }}" {{ request('unpaid_month') == ($idx + 1) ? 'selected' : '' }}>
                        {{ $mName }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group mb-0" style="flex:0 0 auto;">
            <label class="form-label mb-1" style="font-size:0.8rem;">Status</label>
            <select name="status" class="form-select form-select-sm" style="width: auto;">
                <option value="">Semua</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
            </select>
        </div>
        <div class="form-group mb-0" style="flex:0 0 auto;">
            <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search me-1"></i>Filter</button>
            @if(request()->anyFilled(['search', 'unpaid_month', 'status']) || request('year') != now()->year)
                <a href="{{ route('admin.residents.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            @endif
        </div>
    </form>

    <div class="table-wrapper animate-in">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No. Blok</th>
                        <th>Nama</th>
                        <th>Blok</th>
                        <th>No. Rumah</th>
                        <th>Telepon</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($residents as $i => $resident)
                        <tr>
                            <td>{{ $residents->firstItem() + $i }}</td>
                            <td><span class="fw-semibold">{{ strtoupper($resident->block_number) }}</span></td>
                            <td>{{ $resident->name }}</td>
                            <td>{{ $resident->block }}</td>
                            <td>{{ $resident->house_number }}</td>
                            <td>{{ $resident->phone_number ?? '-' }}</td>
                            <td>
                                @if($resident->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Non-Aktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.residents.edit', $resident) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.residents.destroy', $resident) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <i class="bi bi-people"></i>
                                    <h5>Belum ada data warga</h5>
                                    <p>Silakan tambahkan warga baru</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($residents->hasPages())
            <div class="p-3 d-flex justify-content-center">
                {{ $residents->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
