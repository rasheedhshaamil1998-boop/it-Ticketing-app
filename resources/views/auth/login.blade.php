<x-layouts.auth title="Login - IT HELPDESK">
    <div class="text-center mb-4">
        <div class="auth-kicker mb-2">Secure Staff Portal</div>
        <h2 class="h4 fw-bold mb-1">Welcome back</h2>
        <p class="text-muted mb-0">Sign in to manage helpdesk requests.</p>
    </div>
    <form method="POST" action="{{ route('login.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label" for="email">Email Address</label>
            <div class="input-group input-group-lg">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label" for="password">Password</label>
            <div class="input-group input-group-lg">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" required>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="form-check mb-4">
            <input class="form-check-input" id="remember" name="remember" type="checkbox" value="1">
            <label class="form-check-label" for="remember">Remember me</label>
        </div>
        <button class="btn btn-primary btn-lg w-100 auth-submit" type="submit"><i class="bi bi-box-arrow-in-right me-2"></i>Login</button>
    </form>
    <div class="text-center mt-4">
        <a href="{{ route('register') }}">Create an employee account</a>
    </div>
</x-layouts.auth>
