<x-layouts.app title="Admin Dashboard - IT HELPDESK" heading="Administrator Dashboard">
    @php
        $total = $statistics['Total Tickets'];
        $completed = $statistics['Resolved Tickets'] + $statistics['Closed Tickets'];
        $completionRate = $total > 0 ? (int) round($completed / $total * 100) : 0;
        $metrics = [
            ['label' => 'Total tickets', 'value' => $total, 'note' => 'Across all support requests', 'icon' => 'ticket-detailed', 'tone' => 'blue', 'filter' => []],
            ['label' => 'Open tickets', 'value' => $statistics['Open Tickets'], 'note' => 'Awaiting a response', 'icon' => 'inbox', 'tone' => 'amber', 'filter' => ['status' => 'Open']],
            ['label' => 'In progress', 'value' => $statistics['In Progress Tickets'], 'note' => 'Being worked on by your team', 'icon' => 'hourglass-split', 'tone' => 'violet', 'filter' => ['status' => 'In Progress']],
            ['label' => 'Resolved tickets', 'value' => $statistics['Resolved Tickets'], 'note' => $statistics['Closed Tickets'].' additional tickets closed', 'icon' => 'check2-circle', 'tone' => 'green', 'filter' => ['status' => 'Resolved']],
        ];
        $statusColors = ['Open' => 'amber', 'In Progress' => 'violet', 'Resolved' => 'green', 'Closed' => 'blue'];
        $activityMax = max(1, $activity->max('total'));
    @endphp
    <div class="operations-dashboard">
        <section class="overview-heading">
            <div>
                <div class="overview-eyebrow"><span></span> Support operations</div>
                <h2>Welcome back, {{ auth()->user()->name }}.</h2>
                <p>Here is what is happening with your helpdesk today.</p>
            </div>
            <div class="overview-actions">
                <span class="overview-date"><i class="bi bi-calendar3" aria-hidden="true"></i> {{ now()->format('D, d M Y') }}</span>
                <a class="btn btn-primary" href="{{ route('admin.reports.index') }}"><i class="bi bi-bar-chart me-2" aria-hidden="true"></i>View reports</a>
            </div>
        </section>

        <section class="overview-metrics" aria-label="Ticket overview">
            @foreach ($metrics as $metric)
                <a class="overview-metric tone-{{ $metric['tone'] }}" href="{{ route('admin.tickets.index', $metric['filter']) }}">
                    <div class="metric-top"><span>{{ $metric['label'] }}</span><span class="metric-symbol"><i class="bi bi-{{ $metric['icon'] }}" aria-hidden="true"></i></span></div>
                    <div class="metric-value">{{ number_format($metric['value']) }}</div>
                    <div class="metric-note">{{ $metric['note'] }}<i class="bi bi-arrow-up-right" aria-hidden="true"></i></div>
                </a>
            @endforeach
        </section>

        <div class="overview-main-grid">
            <section class="overview-panel activity-panel">
                <div class="panel-heading">
                    <div><h2>Ticket activity</h2><p>New requests over the last seven days</p></div>
                    <span class="period-label">Last 7 days</span>
                </div>
                <div class="activity-summary"><strong>{{ $activity->sum('total') }}</strong><span>tickets submitted</span></div>
                <div class="activity-chart" role="img" aria-label="Daily ticket submissions for the last seven days">
                    @foreach ($activity as $day)
                        <div class="activity-day">
                            <span class="activity-count">{{ $day['total'] }}</span>
                            <div class="activity-track"><div class="activity-bar" style="height: {{ $day['total'] / $activityMax * 100 }}%"></div></div>
                            <span class="activity-label">{{ $day['label'] }}<small>{{ $day['date'] }}</small></span>
                        </div>
                    @endforeach
                </div>
            </section>
            <section class="overview-panel">
                <div class="panel-heading"><div><h2>Tickets by Status</h2><p>Your queue at a glance</p></div><i class="bi bi-pie-chart text-muted" aria-hidden="true"></i></div>
                <div class="completion-summary"><div><strong>{{ $completionRate }}<small>%</small></strong><span>Completion rate</span></div><span class="completion-icon"><i class="bi bi-check2-all" aria-hidden="true"></i></span></div>
                <p class="completion-caption">{{ $completed }} of {{ $total }} tickets resolved or closed</p>
                <div class="status-stack" aria-hidden="true">
                    @foreach ($byStatus as $item)
                        <span class="tone-{{ $statusColors[$item->status] ?? 'blue' }}" style="width: {{ $total > 0 ? $item->total / $total * 100 : 0 }}%"></span>
                    @endforeach
                </div>
                <div class="status-legend">
                    @foreach ($statusColors as $status => $color)
                        <a href="{{ route('admin.tickets.index', ['status' => $status]) }}"><span><i class="status-dot tone-{{ $color }}" aria-hidden="true"></i>{{ $status }}</span><strong>{{ $byStatus->firstWhere('status', $status)?->total ?? 0 }}</strong></a>
                    @endforeach
                </div>
            </section>
        </div>

        <section class="queue-strip" aria-label="Queue shortcuts">
            <div><span class="queue-symbol"><i class="bi bi-lightning-charge" aria-hidden="true"></i></span><div><strong>Keep your queue moving</strong><span>{{ $statistics['New Tickets'] }} new open tickets today · {{ $statistics['Critical Tickets'] }} critical-priority tickets overall</span></div></div>
            <a href="{{ route('admin.tickets.index', ['priority' => 'Critical']) }}">Review critical tickets <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
        </section>

        <section class="overview-panel recent-panel">
            <div class="panel-heading"><div><h2>Latest Submitted Tickets</h2><p>The eight most recent support requests</p></div><a class="panel-link" href="{{ route('admin.tickets.index') }}">All tickets <i class="bi bi-arrow-right" aria-hidden="true"></i></a></div>
            <div class="table-responsive">
                <table class="table align-middle overview-table">
                    <thead><tr><th>Ticket / Subject</th><th>Created By</th><th>Priority</th><th>Status</th><th>Assigned To</th><th><span class="visually-hidden">Actions</span></th></tr></thead>
                    <tbody>
                        @forelse ($recentTickets as $ticket)
                            <tr>
                                <td><a class="ticket-subject" href="{{ route('admin.tickets.show', $ticket) }}">{{ $ticket->subject }}</a><span class="ticket-meta">{{ $ticket->ticket_number }} <span>·</span> {{ $ticket->created_at->diffForHumans() }}</span></td>
                                <td>{{ $ticket->user->name }}</td>
                                <td><span class="badge {{ $ticket->priorityBadgeClass() }}">{{ $ticket->priority }}</span></td>
                                <td><span class="badge {{ $ticket->statusBadgeClass() }}">{{ $ticket->status }}</span></td>
                                <td><span class="{{ $ticket->assignee ? '' : 'text-muted' }}">{{ $ticket->assignee?->name ?? 'Unassigned' }}</span></td>
                                <td><a class="ticket-open" href="{{ route('admin.tickets.show', $ticket) }}" aria-label="View ticket {{ $ticket->ticket_number }}"><i class="bi bi-arrow-up-right" aria-hidden="true"></i></a></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-5"><i class="bi bi-inbox fs-2 text-muted" aria-hidden="true"></i><div class="mt-2 fw-semibold">Your queue is clear</div><p class="text-muted mb-0">New support requests will appear here.</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div class="overview-breakdowns">
            <section class="overview-panel">
                <div class="panel-heading"><div><h2>Tickets by Category</h2><p>Where your team is needed most</p></div><i class="bi bi-grid text-muted" aria-hidden="true"></i></div>
                @forelse ($byCategory as $item)
                    <a class="breakdown-row" href="{{ route('admin.tickets.index', ['category' => $item->category]) }}"><div><span>{{ $item->category }}</span><strong>{{ $item->total }}</strong></div><div class="breakdown-track"><span style="width: {{ $total > 0 ? $item->total / $total * 100 : 0 }}%"></span></div></a>
                @empty
                    <p class="text-muted mb-0">No category data yet.</p>
                @endforelse
            </section>
            <section class="overview-panel">
                <div class="panel-heading"><div><h2>Tickets by Priority</h2><p>All requests, grouped by urgency</p></div><i class="bi bi-flag text-muted" aria-hidden="true"></i></div>
                @forelse ($byPriority->sortBy(fn ($item) => array_search($item->priority, array_reverse(\App\Models\Ticket::PRIORITIES))) as $item)
                    <a class="priority-row" href="{{ route('admin.tickets.index', ['priority' => $item->priority]) }}"><span><i class="bi bi-flag" aria-hidden="true"></i>{{ $item->priority }}</span><div><strong>{{ $item->total }}</strong><span>{{ $total > 0 ? round($item->total / $total * 100) : 0 }}%</span><i class="bi bi-chevron-right" aria-hidden="true"></i></div></a>
                @empty
                    <p class="text-muted mb-0">No priority data yet.</p>
                @endforelse
                <a class="priority-footer" href="{{ route('admin.tickets.index', ['status' => 'Open']) }}">Go to the open queue <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </section>
        </div>
        <footer class="overview-footer"><span>IT HELPDESK <span> / </span> Maldives Immigration</span><span>Built around better support.</span></footer>
    </div>
</x-layouts.app>
