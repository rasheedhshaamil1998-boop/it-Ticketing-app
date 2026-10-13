<?php

namespace App\Http\Controllers;

use App\Models\TicketAttachment;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class TicketAttachmentController extends Controller
{
    public function show(TicketAttachment $attachment): Response
    {
        $attachment->load('ticket');
        Gate::authorize('view', $attachment->ticket);

        return Storage::download($attachment->stored_path, $attachment->original_filename);
    }
}
