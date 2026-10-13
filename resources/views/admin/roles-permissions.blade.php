<x-layouts.app title="Roles and Permissions - IT HELPDESK" heading="Roles and Permissions">
    <section class="dashboard-subsection" id="roles-permissions">
        <div class="dashboard-section-heading d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <h2 class="h4 mb-1">Roles and Permissions</h2>
                <p class="text-muted mb-0">Dynamic role permissions and live account counts for the helpdesk access model.</p>
            </div>
            @if (auth()->user()->isSuperAdmin())
                <a class="btn btn-sm btn-primary" href="{{ route('admin.users.index') }}"><i class="bi bi-people me-2"></i>Manage User Access</a>
            @endif
        </div>

        <div class="row g-3">
            @foreach ($roles as $role)
                <div class="col-md-4">
                    <div class="role-permission-card p-3 h-100">
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="stat-icon bg-primary-subtle text-primary"><i class="bi {{ $role['icon'] }}"></i></span>
                                <div>
                                    <h3 class="h6 mb-0">{{ $role['name'] }}</h3>
                                    <small class="text-muted">{{ $role['badge'] }}</small>
                                </div>
                            </div>
                            <span class="badge text-bg-light">{{ $role['users_count'] }} {{ $role['users_count'] === 1 ? 'account' : 'accounts' }}</span>
                        </div>
                        <div class="d-flex flex-column gap-3">
                            @foreach ($role['groups'] as $group => $permissions)
                                <div class="permission-group">
                                    <div class="fw-semibold small mb-2">{{ $group }} Permissions</div>
                                    <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                                        @foreach ($permissions as $permission)
                                            <li class="d-flex gap-2 small text-muted">
                                                <i class="bi bi-check-circle text-success flex-shrink-0"></i>
                                                <span>{{ $permission }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</x-layouts.app>
