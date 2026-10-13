<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketCommentRequest;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;

class TicketCommentController extends Controller
{
    public function store(StoreTicketCommentRequest $request, Ticket $ticket): RedirectResponse
    {
        $ticket->comments()->create([
            'user_id' => $request->user()->id,
            'message' => $request->validated('message'),
        ]);

        return back()->with('status', 'Comment added successfully.');
    }
}
