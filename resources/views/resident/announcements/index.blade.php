<x-public-layout>
    <x-slot name="title">Berita & Rencana Kerja</x-slot>

    <div class="card border-0 shadow-sm animate-in" style="border-radius:14px;">
        <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between py-3 px-4"
             style="border-radius:14px 14px 0 0;border-bottom:1px solid #f1f5f9;">
            <span class="fw-semibold" style="font-size:1rem;">
                <i class="bi bi-megaphone me-2 text-primary"></i>Berita &amp; Rencana Kerja
            </span>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                @forelse($announcements as $item)
                    <div class="col-md-6 col-lg-12">
                        <div class="card h-100 shadow-none border-1 border-light">
                            <div class="card-body p-4">
                                <h5 class="card-title fw-bold mb-2 text-dark">{{ $item->title }}</h5>
                                <div class="d-flex flex-wrap gap-3 text-muted small mb-3">
                                    <span><i class="bi bi-clock me-1"></i>{{ $item->published_at->format('d F Y') }}</span>
                                    @if($item->budget && $item->budget > 0)
                                    <span class="text-success fw-semibold"><i class="bi bi-tags me-1"></i>Anggaran: Rp {{ number_format($item->budget, 0, ',', '.') }}</span>
                                    @endif
                                </div>
                                <p class="card-text text-muted" style="font-size:0.95rem;">
                                    {{ Str::limit(strip_tags($item->content), 120) }}
                                </p>
                                <a href="{{ route('resident.announcements.show', $item) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 mt-2">
                                    Baca Selengkapnya <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5 text-muted">
                        <i class="bi bi-inbox" style="font-size:3rem;opacity:.3;"></i>
                        <p class="mt-3 mb-0">Belum ada berita atau rencana kerja.</p>
                    </div>
                @endforelse
            </div>

            @if($announcements->hasPages())
                <div class="mt-4">
                    {{ $announcements->links() }}
                </div>
            @endif
        </div>
    </div>
</x-public-layout>
