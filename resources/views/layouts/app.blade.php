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
    @php
        $user = auth()->user();
        $ticketIndex = $user->isAdmin() ? 'admin.tickets.index' : 'tickets.index';
        $navigation = $user->isAdmin()
            ? [
                ['Dashboard', 'grid-1x2', 'admin.dashboard', [], request()->routeIs('admin.dashboard')],
                ['All Tickets', 'ticket-detailed', 'admin.tickets.index', [], request()->routeIs('admin.tickets.*') && ! request('status') && ! request('assigned')],
                ['Assigned Tickets', 'person-check', 'admin.tickets.index', ['assigned' => 'me'], request()->routeIs('admin.tickets.index') && request('assigned') === 'me'],
                ['Open Tickets', 'inbox', 'admin.tickets.index', ['status' => 'Open'], request()->routeIs('admin.tickets.index') && ! request('assigned') && request('status') === 'Open'],
                ['In Progress', 'hourglass-split', 'admin.tickets.index', ['status' => 'In Progress'], request()->routeIs('admin.tickets.index') && ! request('assigned') && request('status') === 'In Progress'],
                ['Resolved Tickets', 'check2-circle', 'admin.tickets.index', ['status' => 'Resolved'], request()->routeIs('admin.tickets.index') && ! request('assigned') && request('status') === 'Resolved'],
                ['Closed Tickets', 'archive', 'admin.tickets.index', ['status' => 'Closed'], request()->routeIs('admin.tickets.index') && ! request('assigned') && request('status') === 'Closed'],
              ]
            : [['Dashboard', 'grid-1x2', 'dashboard', [], request()->routeIs('dashboard')]];
    @endphp
    <div class="app-shell d-lg-flex">
        <aside class="sidebar p-3" id="app-sidebar" aria-label="Main navigation">
            <a class="sidebar-brand-link" href="{{ route($user->isAdmin() ? 'admin.dashboard' : 'dashboard') }}" title="IT HELPDESK dashboard"><span class="sidebar-logo"><img src="{{ asset('images/mi-logo.png') }}" alt="Maldives Immigration"></span><span class="sidebar-brand-copy"><strong>IT HELPDESK</strong><small>Maldives Immigration</small></span></a>
            <button class="sidebar-close" type="button" data-sidebar-dismiss aria-label="Close navigation"><i class="bi bi-x-lg" aria-hidden="true"></i></button>
            <div class="sidebar-subheading">Workspace</div>
            <nav class="nav flex-column gap-1">
                @foreach ($navigation as [$label, $icon, $route, $parameters, $active])
                    <a class="nav-link {{ $active ? 'active' : '' }}" href="{{ route($route, $parameters) }}" title="{{ $label }}" @if ($active) aria-current="page" @endif><i class="bi bi-{{ $icon }}" aria-hidden="true"></i><span class="nav-label">{{ $label }}</span></a>
                @endforeach
                <a class="nav-link {{ request()->routeIs('tickets.create') ? 'active' : '' }}" href="{{ route('tickets.create') }}" title="Create Ticket"><i class="bi bi-plus-circle" aria-hidden="true"></i><span class="nav-label">Create Ticket</span></a>
                <a class="nav-link {{ request()->routeIs('tickets.index') ? 'active' : '' }}" href="{{ route('tickets.index') }}" title="My Tickets"><i class="bi bi-ticket-perforated" aria-hidden="true"></i><span class="nav-label">My Tickets</span></a>
                @if ($user->isAdmin())
                    <div class="sidebar-subheading">Administration</div>
                    @if ($user->isSuperAdmin())<a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}" title="Users Management"><i class="bi bi-people" aria-hidden="true"></i><span class="nav-label">Users Management</span></a>@endif
                    <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}" title="Reports"><i class="bi bi-bar-chart" aria-hidden="true"></i><span class="nav-label">Reports</span></a>
                    <a class="nav-link {{ request()->routeIs('admin.roles-permissions') ? 'active' : '' }}" href="{{ route('admin.roles-permissions') }}" title="Access Matrix"><i class="bi bi-shield-lock" aria-hidden="true"></i><span class="nav-label">Access Matrix</span></a>
                @endif
                <div class="sidebar-subheading">Account</div>
                <a class="nav-link {{ request()->routeIs('profile') ? 'active' : '' }}" href="{{ route('profile') }}" title="Profile"><i class="bi bi-person-circle" aria-hidden="true"></i><span class="nav-label">Profile</span></a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="nav-link sidebar-logout w-100 text-start" type="submit" title="Logout"><i class="bi bi-box-arrow-right" aria-hidden="true"></i><span class="nav-label">Logout</span></button></form>
            </nav>
            <div class="sidebar-footnote"><i class="bi bi-shield-check" aria-hidden="true"></i><span>Staff support workspace</span></div>
        </aside>
        <button class="sidebar-backdrop" type="button" data-sidebar-dismiss aria-label="Close navigation" hidden></button>
        <div class="main-panel flex-grow-1" id="app-main-panel">
            <header class="topbar">
                <div class="topbar-heading"><button class="navigation-toggle" type="button" data-sidebar-toggle aria-controls="app-sidebar" aria-expanded="true" aria-label="Toggle navigation" title="Toggle navigation"><i class="bi bi-list" aria-hidden="true"></i></button><div><span class="topbar-kicker">IT Helpdesk</span><h1>{{ $heading ?? 'Dashboard' }}</h1></div></div>
                <form class="topbar-search" method="GET" action="{{ route($ticketIndex) }}" role="search"><i class="bi bi-search" aria-hidden="true"></i><label class="visually-hidden" for="workspace-search">Search tickets</label><input id="workspace-search" name="search" type="search" placeholder="Search tickets..." maxlength="255"><button type="submit" aria-label="Search tickets"><i class="bi bi-arrow-return-left" aria-hidden="true"></i></button></form>
                <div class="topbar-controls"><a class="queue-shortcut" href="{{ route($ticketIndex, ['status' => 'Open']) }}" title="View open requests" aria-label="View open requests"><i class="bi bi-inbox" aria-hidden="true"></i></a><x-theme-toggle /><details class="profile-menu"><summary><span class="profile-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span><span class="profile-copy"><strong>{{ $user->name }}</strong><small>{{ $user->roleLabel() }}</small></span><i class="bi bi-chevron-down" aria-hidden="true"></i></summary><div class="profile-dropdown"><div class="profile-department">{{ $user->department }}</div><a href="{{ route('profile') }}"><i class="bi bi-person-circle" aria-hidden="true"></i>My Profile</a><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit"><i class="bi bi-box-arrow-right" aria-hidden="true"></i>Logout</button></form></div></details></div>
            </header>
            <main class="workspace-content">
                @if (session('status'))<div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('status') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>@endif
                @if ($errors->any())<div class="alert alert-danger"><div class="fw-semibold">Please fix the highlighted fields.</div><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
                {{ $slot }}
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    @vite('resources/js/app.js')
</body>
</html>
