<x-app-layout>
    <x-slot name="title">Permintaan Perubahan</x-slot>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    
    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show">{{ session('info') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="table-wrapper animate-in">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Data</th>
                        <th>Diajukan Oleh</th>
                        <th>Perubahan</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($changeRequests as $i => $cr)
                        <tr>
                            <td>{{ $changeRequests->firstItem() + $i }}</td>
                            <td>
                                <span class="fw-semibold">{{ $cr->model_name }}</span>
                                <span class="text-muted" style="font-size:.8rem;">#{{ $cr->model_id }}</span>
                            </td>
                            <td>{{ $cr->requester->name }}</td>
                            <td style="font-size:.8rem;">
                                @foreach($cr->requested_data as $field => $value)
                                    <div>
                                        <span class="text-muted">{{ $field }}:</span>
                                        <span class="text-muted">{{ $cr->original_data[$field] ?? '-' }}</span>
                                        <i class="bi bi-arrow-right text-primary mx-1"></i>
                                        <strong class="text-primary">{{ $value }}</strong>
                                    </div>
                                @endforeach
                                @if($cr->reason)
                                    <div class="text-muted mt-1"><em>{{ $cr->reason }}</em></div>
                                @endif
                            </td>
                            <td>
                                @if($cr->status === 'pending')
                                    <span class="badge bg-warning text-dark">Menunggu</span>
                                @elseif($cr->status === 'approved')
                                    <span class="badge bg-success">Disetujui</span>
                                @else
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
                            </td>
                            <td style="font-size:.8rem;">{{ $cr->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($cr->isPending())
                                    <div class="d-flex gap-1">
                                        <form method="POST" action="{{ route('admin.change-requests.approve', $cr) }}"
                                              onsubmit="return confirm('Setujui perubahan ini?')">
                                            @csrf
                                            <button class="btn btn-sm btn-success" title="Setujui">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                        <button class="btn btn-sm btn-danger"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#reject-{{ $cr->id }}"
                                                title="Tolak">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                    <div class="collapse mt-2" id="reject-{{ $cr->id }}">
                                        <form method="POST" action="{{ route('admin.change-requests.reject', $cr) }}">
                                            @csrf
                                            <textarea name="review_notes" class="form-control form-control-sm mb-1"
                                                      rows="2" placeholder="Alasan penolakan..." required></textarea>
                                            <button type="submit" class="btn btn-danger btn-sm w-100">Kirim Penolakan</button>
                                        </form>
                                    </div>
                                @elseif($cr->status === 'approved')
                                    <small class="text-muted">Oleh {{ $cr->reviewer?->name }}<br>{{ $cr->reviewed_at?->format('d/m/Y') }}</small>
                                @else
                                    <small class="text-danger">{{ $cr->review_notes }}</small>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <h5>Tidak ada permintaan perubahan</h5>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($changeRequests->hasPages())
            <div class="p-3 d-flex justify-content-center">{{ $changeRequests->withQueryString()->links() }}</div>
        @endif
    </div>
</x-app-layout>
