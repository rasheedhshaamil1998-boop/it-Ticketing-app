<!doctype html>
<html lang="en" data-theme="dark" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'IT HELPDESK' }}</title>
    <script src="{{ asset('js/theme.js') }}?v={{ filemtime(public_path('js/theme.js')) }}"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/helpdesk.css') }}?v={{ filemtime(public_path('css/helpdesk.css')) }}" rel="stylesheet">
</head>
<body>
    <main class="auth-shell d-flex align-items-center py-5">
        <div class="auth-theme-tools"><x-theme-toggle /></div>
        <div class="container position-relative">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-5 col-xl-4">
                    <div class="auth-brand text-center mb-4">
                        <div class="auth-logo-wrap mx-auto mb-3">
                            <img class="auth-logo" src="{{ asset('images/mi-logo.png') }}" alt="Maldives Immigration">
                        </div>
                        <h1 class="h3 fw-bold mb-1">IT HELPDESK</h1>
                        <p class="mb-0">Support Ticket Management System</p>
                    </div>

                    @if (session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    <div class="auth-card card border-0">
                        <div class="card-body p-4 p-md-5">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
