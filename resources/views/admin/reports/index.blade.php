<x-layouts.app title="Reports - IT HELPDESK" heading="Reports">
    <div class="content-card p-3 mb-4">
        <form class="row g-2 align-items-end" method="GET">
            <div class="col-md-4">
                <label class="form-label" for="from">From</label>
                <input class="form-control" id="from" name="from" type="date" value="{{ request('from') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="to">To</label>
                <input class="form-control" id="to" name="to" type="date" value="{{ request('to') }}">
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-primary" type="submit"><i class="bi bi-funnel me-2"></i>Apply</button>
            </div>
            <div class="col-md-2 d-grid">
                <a class="btn btn-outline-primary" href="{{ route('admin.reports.export', request()->query()) }}"><i class="bi bi-download me-2"></i>CSV</a>
            </div>
        </form>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="stat-card p-3">
                <div class="text-muted small">Total Tickets</div>
                <div class="fs-3 fw-bold">{{ $totalTickets }}</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card p-3">
                <div class="text-muted small">Average Resolution Time</div>
                <div class="fs-3 fw-bold">{{ $averageResolutionHours ? number_format($averageResolutionHours, 1).' hrs' : 'N/A' }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        @foreach (['Tickets by Category' => $byCategory, 'Tickets by Status' => $byStatus, 'Tickets by Priority' => $byPriority, 'Tickets by Department' => $byDepartment] as $title => $items)
            <div class="col-lg-6">
                <div class="content-card p-3 h-100">
                    <h2 class="h5">{{ $title }}</h2>
                    @foreach ($items as $item)
                        @php($label = $item->category ?? $item->status ?? $item->priority ?? $item->department)
                        <div class="d-flex justify-content-between border-bottom py-2"><span>{{ $label }}</span><strong>{{ $item->total }}</strong></div>
                    @endforeach
                </div>
            </div>
        @endforeach
        <div class="col-lg-6">
            <div class="content-card p-3 h-100">
                <h2 class="h5">Tickets Resolved by Administrator</h2>
                @forelse ($resolvedBy as $name => $tickets)
                    <div class="d-flex justify-content-between border-bottom py-2"><span>{{ $name ?: 'Unknown' }}</span><strong>{{ $tickets->count() }}</strong></div>
                @empty
                    <p class="text-muted mb-0">No resolved tickets in this date range.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>
