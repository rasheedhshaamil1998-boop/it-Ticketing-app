@props(['tickets', 'admin' => true, 'title' => 'Latest Submitted Tickets'])
<section class="overview-panel recent-panel" data-ticket-table>
    <div class="panel-heading"><div><h2>{{ $title }}</h2><p>The {{ $tickets->count() }} most recent support requests</p></div><a class="panel-link" href="{{ route($admin ? 'admin.tickets.index' : 'tickets.index') }}">All tickets <i class="bi bi-arrow-right" aria-hidden="true"></i></a></div>
    <div class="recent-toolbar">
        <label class="table-search"><i class="bi bi-search" aria-hidden="true"></i><span class="visually-hidden">Search recent tickets</span><input type="search" data-ticket-search placeholder="Search these {{ $tickets->count() }} tickets" autocomplete="off"></label>
        <label class="table-sort"><span>Sort by</span><select class="form-select form-select-sm" data-ticket-sort><option value="newest">Newest first</option><option value="oldest">Oldest first</option><option value="subject">Subject A&ndash;Z</option><option value="priority">Priority: highest first</option></select></label>
    </div>
    <div class="table-responsive">
        <table class="table align-middle overview-table">
            <thead><tr><th>Ticket ID</th><th>Subject</th><th>Created By</th><th>Department</th><th>Priority</th><th>Status</th><th>Assigned Technician</th><th>Created Date</th><th>Actions</th></tr></thead>
            <tbody>
                @foreach ($tickets as $ticket)
                    <tr data-ticket-row data-created="{{ $ticket->created_at->timestamp }}" data-priority="{{ array_search($ticket->priority, \App\Models\Ticket::PRIORITIES) }}">
                        <td><a class="ticket-id" href="{{ route($admin ? 'admin.tickets.show' : 'tickets.show', $ticket) }}">{{ $ticket->ticket_number }}</a></td>
                        <td><span class="ticket-subject" data-ticket-subject>{{ $ticket->subject }}</span><span class="ticket-meta">{{ $ticket->category }}</span></td>
                        <td>{{ $ticket->user->name }}</td><td>{{ $ticket->department }}</td>
                        <td><span class="badge {{ $ticket->priorityBadgeClass() }}">{{ $ticket->priority }}</span></td>
                        <td><span class="badge {{ $ticket->statusBadgeClass() }}">{{ $ticket->status }}</span></td>
                        <td><span class="{{ $ticket->assignee ? '' : 'text-muted' }}">{{ $ticket->assignee?->name ?? 'Unassigned' }}</span></td>
                        <td><time datetime="{{ $ticket->created_at->toIso8601String() }}">{{ $ticket->created_at->format('d M Y') }}</time></td>
                        <td><a class="ticket-open" href="{{ route($admin ? 'admin.tickets.show' : 'tickets.show', $ticket) }}" aria-label="View ticket {{ $ticket->ticket_number }}" title="View ticket"><i class="bi bi-arrow-up-right" aria-hidden="true"></i></a></td>
                    </tr>
                @endforeach
                <tr data-ticket-empty @if ($tickets->isNotEmpty()) hidden @endif><td colspan="9" class="text-center py-5"><i class="bi bi-inbox fs-2 text-muted" aria-hidden="true"></i><div class="mt-2 fw-semibold" data-empty-title>Your queue is clear</div><p class="text-muted mb-0" data-empty-message>New support requests will appear here.</p></td></tr>
            </tbody>
        </table>
    </div>
    <div class="table-footer"><span data-table-count role="status" aria-live="polite">Showing {{ $tickets->count() }} recent tickets</span><span>Search all requests from the navigation bar.</span></div>
</section>
