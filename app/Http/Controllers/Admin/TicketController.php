<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminResolveTicketRequest;
use App\Http\Requests\AdminUpdateTicketRequest;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $tickets = Ticket::query()
            ->with(['user', 'assignee'])
            ->when($request->string('assigned')->toString() === 'me', fn ($query) => $query->where('assigned_to', $request->user()->id))
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();

                $query->where(function ($query) use ($search): void {
                    $query->where('ticket_number', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($query) => $query->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('priority'), fn ($query) => $query->where('priority', $request->string('priority')))
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->string('category')))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.tickets.index', ['tickets' => $tickets]);
    }

    public function create(): never
    {
        abort(404);
    }

    public function store(Request $request): never
    {
        abort(404);
    }

    public function show(Ticket $ticket): View
    {
        $ticket->load(['user', 'assignee', 'resolver', 'attachments.uploader', 'comments.user']);
        $administrators = User::query()
            ->whereIn('role', ['admin', 'superadmin'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.tickets.show', compact('ticket', 'administrators'));
    }

    public function edit(Ticket $ticket): never
    {
        abort(404);
    }

    public function update(AdminUpdateTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        $ticket->update($request->validated());

        return back()->with('status', 'Ticket updated successfully.');
    }

    public function destroy(Ticket $ticket): never
    {
        abort(404);
    }

    public function resolve(AdminResolveTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        $ticket->update([
            'status' => 'Resolved',
            'resolution_notes' => $request->validated('resolution_notes'),
            'resolved_by' => $request->user()->id,
            'resolved_at' => now(),
        ]);

        $ticket->comments()->create([
            'user_id' => $request->user()->id,
            'message' => 'Resolution: '.$request->validated('resolution_notes'),
        ]);

        return back()->with('status', 'Ticket resolved successfully.');
    }

    public function close(Ticket $ticket): RedirectResponse
    {
        $ticket->update(['status' => 'Closed']);

        return back()->with('status', 'Ticket closed successfully.');
    }

    public function reopen(Ticket $ticket): RedirectResponse
    {
        $ticket->update([
            'status' => 'Open',
            'resolution_notes' => null,
            'resolved_by' => null,
            'resolved_at' => null,
        ]);

        return back()->with('status', 'Ticket reopened successfully.');
    }
}
