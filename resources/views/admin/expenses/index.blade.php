<x-app-layout>
    <x-slot name="title">Pengeluaran</x-slot>

    <div class="d-flex justify-content-between align-items-center mb-3 animate-in">
        <div></div>
        <a href="{{ route('admin.expenses.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Tambah Pengeluaran
        </a>
    </div>

    <form method="GET" class="filter-bar">
        <div class="form-group" style="flex:0 0 auto;">
            <label class="form-label">Tahun</label>
            <select name="year" class="form-select" onchange="this.form.submit()">
                <option value="">Semua</option>
                @for($y = now()->year; $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="form-group" style="flex:0 0 auto;">
            <label class="form-label">Kategori</label>
            <select name="category" class="form-select" onchange="this.form.submit()">
                <option value="">Semua</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
        </div>
    </form>

    <div class="table-wrapper animate-in">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Deskripsi</th>
                        <th>Kategori</th>
                        <th class="text-end">Jumlah</th>
                        <th>Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $i => $expense)
                        <tr>
                            <td>{{ $expenses->firstItem() + $i }}</td>
                            <td>{{ $expense->date->format('d/m/Y') }}</td>
                            <td>{{ $expense->description }}</td>
                            <td><span class="badge bg-secondary">{{ $expense->category }}</span></td>
                            <td class="text-end fw-semibold">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                            <td>{{ $expense->creator->name }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.expenses.edit', $expense) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                    <form method="POST" action="{{ route('admin.expenses.destroy', $expense) }}" onsubmit="return confirm('Hapus pengeluaran ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="bi bi-cash-stack"></i>
                                    <h5>Belum ada pengeluaran</h5>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($expenses->hasPages())
            <div class="p-3 d-flex justify-content-center">{{ $expenses->withQueryString()->links() }}</div>
        @endif
    </div>
</x-app-layout>
