@props(['title' => null])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="SAB Swadaya - Sistem Informasi Tagihan Air Warga Perum The Spring Ville">
    <title>{{ $title ?? 'Portal Warga' }} - SAB Swadaya Springville</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
        body {
            background: #f0f4f8;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* ── Navbar ── */
        .public-topbar {
            background: #1d4ed8;
            box-shadow: 0 1px 0 rgba(0,0,0,.12);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .public-topbar-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            height: 60px;
            gap: 1rem;
        }

        /* Brand */
        .public-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #fff;
            flex-shrink: 0;
        }
        .public-brand img {
            width: 40px;
            height: 40px;
            object-fit: contain;
            border-radius: 10px;
            background: #fff;
            padding: 3px;
        }
        .public-brand-text strong {
            display: block;
            font-size: 14px;
            font-weight: 700;
            line-height: 1.2;
            color: #fff;
        }
        .public-brand-text span {
            display: block;
            font-size: 11px;
            color: rgba(255,255,255,.7);
        }

        /* Desktop nav */
        .pub-nav {
            display: flex;
            align-items: center;
            gap: 2px;
        }
        .pub-nav-link {
            color: rgba(255,255,255,.85);
            text-decoration: none;
            padding: 7px 11px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: background .15s;
            white-space: nowrap;
        }
        .pub-nav-link:hover,
        .pub-nav-link.active {
            background: rgba(255,255,255,.18);
            color: #fff;
        }
        .admin-badge {
            color: rgba(255,255,255,.7);
            text-decoration: none;
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,.3);
            display: flex;
            align-items: center;
            gap: 5px;
            margin-left: 4px;
            transition: all .15s;
            white-space: nowrap;
        }
        .admin-badge:hover { background: rgba(255,255,255,.12); color: #fff; }

        /* Hamburger */
        .pub-hamburger {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            color: #fff;
            transition: background .15s;
            flex-shrink: 0;
            line-height: 0;
        }
        .pub-hamburger:hover { background: rgba(255,255,255,.15); }
        .pub-hamburger i { font-size: 22px; }

        /* Drawer overlay */
        .drawer-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.45);
            z-index: 1040;
            opacity: 0;
            transition: opacity .25s;
        }
        .drawer-overlay.open { opacity: 1; }

        /* Drawer panel */
        .pub-drawer {
            position: fixed;
            top: 0;
            right: -300px;
            width: 280px;
            height: 100%;
            background: #1e3a8a;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            transition: right .28s cubic-bezier(.4,0,.2,1);
        }
        .pub-drawer.open { right: 0; }

        .drawer-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 16px 12px;
            border-bottom: 1px solid rgba(255,255,255,.12);
        }
        .drawer-header span {
            font-size: 14px;
            font-weight: 600;
            color: #fff;
        }
        .drawer-close {
            background: none;
            border: none;
            color: rgba(255,255,255,.7);
            cursor: pointer;
            padding: 6px;
            border-radius: 8px;
            line-height: 0;
            transition: background .15s;
        }
        .drawer-close:hover { background: rgba(255,255,255,.12); color: #fff; }
        .drawer-close i { font-size: 18px; }

        .drawer-nav {
            flex: 1;
            padding: 12px 10px;
            display: flex;
            flex-direction: column;
            gap: 2px;
            overflow-y: auto;
        }
        .drawer-nav-link {
            color: rgba(255,255,255,.85);
            text-decoration: none;
            padding: 10px 12px;
            border-radius: 9px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: background .15s;
        }
        .drawer-nav-link:hover,
        .drawer-nav-link.active {
            background: rgba(255,255,255,.15);
            color: #fff;
        }
        .drawer-nav-link i { font-size: 17px; }

        .drawer-footer {
            padding: 10px 10px 16px;
            border-top: 1px solid rgba(255,255,255,.12);
        }
        .drawer-admin-link {
            color: rgba(255,255,255,.7);
            text-decoration: none;
            font-size: 13px;
            padding: 9px 12px;
            border-radius: 9px;
            border: 1px solid rgba(255,255,255,.25);
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background .15s;
        }
        .drawer-admin-link:hover { background: rgba(255,255,255,.1); color: #fff; }
        .drawer-admin-link i { font-size: 15px; }

        /* ── Main & Footer ── */
        .pub-main {
            max-width: 1100px;
            margin: 0 auto;
            padding: 2rem 1.25rem 3.5rem;
        }
        .pub-footer {
            background: #1e293b;
            color: rgba(255,255,255,.45);
            text-align: center;
            padding: 1.25rem;
            font-size: .78rem;
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .pub-nav { display: none; }
            .pub-hamburger { display: flex; align-items: center; }
            .drawer-overlay { display: block; pointer-events: none; }
            .drawer-overlay.open { pointer-events: auto; }
        }
    </style>

    @stack('styles')
</head>
<body>

    {{-- Drawer overlay --}}
    <div class="drawer-overlay" id="drawerOverlay" onclick="closeDrawer()"></div>

    {{-- Drawer panel --}}
    <div class="pub-drawer" id="pubDrawer">
        <div class="drawer-header">
            <span>Menu</span>
            <button class="drawer-close" onclick="closeDrawer()"><i class="bi bi-x-lg"></i></button>
        </div>
        <nav class="drawer-nav">
            <a href="{{ route('resident.dashboard') }}"
               class="drawer-nav-link {{ request()->routeIs('resident.dashboard') ? 'active' : '' }}">
                <i class="bi bi-house-door"></i> Beranda
            </a>
            <a href="{{ route('resident.bills.index') }}"
               class="drawer-nav-link {{ request()->routeIs('resident.bills.*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i> Cek Tagihan
            </a>
            <a href="{{ route('resident.payments.history') }}"
               class="drawer-nav-link {{ request()->routeIs('resident.payments.*') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i> Riwayat
            </a>
            <a href="{{ route('resident.documents.index') }}"
               class="drawer-nav-link {{ request()->routeIs('resident.documents.*') ? 'active' : '' }}">
                <i class="bi bi-folder-fill"></i> Dokumen
            </a>
            <a href="{{ route('resident.announcements.index') }}"
               class="drawer-nav-link {{ request()->routeIs('resident.announcements.*') ? 'active' : '' }}">
                <i class="bi bi-megaphone-fill"></i> Berita
            </a>
        </nav>
        <div class="drawer-footer">
            @auth
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="drawer-admin-link">
                        <i class="bi bi-shield-lock"></i> Panel Admin
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="drawer-admin-link">
                    <i class="bi bi-lock"></i> Login
                </a>
            @endauth
        </div>
    </div>

    {{-- Navbar --}}
    <nav class="public-topbar">
        <div class="public-topbar-inner">
            <a href="{{ route('resident.dashboard') }}" class="public-brand">
                <img src="{{ asset('images/logo.jpg') }}" alt="SAB Swadaya">
                <div class="public-brand-text">
                    <strong>SAB Swadaya</strong>
                    <span>Perum The Spring Ville</span>
                </div>
            </a>

            {{-- Desktop nav --}}
            <div class="pub-nav">
                <a href="{{ route('resident.dashboard') }}"
                   class="pub-nav-link {{ request()->routeIs('resident.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-house-door"></i> Beranda
                </a>
                <a href="{{ route('resident.bills.index') }}"
                   class="pub-nav-link {{ request()->routeIs('resident.bills.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i> Cek Tagihan
                </a>
                <a href="{{ route('resident.payments.history') }}"
                   class="pub-nav-link {{ request()->routeIs('resident.payments.*') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i> Riwayat
                </a>
                <a href="{{ route('resident.documents.index') }}"
                   class="pub-nav-link {{ request()->routeIs('resident.documents.*') ? 'active' : '' }}">
                    <i class="bi bi-folder-fill"></i> Dokumen
                </a>
                <a href="{{ route('resident.announcements.index') }}"
                   class="pub-nav-link {{ request()->routeIs('resident.announcements.*') ? 'active' : '' }}">
                    <i class="bi bi-megaphone-fill"></i> Berita
                </a>

                @auth
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="admin-badge">
                            <i class="bi bi-shield-lock"></i> Admin
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="admin-badge">
                        <i class="bi bi-lock"></i> Admin
                    </a>
                @endauth
            </div>

            {{-- Hamburger (mobile only) --}}
            <button class="pub-hamburger" onclick="openDrawer()" aria-label="Buka menu">
                <i class="bi bi-list"></i>
            </button>
        </div>
    </nav>

    {{-- Content --}}
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function openDrawer() {
            document.getElementById('pubDrawer').classList.add('open');
            document.getElementById('drawerOverlay').classList.add('open');
            document.body.style.overflow = 'hidden';
        }
        function closeDrawer() {
            document.getElementById('pubDrawer').classList.remove('open');
            document.getElementById('drawerOverlay').classList.remove('open');
            document.body.style.overflow = '';
        }
        // Tutup drawer kalau layar diperbesar ke desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) closeDrawer();
        });
        // Tutup dengan tombol Escape
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeDrawer();
        });
    </script>

    @stack('scripts')
</body>
</html>