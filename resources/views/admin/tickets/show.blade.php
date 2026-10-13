<x-layouts.app title="{{ $ticket->ticket_number }} - Admin - IT HELPDESK" heading="Manage Ticket">
    @include('tickets._details', ['ticket' => $ticket])

    <div class="row g-4 mt-1">
        <div class="col-lg-6">
            <div class="content-card p-4 h-100">
                <h2 class="h5 mb-3">Admin Controls</h2>
                <form method="POST" action="{{ route('admin.tickets.update', $ticket) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label" for="status">Status</label>
                        <select class="form-select" id="status" name="status">
                            @foreach (App\Models\Ticket::STATUSES as $status)
                                <option value="{{ $status }}" @selected($ticket->status === $status)>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="priority">Priority</label>
                        <select class="form-select" id="priority" name="priority">
                            @foreach (App\Models\Ticket::PRIORITIES as $priority)
                                <option value="{{ $priority }}" @selected($ticket->priority === $priority)>{{ $priority }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="assigned_to">Assign To</label>
                        <select class="form-select" id="assigned_to" name="assigned_to">
                            <option value="">Unassigned</option>
                            @foreach ($administrators as $administrator)
                                <option value="{{ $administrator->id }}" @selected($ticket->assigned_to === $administrator->id)>{{ $administrator->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-primary w-100" type="submit"><i class="bi bi-save me-2"></i>Update Ticket</button>
                </form>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="content-card p-4 h-100">
                <h2 class="h5 mb-3">Resolution</h2>
                <form method="POST" action="{{ route('admin.tickets.resolve', $ticket) }}" class="mb-3">
                    @csrf
                    <textarea class="form-control mb-3" name="resolution_notes" rows="4" placeholder="Resolution description" required>{{ old('resolution_notes', $ticket->resolution_notes) }}</textarea>
                    <button class="btn btn-success w-100" type="submit"><i class="bi bi-check2-circle me-2"></i>Resolve Ticket</button>
                </form>
                <div class="d-grid gap-2">
                    <form method="POST" action="{{ route('admin.tickets.close', $ticket) }}">
                        @csrf
                        <button class="btn btn-secondary w-100" type="submit"><i class="bi bi-archive me-2"></i>Close Ticket</button>
                    </form>
                    <form method="POST" action="{{ route('admin.tickets.reopen', $ticket) }}">
                        @csrf
                        <button class="btn btn-outline-primary w-100" type="submit"><i class="bi bi-arrow-counterclockwise me-2"></i>Reopen Ticket</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
