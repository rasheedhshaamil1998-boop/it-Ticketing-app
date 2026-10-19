<x-layouts.app title="Profile - IT HELPDESK" heading="Profile">
    <div class="content-card p-4">
        <dl class="row mb-0">
            <dt class="col-sm-3">Name</dt>
            <dd class="col-sm-9">{{ auth()->user()->name }}</dd>
            <dt class="col-sm-3">Email</dt>
            <dd class="col-sm-9">{{ auth()->user()->email }}</dd>
            <dt class="col-sm-3">Department</dt>
            <dd class="col-sm-9">{{ auth()->user()->department }}</dd>
            <dt class="col-sm-3">Record Card Number</dt>
            <dd class="col-sm-9">{{ auth()->user()->record_card_number ?? 'Not provided' }}</dd>
            <dt class="col-sm-3">Role</dt>
            <dd class="col-sm-9">{{ auth()->user()->roleLabel() }}</dd>
            <dt class="col-sm-3">Account Status</dt>
            <dd class="col-sm-9">{{ auth()->user()->is_active ? 'Active' : 'Inactive' }}</dd>
        </dl>
    </div>
</x-layouts.app>
