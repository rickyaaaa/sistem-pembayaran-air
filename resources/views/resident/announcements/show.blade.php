<x-public-layout>
    <x-slot name="title">{{ $announcement->title }}</x-slot>

    <div class="mb-4">
        <a href="{{ route('resident.announcements.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar
        </a>
    </div>

    <article class="card border-0 shadow-sm animate-in" style="border-radius:14px;">
        <div class="card-body p-4 p-md-5">
            <h1 class="h3 fw-bold text-dark mb-3">{{ $announcement->title }}</h1>
            
            <div class="d-flex flex-wrap align-items-center gap-4 text-muted small mb-4 pb-4 border-bottom">
                <span><i class="bi bi-calendar3 me-2"></i> {{ $announcement->published_at->format('d F Y, H:i') }}</span>
                @if($announcement->budget && $announcement->budget > 0)
                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill" style="font-size:.85rem;">
                    <i class="bi bi-cash-stack me-1"></i> Anggaran: Rp {{ number_format($announcement->budget, 0, ',', '.') }}
                </span>
                @endif
            </div>

            <div class="content text-dark" style="font-size:1rem; line-height:1.7;">
                {!! nl2br(e($announcement->content)) !!}
            </div>
        </div>
    </article>
</x-public-layout>
