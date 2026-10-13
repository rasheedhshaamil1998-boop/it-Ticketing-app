<x-layouts.auth title="Register - IT HELPDESK">
    <h2 class="h4 fw-bold mb-4">Create employee account</h2>
    <form method="POST" action="{{ route('register.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label" for="name">Full Name</label>
            <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label" for="email">Email Address</label>
            <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label" for="department">Department</label>
            <input class="form-control @error('department') is-invalid @enderror" id="department" name="department" value="{{ old('department') }}" required>
            @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="password">Password</label>
                <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" required>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="password_confirmation">Confirm Password</label>
                <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" required>
            </div>
        </div>
        <button class="btn btn-primary w-100 mt-4" type="submit"><i class="bi bi-person-plus me-2"></i>Register</button>
    </form>
    <div class="text-center mt-4">
        <a href="{{ route('login') }}">Already have an account?</a>
    </div>
</x-layouts.auth>
