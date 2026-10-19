<x-layouts.app title="All Tickets - IT HELPDESK" heading="All Tickets">
    @php
        $activeFilters = collect(['search', 'status', 'priority', 'category', 'assigned'])
            ->filter(fn ($filter) => request()->filled($filter))
            ->count();
    @endphp

    <div class="content-card p-3 p-lg-4 mb-3">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-3">
            <div>
                <div class="text-muted small fw-semibold text-uppercase">Ticket Queue</div>
                <h2 class="h5 mb-1">Review and manage support tickets</h2>
                <p class="text-muted mb-0">Showing {{ $tickets->firstItem() ?? 0 }}-{{ $tickets->lastItem() ?? 0 }} of {{ $tickets->total() }} tickets.</p>
            </div>
            <div class="d-flex flex-wrap gap-2 align-items-start">
                @if ($activeFilters > 0)
                    <span class="badge text-bg-light admin-filter-count">{{ $activeFilters }} active {{ $activeFilters === 1 ? 'filter' : 'filters' }}</span>
                @endif
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.tickets.index') }}">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                </a>
            </div>
        </div>

        <form class="row g-2 align-items-end" method="GET">
            @if (request('assigned') === 'me')
                <input type="hidden" name="assigned" value="me">
                <div class="col-12"><span class="badge text-bg-primary">Assigned to me</span></div>
            @endif
            <div class="col-md-3">
                <label class="form-label" for="search">Search</label>
                <input class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Ticket, subject, user">
            </div>
            <div class="col-md-2">
                <label class="form-label" for="status">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All</option>
                    @foreach (App\Models\Ticket::STATUSES as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label" for="priority">Priority</label>
                <select class="form-select" id="priority" name="priority">
                    <option value="">All</option>
                    @foreach (App\Models\Ticket::PRIORITIES as $priority)
                        <option value="{{ $priority }}" @selected(request('priority') === $priority)>{{ $priority }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="category">Category</label>
                <select class="form-select" id="category" name="category">
                    <option value="">All</option>
                    @foreach (App\Models\Ticket::CATEGORIES as $category)
                        <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-primary" type="submit"><i class="bi bi-search me-2"></i>Filter</button>
            </div>
        </form>
    </div>
    <div class="content-card p-3 p-lg-4">
        <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3">
            <div>
                <h2 class="h5 mb-1">All Tickets</h2>
                <p class="text-muted mb-0">Newest tickets appear first.</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle admin-ticket-table">
                <thead>
                    <tr>
                        <th>Ticket</th>
                        <th>Subject</th>
                        <th>Created By</th>
                        <th>Category</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tickets as $ticket)
                        <tr>
                            <td class="fw-semibold">{{ $ticket->ticket_number }}</td>
                            <td class="ticket-subject-cell">{{ $ticket->subject }}</td>
                            <td>
                                <div class="fw-semibold">{{ $ticket->user->name }}</div>
                                <div class="small text-muted">{{ $ticket->department }}</div>
                            </td>
                            <td>{{ $ticket->category }}</td>
                            <td><span class="badge {{ $ticket->priorityBadgeClass() }}">{{ $ticket->priority }}</span></td>
                            <td><span class="badge {{ $ticket->statusBadgeClass() }}">{{ $ticket->status }}</span></td>
                            <td>{{ $ticket->created_at->format('M d, Y') }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.tickets.show', $ticket) }}" aria-label="View {{ $ticket->ticket_number }}">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="empty-state mx-auto">
                                    <div class="empty-state-icon mb-2"><i class="bi bi-inbox"></i></div>
                                    <div class="fw-semibold">No tickets found</div>
                                    <div class="text-muted">Try changing or resetting the filters.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrap">
            {{ $tickets->links() }}
        </div>
    </div>
</x-layouts.app>
