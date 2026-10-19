<x-layouts.app title="Admin Dashboard - IT HELPDESK" heading="Dashboard">
    @php
        $total = $statistics['Total Tickets'];
        $completed = $statistics['Resolved Tickets'] + $statistics['Closed Tickets'];
        $completionRate = $total > 0 ? (int) round($completed / $total * 100) : 0;
    @endphp
    <div class="operations-dashboard enterprise-dashboard">
        <section class="overview-heading">
            <div><div class="overview-eyebrow"><span></span> {{ auth()->user()->isSuperAdmin() ? 'Administration overview' : 'Support operations' }}</div><h2>Your helpdesk, at a glance.</h2><p>Welcome back, {{ auth()->user()->name }}. Here is what needs your attention.</p></div>
            <div class="overview-actions"><span class="overview-date"><i class="bi bi-calendar3" aria-hidden="true"></i> {{ now()->format('D, d M Y') }}</span><a class="btn btn-primary" href="{{ route('tickets.create') }}"><i class="bi bi-plus-lg me-2" aria-hidden="true"></i>Create New Ticket</a></div>
        </section>
        <section class="overview-metrics" aria-label="Helpdesk statistics">
            <x-dashboard-stat label="Total Tickets" :value="$total" note="All support requests" icon="ticket-detailed" tone="blue" :href="route('admin.tickets.index')" />
            <x-dashboard-stat label="Open Tickets" :value="$statistics['Open Tickets']" note="Awaiting action" icon="inbox" tone="amber" :href="route('admin.tickets.index', ['status' => 'Open'])" />
            <x-dashboard-stat label="In Progress" :value="$statistics['In Progress Tickets']" note="Being handled by your team" icon="hourglass-split" tone="violet" :href="route('admin.tickets.index', ['status' => 'In Progress'])" />
            <x-dashboard-stat label="Resolved Tickets" :value="$statistics['Resolved Tickets']" note="Successfully resolved" icon="check2-circle" tone="green" :href="route('admin.tickets.index', ['status' => 'Resolved'])" />
            <x-dashboard-stat label="High Priority" :value="$statistics['High Priority']" note="High-priority requests" icon="exclamation-triangle" tone="red" :href="route('admin.tickets.index', ['priority' => 'High'])" />
            @if (auth()->user()->isSuperAdmin())
                <x-dashboard-stat label="Total Users" :value="$totalUsers" note="Registered staff accounts" icon="people" tone="cyan" :href="route('admin.users.index')" />
            @endif
        </section>
        <section class="dashboard-quick-actions" aria-label="Quick actions"><span>Quick actions</span><a href="{{ route('admin.tickets.index', ['status' => 'Open']) }}"><i class="bi bi-inbox" aria-hidden="true"></i>View Open Tickets</a><a href="{{ route('admin.tickets.index', ['assigned' => 'me']) }}"><i class="bi bi-person-check" aria-hidden="true"></i>View Assigned Tickets</a>@if (auth()->user()->isSuperAdmin())<a href="{{ route('admin.users.index') }}"><i class="bi bi-people" aria-hidden="true"></i>Manage Users</a>@endif<a href="{{ route('admin.reports.index') }}"><i class="bi bi-file-earmark-bar-graph" aria-hidden="true"></i>Generate Reports</a></section>
        <div class="dashboard-chart-grid">
            <x-dashboard-chart title="Monthly Ticket Activity" subtitle="Ticket activity over the last six months" type="line" :data="$chartData['monthly']" class="monthly-panel" />
            <x-dashboard-chart title="Tickets by Status" subtitle="Current distribution across all tickets" type="doughnut" :data="$chartData['status']" filter="status" />
            <x-dashboard-chart title="Priority Distribution" subtitle="Requests grouped by urgency" type="bar" :data="$chartData['priority']" filter="priority" />
        </div>
        <section class="queue-strip"><div><span class="queue-symbol"><i class="bi bi-activity" aria-hidden="true"></i></span><div><strong>{{ $completionRate }}% completion rate</strong><span>{{ $completed }} resolved or closed &middot; {{ $statistics['New Tickets'] }} new open tickets today &middot; {{ $activity->sum('total') }} submitted in the last seven days</span></div></div><a href="{{ route('admin.tickets.index', ['priority' => 'Critical']) }}">{{ $statistics['Critical Tickets'] }} critical tickets <i class="bi bi-arrow-right" aria-hidden="true"></i></a></section>
        <x-recent-tickets :tickets="$recentTickets" />
        <div class="overview-breakdowns">
            <section class="overview-panel"><div class="panel-heading"><div><h2>Your assigned work</h2><p>Your technician queue and recent assignments</p></div><a class="panel-link" href="{{ route('admin.tickets.index', ['assigned' => 'me']) }}">View queue <i class="bi bi-arrow-right" aria-hidden="true"></i></a></div><div class="assigned-statistics">@foreach (['Open' => 'Pending', 'In Progress' => 'In progress', 'Resolved' => 'Resolved', 'Closed' => 'Closed'] as $status => $label)<a href="{{ route('admin.tickets.index', ['assigned' => 'me', 'status' => $status]) }}"><strong>{{ $assignedStatistics[$status] ?? 0 }}</strong><span>{{ $label }}</span></a>@endforeach</div><div class="assignment-list">@forelse ($assignedTickets as $ticket)<a href="{{ route('admin.tickets.show', $ticket) }}"><span><strong>{{ $ticket->subject }}</strong><small>{{ $ticket->ticket_number }} &middot; {{ $ticket->user->name }}</small></span><span class="badge {{ $ticket->statusBadgeClass() }}">{{ $ticket->status }}</span></a>@empty<p class="text-muted mb-0">No tickets assigned to you yet.</p>@endforelse</div></section>
            <section class="overview-panel"><div class="panel-heading"><div><h2>Tickets by Category</h2><p>Where your team is needed most</p></div><i class="bi bi-grid text-muted" aria-hidden="true"></i></div>@forelse ($byCategory as $item)<a class="breakdown-row" href="{{ route('admin.tickets.index', ['category' => $item->category]) }}"><div><span>{{ $item->category }}</span><strong>{{ $item->total }}</strong></div><div class="breakdown-track"><span style="width: {{ $total > 0 ? $item->total / $total * 100 : 0 }}%"></span></div></a>@empty<p class="text-muted mb-0">No category data yet.</p>@endforelse</section>
        </div>
        <footer class="overview-footer"><span>IT HELPDESK / Maldives Immigration</span><span>Support that keeps your team moving.</span></footer>
    </div>
    @vite('resources/js/dashboard.js')
</x-layouts.app>
