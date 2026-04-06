<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="SAB Swadaya - Sistem Informasi Tagihan Air Warga Perum The Spring Ville">

    <title>{{ $title ?? 'SAB Swadaya' }} - SAB Swadaya Springville</title>

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

    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav id="sidebar" class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <img src="{{ asset('images/logo.jpg') }}" alt="SAB Swadaya"
                     style="height:32px;width:auto;object-fit:contain;border-radius:5px;background:#fff;padding:1px;">
                <span>SAB Swadaya</span>
            </div>
            <button class="sidebar-close d-lg-none" onclick="toggleSidebar()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="sidebar-user">
            <div class="sidebar-user-avatar">
                <i class="bi bi-person-circle"></i>
            </div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ Auth::user()->name }}</div>
                <div class="sidebar-user-role">
                    @if(Auth::user()->isAdmin())
                        <span class="badge bg-primary bg-opacity-25 text-primary">Admin</span>
                    @elseif(Auth::user()->isPengurus())
                        <span class="badge bg-info bg-opacity-25 text-info">Pengurus</span>
                    @else
                        <span class="badge bg-success bg-opacity-25 text-success">Warga</span>
                    @endif
                </div>
            </div>
        </div>

        <ul class="sidebar-nav">
            @if(Auth::user()->isStaff())
                <li class="sidebar-nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                @if(auth()->user()->isAdmin())
                    @php $pendingChangesCount = \App\Models\ChangeRequest::where('status', 'pending')->count(); @endphp
                    <li class="sidebar-nav-item">
                        <a href="{{ route('admin.change-requests.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.change-requests.*') ? 'active' : '' }}">
                            <i class="bi bi-clipboard-check"></i>
                            <span>Permintaan</span>
                            @if($pendingChangesCount > 0)
                                <span class="badge bg-danger ms-auto">{{ $pendingChangesCount }}</span>
                            @endif
                        </a>
                    </li>
                @endif
                <li class="sidebar-nav-item">
                    <a href="{{ route('admin.residents.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.residents.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i>
                        <span>Data Warga</span>
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('admin.bills.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.bills.*') ? 'active' : '' }}">
                        <i class="bi bi-receipt"></i>
                        <span>Tagihan</span>
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('admin.payments.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                        <i class="bi bi-credit-card-2-front"></i>
                        <span>Pembayaran</span>
                        @php
                            $pendingCount = \App\Models\Payment::where('status', 'pending')->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="badge bg-warning text-dark ms-auto">{{ $pendingCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('admin.expenses.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.expenses.*') ? 'active' : '' }}">
                        <i class="bi bi-cash-stack"></i>
                        <span>Pengeluaran</span>
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('admin.registrations.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.registrations.*') ? 'active' : '' }}">
                        <i class="bi bi-cash-coin"></i>
                        <span>Pemasukan</span>
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('admin.reports.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <i class="bi bi-bar-chart-line"></i>
                        <span>Laporan</span>
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('admin.documents.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.documents.*') ? 'active' : '' }}">
                        <i class="bi bi-folder-fill"></i>
                        <span>Dokumen</span>
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('admin.announcements.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
                        <i class="bi bi-megaphone-fill"></i>
                        <span>Berita</span>
                    </a>
                </li>
            @else
                <li class="sidebar-nav-item">
                    <a href="{{ route('resident.dashboard') }}" class="sidebar-nav-link {{ request()->routeIs('resident.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('resident.bills.index') }}" class="sidebar-nav-link {{ request()->routeIs('resident.bills.*') ? 'active' : '' }}">
                        <i class="bi bi-receipt"></i>
                        <span>Tagihan Saya</span>
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('resident.payments.history') }}" class="sidebar-nav-link {{ request()->routeIs('resident.payments.*') ? 'active' : '' }}">
                        <i class="bi bi-clock-history"></i>
                        <span>Riwayat Bayar</span>
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('resident.documents.index') }}" class="sidebar-nav-link {{ request()->routeIs('resident.documents.*') ? 'active' : '' }}">
                        <i class="bi bi-folder-fill"></i>
                        <span>Dokumen</span>
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('resident.announcements.index') }}" class="sidebar-nav-link {{ request()->routeIs('resident.announcements.*') ? 'active' : '' }}">
                        <i class="bi bi-megaphone-fill"></i>
                        <span>Berita</span>
                    </a>
                </li>
            @endif

            <li class="sidebar-nav-divider"></li>

            <li class="sidebar-nav-item">
                <a href="{{ route('password.edit') }}" class="sidebar-nav-link {{ request()->routeIs('password.*') ? 'active' : '' }}">
                    <i class="bi bi-key"></i>
                    <span>Ubah Password</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                    @csrf
                    <a href="#" class="sidebar-nav-link text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-left"></i>
                        <span>Keluar</span>
                    </a>
                </form>
            </li>
        </ul>
    </nav>

    <!-- Overlay -->
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Bar -->
        <div class="topbar">
            <button class="topbar-toggle d-lg-none" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <h1 class="topbar-title">{{ $title ?? 'Dashboard' }}</h1>
            <div class="topbar-right">
                <span class="topbar-date">
                    <i class="bi bi-calendar3"></i>
                    {{ now()->translatedFormat('l, d F Y') }}
                </span>
            </div>
        </div>

        <!-- Content -->
        <div class="content-wrapper">
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

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }

        // Format numbers as Indonesian Rupiah
        function formatRupiah(num) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
        }
    </script>

    @stack('scripts')
</body>
</html>
