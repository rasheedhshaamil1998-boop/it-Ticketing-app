<x-layouts.app title="Admin Dashboard - IT HELPDESK" heading="Administrator Dashboard">
    <div class="row g-3 mb-4">
        @foreach ($statistics as $label => $value)
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card p-3 h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small">{{ $label }}</div>
                            <div class="fs-3 fw-bold">{{ $value }}</div>
                        </div>
                        <span class="stat-icon bg-primary-subtle text-primary"><i class="bi bi-graph-up"></i></span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="content-card p-3 h-100">
                <h2 class="h5">Tickets by Category</h2>
                @foreach ($byCategory as $item)
                    <div class="d-flex justify-content-between border-bottom py-2"><span>{{ $item->category }}</span><strong>{{ $item->total }}</strong></div>
                @endforeach
            </div>
        </div>
        <div class="col-lg-4">
            <div class="content-card p-3 h-100">
                <h2 class="h5">Tickets by Status</h2>
                @foreach ($byStatus as $item)
                    <div class="d-flex justify-content-between border-bottom py-2"><span>{{ $item->status }}</span><strong>{{ $item->total }}</strong></div>
                @endforeach
            </div>
        </div>
        <div class="col-lg-4">
            <div class="content-card p-3 h-100">
                <h2 class="h5">Tickets by Priority</h2>
                @foreach ($byPriority as $item)
                    <div class="d-flex justify-content-between border-bottom py-2"><span>{{ $item->priority }}</span><strong>{{ $item->total }}</strong></div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="content-card p-3">
        <h2 class="h5 mb-3">Latest Submitted Tickets</h2>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Ticket</th>
                        <th>Subject</th>
                        <th>Created By</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Assigned To</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recentTickets as $ticket)
                        <tr>
                            <td class="fw-semibold">{{ $ticket->ticket_number }}</td>
                            <td>{{ $ticket->subject }}</td>
                            <td>{{ $ticket->user->name }}</td>
                            <td><span class="badge {{ $ticket->priorityBadgeClass() }}">{{ $ticket->priority }}</span></td>
                            <td><span class="badge {{ $ticket->statusBadgeClass() }}">{{ $ticket->status }}</span></td>
                            <td>{{ $ticket->assignee?->name ?? 'Unassigned' }}</td>
                            <td><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.tickets.show', $ticket) }}"><i class="bi bi-eye"></i></a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
