<x-public-layout>
    <x-slot name="title">Dokumen Organisasi</x-slot>

    <div class="card border-0 shadow-sm animate-in" style="border-radius:14px;">
        <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between py-3 px-4"
             style="border-radius:14px 14px 0 0;border-bottom:1px solid #f1f5f9;">
            <span class="fw-semibold" style="font-size:1rem;">
                <i class="bi bi-file-earmark-text me-2 text-primary"></i>Dokumen Organisasi
            </span>
        </div>
        <div class="card-body p-4">
            <div class="text-muted mb-4" style="font-size:0.9rem;">
                Berikut ini adalah dokumen-dokumen terbuka mengenai keorganisasian SAB Springville seperti Anggaran Dasar, AD/ART, dan SOP terkait.
            </div>

            <form method="GET" action="{{ route('resident.documents.index') }}" class="d-flex flex-wrap gap-3 mb-4 align-items-center bg-light p-3 rounded" style="border: 1px solid #f1f5f9;">
                <div class="d-flex align-items-center gap-2">
                    <label for="start_date" class="form-label mb-0 small text-muted text-nowrap"><i class="bi bi-calendar-event me-1"></i>Dari Tgl:</label>
                    <input type="date" name="start_date" id="start_date" class="form-control form-control-sm border-0 shadow-sm" value="{{ request('start_date') }}">
                </div>
                <div class="d-flex align-items-center gap-2">
                    <label for="end_date" class="form-label mb-0 small text-muted text-nowrap"><i class="bi bi-calendar-event-fill me-1"></i>Sampai Tgl:</label>
                    <input type="date" name="end_date" id="end_date" class="form-control form-control-sm border-0 shadow-sm" value="{{ request('end_date') }}">
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary px-3 shadow-sm">Filter</button>
                    @if(request('start_date') || request('end_date'))
                        <a href="{{ route('resident.documents.index') }}" class="btn btn-sm btn-light border shadow-sm">Reset</a>
                    @endif
                </div>
            </form>

            <div class="list-group">
                @forelse($documents as $doc)
                    <div class="list-group-item border-0 px-0 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:48px;height:48px;background:#eff6ff;">
                                    <i class="bi bi-file-richtext text-primary" style="font-size:1.5rem;"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">{{ $doc->title }}</h6>
                                    <div class="text-muted small">
                                        Diperbarui: {{ $doc->created_at->format('d F Y') }}
                                    </div>
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('resident.documents.download', $doc) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="bi bi-download me-1"></i>Unduh
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-folder2-open" style="font-size:3rem;opacity:.3;"></i>
                        <p class="mt-3 mb-0">Belum ada dokumen yang tersedia.</p>
                    </div>
                @endforelse
            </div>

            @if($documents->hasPages())
                <div class="mt-4">
                    {{ $documents->links() }}
                </div>
            @endif
        </div>
    </div>
</x-public-layout>
