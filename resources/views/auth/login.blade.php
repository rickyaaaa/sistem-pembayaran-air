<x-guest-layout>
    <style>
        .login-split-card {
            display: flex;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            overflow: hidden;
            width: 100%;
            max-width: 950px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
            animation: authCardAppear 0.4s ease-out;
            margin: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        .login-brand-side {
            flex: 1.1;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-brand-side::before {
            content: '';
            position: absolute;
            top: -20%; left: -10%; width: 150%; height: 150%;
            background: radial-gradient(circle at top right, rgba(255,255,255,0.1), transparent 60%),
                        radial-gradient(circle at bottom left, rgba(0,0,0,0.2), transparent 50%);
            z-index: 0;
        }

        .login-brand-content {
            position: relative;
            z-index: 1;
        }

        .login-form-side {
            flex: 1;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
        }

        .login-logo {
            width: 110px;
            height: 110px;
            object-fit: contain;
            background: white;
            padding: 12px;
            border-radius: 20px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
            margin-bottom: 2rem;
            transition: transform 0.3s ease;
        }

        .login-logo:hover {
            transform: scale(1.05);
        }

        .login-brand-title {
            font-size: 1.7rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }

        .login-brand-desc {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.5;
        }

        .login-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.3rem;
        }

        .login-subtitle {
            color: var(--gray-500);
            font-size: 0.95rem;
            margin-bottom: 2rem;
        }

        .mobile-logo-wrap {
            display: none; /* Hidden on desktop */
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .mobile-logo {
            width: 70px;
            height: 70px;
            object-fit: contain;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .auth-divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.25rem 0;
        }

        .auth-divider::before,
        .auth-divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid var(--gray-200);
        }

        .auth-divider-text {
            padding: 0 1rem;
            color: var(--gray-400);
            font-size: 0.8rem;
            font-weight: 600;
        }

        .btn-guest {
            border: 1px solid var(--gray-200);
            background: transparent;
            color: var(--gray-600);
            font-weight: 600;
            transition: all 0.2s ease;
            border-radius: 8px;
        }

        .btn-guest:hover {
            background: var(--gray-50);
            color: var(--gray-900);
            border-color: var(--gray-300);
        }

        .login-form-side .form-control {
            padding: 0.6rem 0.8rem;
            font-size: 0.95rem;
        }

        .login-form-side .input-group-text {
            padding: 0.6rem 0.8rem;
        }

        .login-form-side .btn-primary {
            padding: 0.6rem 1rem;
            border-radius: 8px;
            font-weight: 600;
        }

        /* Responsive Adjustments */
        @media (max-width: 991px) {
            .login-split-card {
                max-width: 420px; /* Small card format */
                flex-direction: column;
                margin: 1rem;
            }
            .login-brand-side {
                display: none; /* Hide large branding on mobile entirely to save space */
            }
            .login-form-side {
                padding: 2rem 1.5rem;
                border-radius: 20px;
            }
            .mobile-logo-wrap {
                display: block; /* Show small logo on mobile explicitly inside form */
            }
            .login-title {
                font-size: 1.4rem;
                text-align: center;
            }
            .login-subtitle {
                font-size: 0.85rem;
                text-align: center;
                margin-bottom: 1.5rem;
            }
        }
        
        @media (max-width: 480px) {
            .login-split-card {
                margin: 0;
                border-radius: 0; /* Remove border radius on very small screens to fit edge to edge if needed, or keep tight margin */
                border: none;
                box-shadow: none;
            }
            .login-form-side {
                padding: 1.5rem;
            }
            /* If app wrapper has paddings we might still see them, but it's okay */
        }
    </style>

    <div class="login-split-card">
        <!-- Branding Side (Desktop Only) -->
        <div class="login-brand-side">
            <div class="login-brand-content d-flex flex-column align-items-center">
                <img src="{{ asset('images/logo.jpg') }}" alt="SAB Swadaya Logo" class="login-logo shadow-lg">
                <h1 class="login-brand-title">Sistem Informasi SAB</h1>
                <p class="login-brand-desc text-center">
                    Pengelolaan Sistem Pembayaran Air Bersih<br>
                    Swadaya Perum The Spring Ville
                </p>
            </div>
        </div>

        <!-- Form Side -->
        <div class="login-form-side">
            <!-- Mobile Logo -->
            <div class="mobile-logo-wrap">
                <img src="{{ asset('images/logo.jpg') }}" alt="Logo" class="mobile-logo mb-2">
                <h3 class="fw-bold mb-0" style="font-size: 1.1rem; color: var(--gray-800);">Sistem SAB</h3>
            </div>

            <div>
                <h2 class="login-title">Selamat Datang 👋</h2>
                <p class="login-subtitle">Silakan masuk menggunakan akun Anda.</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label for="username" class="form-label" style="font-size:0.85rem;">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-secondary"></i></span>
                        <input type="text" class="form-control border-start-0 ps-0 bg-light @error('username') is-invalid @enderror" id="username"
                            name="username" value="{{ old('username') }}" placeholder="Masukkan username" required autofocus>
                    </div>
                    @error('username')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label" style="font-size:0.85rem;">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-secondary"></i></span>
                        <input type="password" class="form-control border-start-0 ps-0 bg-light @error('password') is-invalid @enderror" id="password"
                            name="password" placeholder="Masukkan password" required>
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input shadow-sm" id="remember" name="remember">
                        <label class="form-check-label text-muted" for="remember" style="font-size: 0.8rem;">Ingat saya</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 d-flex justify-content-center align-items-center gap-2">
                    <i class="bi bi-box-arrow-in-right"></i> Masuk Sekarang
                </button>
            </form>

            <div class="auth-divider">
                <span class="auth-divider-text">ATAU</span>
            </div>

            <a href="{{ route('resident.dashboard') }}" class="btn btn-guest w-100 py-2 d-flex justify-content-center align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="bi bi-house-door"></i> Masuk sebagai Warga
            </a>
        </div>
    </div>
</x-guest-layout>