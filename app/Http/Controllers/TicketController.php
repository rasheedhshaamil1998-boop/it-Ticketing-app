<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $tickets = Ticket::query()
            ->whereBelongsTo($request->user())
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();

                $query->where(function ($query) use ($search): void {
                    $query->where('ticket_number', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('priority'), fn ($query) => $query->where('priority', $request->string('priority')))
            ->when($request->string('sort')->toString() === 'oldest', fn ($query) => $query->oldest(), fn ($query) => $query->latest())
            ->paginate(10)
            ->withQueryString();

        return view('tickets.index', ['tickets' => $tickets]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        Gate::authorize('create', Ticket::class);

        return view('tickets.create', [
            'categories' => Ticket::CATEGORIES,
            'priorities' => Ticket::PRIORITIES,
            'department' => $request->user()->department,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $ticket = DB::transaction(function () use ($request): Ticket {
            $latestId = (int) Ticket::query()->lockForUpdate()->max('id');
            $ticket = Ticket::create([
                ...$request->safe()->only(['subject', 'category', 'location', 'department', 'priority', 'description']),
                'ticket_number' => sprintf('IT-%s-%06d', now()->year, $latestId + 1),
                'user_id' => $request->user()->id,
                'status' => 'Open',
            ]);

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $path = $file->store("tickets/{$ticket->id}");

                $ticket->attachments()->create([
                    'uploaded_by' => $request->user()->id,
                    'original_filename' => $file->getClientOriginalName(),
                    'stored_path' => $path,
                    'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
                    'file_size' => $file->getSize() ?: 0,
                ]);
            }

            return $ticket;
        });

        return redirect()->route('tickets.show', $ticket)->with('status', "Ticket {$ticket->ticket_number} submitted successfully.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket): View
    {
        Gate::authorize('view', $ticket);

        $ticket->load(['user', 'assignee', 'resolver', 'attachments.uploader', 'comments.user']);

        return view('tickets.show', ['ticket' => $ticket]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket): never
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket): never
    {
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket): never
    {
        abort(404);
    }
}
