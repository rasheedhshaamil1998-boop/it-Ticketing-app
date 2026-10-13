<x-layouts.app title="Dashboard - IT HELPDESK" heading="User Dashboard">
    <div class="dashboard-hero mb-4">
        <div>
            <div class="text-uppercase small fw-semibold mb-2">Support Center</div>
            <h2 class="display-6 fw-bold mb-2">How can IT help today?</h2>
            <p class="mb-0">Submit a support request, track progress, and continue the conversation with the helpdesk team.</p>
        </div>
        <a href="{{ route('tickets.create') }}" class="btn btn-light btn-lg"><i class="bi bi-plus-circle me-2"></i>Create New Ticket</a>
    </div>
    <div class="row g-3 mb-4">
        @foreach ($statistics as $label => $value)
            <div class="col-sm-6 col-xl">
                <div class="stat-card stat-card-modern p-3 h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small">{{ $label }}</div>
                            <div class="fs-3 fw-bold">{{ $value }}</div>
                        </div>
                        <span class="stat-icon"><i class="bi bi-ticket-detailed"></i></span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="content-card content-card-modern p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 mb-0">Recent Tickets</h2>
            <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Ticket Number</th>
                        <th>Subject</th>
                        <th>Category</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentTickets as $ticket)
                        <tr>
                            <td class="fw-semibold">{{ $ticket->ticket_number }}</td>
                            <td>{{ $ticket->subject }}</td>
                            <td>{{ $ticket->category }}</td>
                            <td><span class="badge {{ $ticket->priorityBadgeClass() }}">{{ $ticket->priority }}</span></td>
                            <td><span class="badge {{ $ticket->statusBadgeClass() }}">{{ $ticket->status }}</span></td>
                            <td>{{ $ticket->created_at->format('M d, Y') }}</td>
                            <td><a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No tickets submitted yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
