<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $query = $this->filteredTickets($request);

        $totalTickets = (clone $query)->count();
        $byCategory = (clone $query)->selectRaw('category, count(*) as total')->groupBy('category')->orderByDesc('total')->get();
        $byStatus = (clone $query)->selectRaw('status, count(*) as total')->groupBy('status')->get();
        $byPriority = (clone $query)->selectRaw('priority, count(*) as total')->groupBy('priority')->get();
        $byDepartment = (clone $query)->selectRaw('department, count(*) as total')->groupBy('department')->orderByDesc('total')->get();
        $resolvedBy = (clone $query)->with('resolver:id,name')->whereNotNull('resolved_by')->get()->groupBy('resolver.name');
        $averageResolutionHours = (clone $query)
            ->whereNotNull('resolved_at')
            ->get()
            ->avg(fn (Ticket $ticket): float => $ticket->created_at->diffInMinutes($ticket->resolved_at) / 60);

        return view('admin.reports.index', compact(
            'totalTickets',
            'byCategory',
            'byStatus',
            'byPriority',
            'byDepartment',
            'resolvedBy',
            'averageResolutionHours',
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        $tickets = $this->filteredTickets($request)
            ->with(['user:id,name', 'assignee:id,name'])
            ->latest()
            ->get();

        return response()->streamDownload(function () use ($tickets): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Ticket Number', 'Subject', 'Category', 'Priority', 'Status', 'Department', 'Created By', 'Assigned To', 'Created Date', 'Resolved Date']);

            foreach ($tickets as $ticket) {
                fputcsv($handle, [
                    $ticket->ticket_number,
                    $ticket->subject,
                    $ticket->category,
                    $ticket->priority,
                    $ticket->status,
                    $ticket->department,
                    $ticket->user?->name,
                    $ticket->assignee?->name,
                    $ticket->created_at?->toDateTimeString(),
                    $ticket->resolved_at?->toDateTimeString(),
                ]);
            }

            fclose($handle);
        }, 'ticket-report.csv', ['Content-Type' => 'text/csv']);
    }

    private function filteredTickets(Request $request)
    {
        return Ticket::query()
            ->when($request->filled('from'), fn ($query) => $query->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('created_at', '<=', $request->date('to')));
    }
}
