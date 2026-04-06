<x-app-layout>
    <x-slot name="title">Kelola Berita / Rencana Kerja</x-slot>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Berita</h1>
        <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tulis Berita
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Judul</th>
                        <th>Anggaran</th>
                        <th>Status</th>
                        <th>Tanggal Terbit</th>
                        <th class="pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($announcements as $idx => $item)
                        <tr>
                            <td class="ps-4">{{ $announcements->firstItem() + $idx }}</td>
                            <td class="fw-semibold">{{ Str::limit($item->title, 50) }}</td>
                            <td>{{ $item->budget > 0 ? 'Rp ' . number_format($item->budget, 0, ',', '.') : '-' }}</td>
                            <td>
                                @if($item->published_at && $item->published_at <= now())
                                    <span class="badge bg-success">Diterbitkan</span>
                                @elseif($item->published_at && $item->published_at > now())
                                    <span class="badge bg-warning">Terjadwal</span>
                                @else
                                    <span class="badge bg-secondary">Draft</span>
                                @endif
                            </td>
                            <td>{{ $item->published_at ? $item->published_at->format('d/m/Y H:i') : '-' }}</td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('admin.announcements.edit', $item) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.announcements.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus berita ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada berita.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($announcements->hasPages())
            <div class="card-footer bg-white border-0 pb-0">
                {{ $announcements->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
