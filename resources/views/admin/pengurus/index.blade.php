<x-app-layout>
    <x-slot name="title">Manajemen Pengurus</x-slot>

    <div class="d-flex justify-content-between align-items-center mb-3 animate-in">
        <h4 class="mb-0">Daftar Pengurus (Level 2)</h4>
        <a href="{{ route('admin.pengurus.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i> Tambah Pengurus
        </a>
    </div>

    <div class="table-wrapper animate-in">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staffs as $i => $s)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td><span class="fw-semibold">{{ $s->name }}</span></td>
                            <td>{{ $s->username }}</td>
                            <td><span class="badge bg-info text-dark">Pengurus</span></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.pengurus.edit', $s) }}" class="btn btn-sm btn-outline-primary"
                                        title="Edit / Ganti Password">
                                        <i class="bi bi-pencil"></i> Edit / Password
                                    </a>
                                    <form method="POST" action="{{ route('admin.pengurus.destroy', $s) }}"
                                        onsubmit="return confirm('Hapus pengurus ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="bi bi-people"></i>
                                    <h5>Belum ada pengurus</h5>
                                    <p>Silakan tambah akun pengurus baru</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>