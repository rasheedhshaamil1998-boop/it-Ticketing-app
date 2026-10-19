<x-layouts.app title="Dashboard - IT HELPDESK" heading="My Dashboard">
    <div class="operations-dashboard enterprise-dashboard employee-dashboard">
        <section class="overview-heading"><div><div class="overview-eyebrow"><span></span> Your support centre</div><h2>Hello, {{ auth()->user()->name }}.</h2><p>Track your requests and get the support you need.</p></div><a class="btn btn-primary" href="{{ route('tickets.create') }}"><i class="bi bi-plus-lg me-2" aria-hidden="true"></i>Create New Ticket</a></section>
        <section class="overview-metrics" aria-label="My ticket statistics">
            <x-dashboard-stat label="My Tickets" :value="$statistics['Total Tickets']" note="All your submitted requests" icon="ticket-detailed" tone="blue" :href="route('tickets.index')" />
            <x-dashboard-stat label="Open Requests" :value="$statistics['Open Tickets']" note="Waiting for a response" icon="inbox" tone="amber" :href="route('tickets.index', ['status' => 'Open'])" />
            <x-dashboard-stat label="In Progress" :value="$statistics['In Progress Tickets']" note="Your team is working on it" icon="hourglass-split" tone="violet" :href="route('tickets.index', ['status' => 'In Progress'])" />
            <x-dashboard-stat label="Resolved Requests" :value="$statistics['Resolved Tickets']" note="Successfully resolved" icon="check2-circle" tone="green" :href="route('tickets.index', ['status' => 'Resolved'])" />
            <x-dashboard-stat label="Closed Requests" :value="$statistics['Closed Tickets']" note="Completed support requests" icon="archive" tone="cyan" :href="route('tickets.index', ['status' => 'Closed'])" />
        </section>
        <section class="dashboard-quick-actions" aria-label="Quick actions"><span>Quick actions</span><a href="{{ route('tickets.create') }}"><i class="bi bi-plus-circle" aria-hidden="true"></i>Create New Ticket</a><a href="{{ route('tickets.index', ['status' => 'Open']) }}"><i class="bi bi-inbox" aria-hidden="true"></i>View Open Requests</a><a href="{{ route('profile') }}"><i class="bi bi-person-circle" aria-hidden="true"></i>My Profile</a></section>
        <x-recent-tickets :tickets="$recentTickets" :admin="false" title="My Recent Tickets" />
        <footer class="overview-footer"><span>IT HELPDESK / Maldives Immigration</span><span>Your requests, always in view.</span></footer>
    </div>
</x-layouts.app>
