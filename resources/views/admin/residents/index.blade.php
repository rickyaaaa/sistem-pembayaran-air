<x-app-layout>
    <x-slot name="title">Data Warga</x-slot>

    <div class="d-flex justify-content-between align-items-center mb-3 animate-in">
        <div></div>
        <a href="{{ route('admin.residents.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Tambah Warga
        </a>
    </div>

    <!-- Filter -->
    <form method="GET" class="filter-bar">
        <div class="form-group">
            <label class="form-label">Cari</label>
            <input type="text" name="search" class="form-control" placeholder="Nama / No. Blok" value="{{ request('search') }}">
        </div>
        <div class="form-group" style="flex:0 0 auto;">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">Semua</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
            </select>
        </div>
        <div style="flex:0 0 auto;">
            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
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
