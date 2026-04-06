<x-app-layout>
    <x-slot name="title">Kelola Dokumen</x-slot>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Dokumen</h1>
        <a href="{{ route('admin.documents.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Dokumen
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
                        <th>Judul Dokumen</th>
                        <th>Diupload Oleh</th>
                        <th>Waktu</th>
                        <th class="pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $idx => $doc)
                        <tr>
                            <td class="ps-4">{{ $documents->firstItem() + $idx }}</td>
                            <td class="fw-semibold">{{ $doc->title }}</td>
                            <td>{{ $doc->creator->name ?? '-' }}</td>
                            <td>{{ $doc->created_at->format('d/m/Y H:i') }}</td>
                            <td class="pe-4 text-end">
                                <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-download"></i>
                                </a>
                                <form action="{{ route('admin.documents.destroy', $doc) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus dokumen ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada dokumen</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($documents->hasPages())
            <div class="card-footer bg-white border-0 pb-0">
                {{ $documents->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
