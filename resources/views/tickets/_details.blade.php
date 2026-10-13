<div class="row g-4">
    <div class="col-xl-8">
        <div class="content-card p-4 mb-4">
            <div class="d-flex flex-wrap justify-content-between gap-3 mb-3">
                <div>
                    <div class="text-muted small">Ticket Number</div>
                    <h2 class="h4 mb-0">{{ $ticket->ticket_number }}</h2>
                </div>
                <div class="d-flex gap-2 align-items-start">
                    <span class="badge {{ $ticket->priorityBadgeClass() }}">{{ $ticket->priority }}</span>
                    <span class="badge {{ $ticket->statusBadgeClass() }}">{{ $ticket->status }}</span>
                </div>
            </div>
            <h3 class="h5">{{ $ticket->subject }}</h3>
            <p class="mb-0">{{ $ticket->description }}</p>
        </div>

        <div class="content-card p-4">
            <h3 class="h5 mb-3">Conversation</h3>
            <div class="d-flex flex-column gap-3 mb-4">
                @forelse ($ticket->comments as $comment)
                    <div class="conversation-item ps-3">
                        <div class="d-flex justify-content-between gap-3">
                            <div class="fw-semibold">{{ $comment->user->name }} <span class="badge text-bg-light">{{ $comment->user->roleLabel() }}</span></div>
                            <small class="text-muted">{{ $comment->created_at->format('M d, Y h:i A') }}</small>
                        </div>
                        <div>{{ $comment->message }}</div>
                    </div>
                @empty
                    <p class="text-muted mb-0">No comments yet.</p>
                @endforelse
            </div>
            <form method="POST" action="{{ route('tickets.comments.store', $ticket) }}">
                @csrf
                <label class="form-label" for="message">Add Comment</label>
                <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="4" required>{{ old('message') }}</textarea>
                @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="text-end mt-3">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-chat-dots me-2"></i>Add Comment</button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="content-card p-4 mb-4">
            <h3 class="h5 mb-3">Ticket Information</h3>
            <dl class="row mb-0">
                <dt class="col-5">Category</dt><dd class="col-7">{{ $ticket->category }}</dd>
                <dt class="col-5">Location</dt><dd class="col-7">{{ $ticket->location }}</dd>
                <dt class="col-5">Department</dt><dd class="col-7">{{ $ticket->department }}</dd>
                <dt class="col-5">Created By</dt><dd class="col-7">{{ $ticket->user->name }}</dd>
                <dt class="col-5">Created</dt><dd class="col-7">{{ $ticket->created_at->format('M d, Y h:i A') }}</dd>
                <dt class="col-5">Last Updated</dt><dd class="col-7">{{ $ticket->updated_at->format('M d, Y h:i A') }}</dd>
                <dt class="col-5">Assigned To</dt><dd class="col-7">{{ $ticket->assignee?->name ?? 'Unassigned' }}</dd>
                <dt class="col-5">Resolved By</dt><dd class="col-7">{{ $ticket->resolver?->name ?? 'Not resolved' }}</dd>
                <dt class="col-5">Resolved At</dt><dd class="col-7">{{ $ticket->resolved_at?->format('M d, Y h:i A') ?? 'Not resolved' }}</dd>
            </dl>
            @if ($ticket->resolution_notes)
                <hr>
                <div class="fw-semibold">Resolution Notes</div>
                <p class="mb-0">{{ $ticket->resolution_notes }}</p>
            @endif
        </div>
        <div class="content-card p-4">
            <h3 class="h5 mb-3">Attachments</h3>
            @forelse ($ticket->attachments as $attachment)
                <a class="d-flex align-items-center gap-2 mb-2" href="{{ route('attachments.show', $attachment) }}">
                    <i class="bi bi-paperclip"></i>
                    <span>{{ $attachment->original_filename }}</span>
                </a>
            @empty
                <p class="text-muted mb-0">No attachments uploaded.</p>
            @endforelse
        </div>
    </div>
</div>
