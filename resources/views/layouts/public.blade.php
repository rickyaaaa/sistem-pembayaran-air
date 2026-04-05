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
        /* Public layout overrides */
        body { background: #f0f4f8; }

        .public-topbar {
            background: linear-gradient(135deg, #0d6efd 0%, #0891b2 100%);
            padding: 0;
            box-shadow: 0 2px 12px rgba(13,110,253,.25);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .public-topbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.65rem 1.5rem;
            gap: 1rem;
        }
        .public-brand {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            text-decoration: none;
            color: #fff;
        }
        .public-brand img {
            height: 46px;
            width: auto;
            object-fit: contain;
            border-radius: 6px;
            background: #fff;
            padding: 2px;
        }
        .public-brand-text {
            line-height: 1.15;
        }
        .public-brand-text strong {
            display: block;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: .02em;
        }
        .public-brand-text span {
            display: block;
            font-size: 0.72rem;
            opacity: .82;
        }
        .public-nav-links {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        .public-nav-link {
            color: rgba(255,255,255,.88);
            text-decoration: none;
            padding: 0.45rem 0.85rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: background .18s, color .18s;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        .public-nav-link:hover,
        .public-nav-link.active {
            background: rgba(255,255,255,.18);
            color: #fff;
        }
        .public-nav-link i { font-size: 1rem; }

        .admin-link {
            color: rgba(255,255,255,.65);
            text-decoration: none;
            font-size: 0.75rem;
            padding: 0.35rem 0.65rem;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,.3);
            transition: all .18s;
        }
        .admin-link:hover { background: rgba(255,255,255,.12); color: #fff; }

        .public-content {
            max-width: 1100px;
            margin: 0 auto;
            padding: 2rem 1.25rem 3rem;
        }

        .public-footer {
            background: #1e293b;
            color: rgba(255,255,255,.5);
            text-align: center;
            padding: 1.25rem;
            font-size: 0.8rem;
        }
        .public-footer a { color: rgba(255,255,255,.65); }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Top Navigation Bar -->
    <nav class="public-topbar">
        <div class="public-topbar-inner">
            <a href="{{ route('resident.dashboard') }}" class="public-brand">
                <img src="{{ asset('images/logo.jpg') }}" alt="SAB Swadaya Logo">
                <div class="public-brand-text">
                    <strong>SAB Swadaya</strong>
                    <span>Perum The Spring Ville</span>
                </div>
            </a>

            <div class="public-nav-links">
                <a href="{{ route('resident.dashboard') }}"
                   class="public-nav-link {{ request()->routeIs('resident.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-house-door"></i>
                    <span class="d-none d-sm-inline">Beranda</span>
                </a>
                <a href="{{ route('resident.bills.index') }}"
                   class="public-nav-link {{ request()->routeIs('resident.bills.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i>
                    <span class="d-none d-sm-inline">Cek Tagihan</span>
                </a>
                <a href="{{ route('resident.payments.history') }}"
                   class="public-nav-link {{ request()->routeIs('resident.payments.*') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i>
                    <span class="d-none d-sm-inline">Riwayat</span>
                </a>

                @auth
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="admin-link ms-2">
                            <i class="bi bi-shield-lock me-1"></i>Admin
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="admin-link ms-2">
                        <i class="bi bi-lock me-1"></i>Admin
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <div class="public-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{ $slot }}
        </div>
    </main>

    <!-- Footer -->
    <footer class="public-footer">
        <p class="mb-0">&copy; {{ date('Y') }} SAB Swadaya — Perum The Spring Ville. Semua data bersifat informatif.</p>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
