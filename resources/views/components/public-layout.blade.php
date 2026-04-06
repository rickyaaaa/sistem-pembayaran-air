@props(['title' => null])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="SAB Swadaya - Sistem Informasi Tagihan Air Warga Perum The Spring Ville">

    <title>{{ $title ?? 'Portal Warga' }} - SAB Swadaya Springville</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
        body { background: #f0f4f8; font-family: 'Inter', sans-serif; }

        /* ---- Public top navbar ---- */
        .public-topbar {
            background: linear-gradient(135deg, #1d4ed8 0%, #0891b2 100%);
            box-shadow: 0 2px 16px rgba(29,78,216,.30);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .public-topbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.6rem 1.5rem;
            gap: 1rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        .public-brand {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            text-decoration: none;
            color: #fff;
        }
        .public-brand img {
            height: 48px;
            width: auto;
            object-fit: contain;
            border-radius: 8px;
            background: #fff;
            padding: 2px;
        }
        .public-brand-text strong {
            display: block;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: .02em;
            line-height: 1.2;
        }
        .public-brand-text span {
            display: block;
            font-size: 0.7rem;
            opacity: .8;
        }
        .pub-nav { display: flex; align-items: center; gap: .2rem; }
        .pub-nav-link {
            color: rgba(255,255,255,.85);
            text-decoration: none;
            padding: .42rem .8rem;
            border-radius: 8px;
            font-size: .875rem;
            font-weight: 500;
            transition: background .15s;
            display: flex;
            align-items: center;
            gap: .4rem;
        }
        .pub-nav-link:hover, .pub-nav-link.active {
            background: rgba(255,255,255,.18);
            color: #fff;
        }
        .admin-badge {
            color: rgba(255,255,255,.65);
            text-decoration: none;
            font-size: .75rem;
            padding: .3rem .65rem;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,.3);
            transition: all .18s;
            margin-left: .5rem;
        }
        .admin-badge:hover { background: rgba(255,255,255,.12); color: #fff; }

        /* ---- Main content ---- */
        .pub-main {
            max-width: 1100px;
            margin: 0 auto;
            padding: 2rem 1.25rem 3.5rem;
        }

        /* ---- Footer ---- */
        .pub-footer {
            background: #1e293b;
            color: rgba(255,255,255,.45);
            text-align: center;
            padding: 1.25rem;
            font-size: .78rem;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="public-topbar">
        <div class="public-topbar-inner">
            <a href="{{ route('resident.dashboard') }}" class="public-brand">
                <img src="{{ asset('images/logo.jpg') }}" alt="SAB Swadaya Logo">
                <div class="public-brand-text">
                    <strong>SAB Swadaya</strong>
                    <span>Perum The Spring Ville</span>
                </div>
            </a>

            <div class="pub-nav">
                <a href="{{ route('resident.dashboard') }}"
                   class="pub-nav-link {{ request()->routeIs('resident.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-house-door"></i>
                    <span class="d-none d-sm-inline">Beranda</span>
                </a>
                <a href="{{ route('resident.bills.index') }}"
                   class="pub-nav-link {{ request()->routeIs('resident.bills.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i>
                    <span class="d-none d-sm-inline">Cek Tagihan</span>
                </a>
                <a href="{{ route('resident.payments.history') }}"
                   class="pub-nav-link {{ request()->routeIs('resident.payments.*') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i>
                    <span class="d-none d-lg-inline">Riwayat</span>
                </a>
                <a href="{{ route('resident.documents.index') }}"
                   class="pub-nav-link {{ request()->routeIs('resident.documents.*') ? 'active' : '' }}">
                    <i class="bi bi-folder-fill"></i>
                    <span class="d-none d-lg-inline">Dokumen</span>
                </a>
                <a href="{{ route('resident.announcements.index') }}"
                   class="pub-nav-link {{ request()->routeIs('resident.announcements.*') ? 'active' : '' }}">
                    <i class="bi bi-megaphone-fill"></i>
                    <span class="d-none d-lg-inline">Berita</span>
                </a>

                @auth
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="admin-badge">
                            <i class="bi bi-shield-lock me-1"></i>Admin
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="admin-badge">
                        <i class="bi bi-lock me-1"></i>Admin
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main class="pub-main">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i>{{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{ $slot }}
    </main>

    <footer class="pub-footer">
        <p class="mb-0">&copy; {{ date('Y') }} SAB Swadaya — Perum The Spring Ville. Semua data bersifat informatif.</p>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
