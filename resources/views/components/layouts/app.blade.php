<!doctype html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'IT HELPDESK' }}</title>
    <script>
        document.documentElement.dataset.theme = 'dark';
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/helpdesk.css') }}?v={{ filemtime(public_path('css/helpdesk.css')) }}" rel="stylesheet">
</head>
<body>
    @php($user = auth()->user())
    <div class="app-shell d-lg-flex">
        <aside class="sidebar p-3">
            <div class="d-flex align-items-center gap-2 mb-4">
                <span class="sidebar-logo"><img src="{{ asset('images/mi-logo.png') }}" alt="Maldives Immigration"></span>
                <div>
                    <div class="fw-bold">IT HELPDESK</div>
                    <small class="text-white-50">Support Tickets</small>
                </div>
            </div>
            <nav class="nav flex-column gap-1">
                @if ($user->isAdmin())
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                    <a class="nav-link {{ request()->routeIs('admin.tickets.*') && ! request('status') ? 'active' : '' }}" href="{{ route('admin.tickets.index') }}"><i class="bi bi-ticket-detailed me-2"></i>All Tickets</a>
                    <a class="nav-link" href="{{ route('admin.tickets.index', ['status' => 'Open']) }}"><i class="bi bi-inbox me-2"></i>Open Tickets</a>
                    <a class="nav-link" href="{{ route('admin.tickets.index', ['status' => 'In Progress']) }}"><i class="bi bi-hourglass-split me-2"></i>In Progress</a>
                    <a class="nav-link" href="{{ route('admin.tickets.index', ['status' => 'Resolved']) }}"><i class="bi bi-check2-circle me-2"></i>Resolved Tickets</a>
                    <a class="nav-link" href="{{ route('admin.tickets.index', ['status' => 'Closed']) }}"><i class="bi bi-archive me-2"></i>Closed Tickets</a>
                    <div class="sidebar-subheading">Roles and Permissions</div>
                    <a class="nav-link {{ request()->routeIs('admin.roles-permissions') ? 'active' : '' }}" href="{{ route('admin.roles-permissions') }}"><i class="bi bi-shield-lock me-2"></i>Access Matrix</a>
                    @if ($user->isSuperAdmin())
                        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}"><i class="bi bi-people me-2"></i>Users</a>
                    @endif
                    <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}"><i class="bi bi-bar-chart me-2"></i>Reports</a>
                @else
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                    <a class="nav-link {{ request()->routeIs('tickets.create') ? 'active' : '' }}" href="{{ route('tickets.create') }}"><i class="bi bi-plus-circle me-2"></i>Create Ticket</a>
                    <a class="nav-link {{ request()->routeIs('tickets.index') ? 'active' : '' }}" href="{{ route('tickets.index') }}"><i class="bi bi-ticket-detailed me-2"></i>My Tickets</a>
                @endif
                <a class="nav-link {{ request()->routeIs('profile') ? 'active' : '' }}" href="{{ route('profile') }}"><i class="bi bi-person-circle me-2"></i>Profile</a>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button class="nav-link border-0 bg-transparent w-100 text-start" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                </form>
            </nav>
        </aside>

        <div class="main-panel flex-grow-1">
            <header class="topbar px-3 px-lg-4 py-3 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">IT Helpdesk</div>
                    <h1 class="h4 mb-0">{{ $heading ?? 'Dashboard' }}</h1>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="text-end">
                        <div class="fw-semibold">{{ $user->name }}</div>
                        <small class="text-muted">{{ $user->roleLabel() }} / {{ $user->department }}</small>
                    </div>
                </div>
            </header>

            <main class="p-3 p-lg-4">
                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <div class="fw-semibold">Please fix the highlighted fields.</div>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                {{ $slot }}
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
