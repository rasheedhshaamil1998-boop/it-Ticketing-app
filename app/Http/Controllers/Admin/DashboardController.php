<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $statistics = [
            'Total Tickets' => Ticket::count(),
            'New Tickets' => Ticket::where('status', 'Open')->whereDate('created_at', today())->count(),
            'Open Tickets' => Ticket::where('status', 'Open')->count(),
            'In Progress Tickets' => Ticket::where('status', 'In Progress')->count(),
            'Resolved Tickets' => Ticket::where('status', 'Resolved')->count(),
            'Closed Tickets' => Ticket::where('status', 'Closed')->count(),
            'Critical Tickets' => Ticket::where('priority', 'Critical')->count(),
        ];

        $recentTickets = Ticket::with(['user', 'assignee'])->latest()->limit(8)->get();
        $byCategory = Ticket::selectRaw('category, count(*) as total')->groupBy('category')->orderByDesc('total')->get();
        $byStatus = Ticket::selectRaw('status, count(*) as total')->groupBy('status')->get();
        $byPriority = Ticket::selectRaw('priority, count(*) as total')->groupBy('priority')->get();

        return view('admin.dashboard', compact('statistics', 'recentTickets', 'byCategory', 'byStatus', 'byPriority'));
    }
}
