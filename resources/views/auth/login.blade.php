<x-guest-layout>
    <div class="auth-card">
        <div class="auth-logo">
            <img src="{{ asset('images/logo.jpg') }}" alt="SAB Swadaya"
                style="max-height: 120px; width: auto; object-fit: contain; margin-bottom: 1rem;">
        </div>

        <div class="text-center mb-4">
            <h1 class="h4 mb-1" style="font-weight: bold;">Sistem Informasi SAB</h1>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">SAB Swadaya Perum The Spring Ville</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control @error('username') is-invalid @enderror" id="username"
                        name="username" value="{{ old('username') }}" placeholder="Masukkan username" required autofocus>
                </div>
                @error('username')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                        name="password" placeholder="Masukkan password" required>
                </div>
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember" style="font-size: 0.8125rem;">Ingat saya</label>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
            </button>
        </form>

        <div class="d-flex align-items-center my-3">
            <hr class="flex-grow-1">
            <span class="mx-2 text-muted" style="font-size:.8rem;">atau</span>
            <hr class="flex-grow-1">
        </div>

        <a href="{{ route('resident.dashboard') }}" class="btn btn-outline-secondary w-100">
            <i class="bi bi-house-door me-1"></i> Lihat Dashboard Warga
        </a>
    </div>
</x-guest-layout>