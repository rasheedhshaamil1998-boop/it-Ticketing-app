<x-layouts.app title="{{ $user->name }} - IT HELPDESK" heading="User Details">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="content-card p-4">
                <h2 class="h5 mb-3">{{ $user->name }}</h2>
                <dl class="row mb-4">
                    <dt class="col-5">Email</dt><dd class="col-7">{{ $user->email }}</dd>
                    <dt class="col-5">Department</dt><dd class="col-7">{{ $user->department }}</dd>
                    <dt class="col-5">Role</dt><dd class="col-7">{{ $user->roleLabel() }}</dd>
                    <dt class="col-5">Status</dt><dd class="col-7">{{ $user->is_active ? 'Active' : 'Inactive' }}</dd>
                    <dt class="col-5">Tickets</dt><dd class="col-7">{{ $user->tickets_count }}</dd>
                    <dt class="col-5">Registered</dt><dd class="col-7">{{ $user->created_at->format('M d, Y') }}</dd>
                </dl>
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label" for="role">Role</label>
                        <select class="form-select" id="role" name="role">
                            <option value="user" @selected($user->role === 'user')>User</option>
                            <option value="admin" @selected($user->role === 'admin')>Administrator</option>
                            <option value="superadmin" @selected($user->role === 'superadmin')>Super Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="is_active">Account Status</label>
                        <select class="form-select" id="is_active" name="is_active">
                            <option value="1" @selected($user->is_active)>Active</option>
                            <option value="0" @selected(! $user->is_active)>Inactive</option>
                        </select>
                    </div>
                    <button class="btn btn-primary w-100" type="submit"><i class="bi bi-save me-2"></i>Update User</button>
                </form>
            </div>
            <div class="content-card p-4 mt-4">
                <h2 class="h5 mb-3">Reset Password</h2>
                <p class="text-muted">Set a new password if this user has forgotten theirs. Share it with them securely after saving.</p>
                <form method="POST" action="{{ route('admin.users.password.update', $user) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label" for="password">New Password</label>
                        <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" autocomplete="new-password" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password_confirmation">Confirm New Password</label>
                        <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
                    </div>
                    <button class="btn btn-primary w-100" type="submit"><i class="bi bi-key me-2"></i>Reset Password</button>
                </form>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="content-card p-3">
                <h2 class="h5 mb-3">Submitted Tickets</h2>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Ticket</th>
                                <th>Subject</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tickets as $ticket)
                                <tr>
                                    <td class="fw-semibold">{{ $ticket->ticket_number }}</td>
                                    <td>{{ $ticket->subject }}</td>
                                    <td><span class="badge {{ $ticket->priorityBadgeClass() }}">{{ $ticket->priority }}</span></td>
                                    <td><span class="badge {{ $ticket->statusBadgeClass() }}">{{ $ticket->status }}</span></td>
                                    <td>{{ $ticket->created_at->format('M d, Y') }}</td>
                                    <td><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.tickets.show', $ticket) }}"><i class="bi bi-eye"></i></a></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">No tickets submitted.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $tickets->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>
