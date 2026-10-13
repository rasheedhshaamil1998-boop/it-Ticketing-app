<x-layouts.app title="Users - IT HELPDESK" heading="Users">
    <div class="d-flex justify-content-end mb-3">
        <a class="btn btn-primary" href="{{ route('admin.users.create') }}"><i class="bi bi-person-plus me-2"></i>Create User</a>
    </div>

    <div class="content-card p-3 mb-3">
        <form class="row g-2 align-items-end" method="GET">
            <div class="col-md-10">
                <label class="form-label" for="search">Search Users</label>
                <input class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Name, email, or department">
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-primary" type="submit"><i class="bi bi-search me-2"></i>Search</button>
            </div>
        </form>
    </div>
    <div class="content-card p-3">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Role</th>
                        <th>Account Status</th>
                        <th>Tickets</th>
                        <th>Registration Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td class="fw-semibold">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->department }}</td>
                            <td><span class="badge {{ $user->isSuperAdmin() ? 'text-bg-danger' : ($user->isAdmin() ? 'text-bg-primary' : 'text-bg-light') }}">{{ $user->roleLabel() }}</span></td>
                            <td><span class="badge {{ $user->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span></td>
                            <td>{{ $user->tickets_count }}</td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.users.show', $user) }}"><i class="bi bi-eye"></i></a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $users->links() }}
    </div>
</x-layouts.app>
