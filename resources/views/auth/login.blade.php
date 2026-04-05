<x-guest-layout>
    <div class="auth-card">
        <div class="auth-logo">
            <div class="auth-logo-icon">
                <i class="bi bi-droplet-fill"></i>
            </div>
            <h1>SAB Springville</h1>
            <p>Sistem Informasi Tagihan Air</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="username" class="form-label">Username (No. Blok)</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control @error('username') is-invalid @enderror"
                           id="username" name="username" value="{{ old('username') }}"
                           placeholder="Contoh: a1" required autofocus>
                </div>
                @error('username')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                           id="password" name="password" placeholder="Masukkan password" required>
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
    </div>
</x-guest-layout>
