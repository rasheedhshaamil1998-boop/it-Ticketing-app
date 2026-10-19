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

        $activityStart = today()->subDays(6);
        $dailyTotals = Ticket::query()
            ->where('created_at', '>=', $activityStart)
            ->where('created_at', '<', today()->addDay())
            ->selectRaw('date(created_at) as day, count(*) as total')
            ->groupByRaw('date(created_at)')
            ->pluck('total', 'day');

        $activity = collect(range(0, 6))->map(function (int $offset) use ($activityStart, $dailyTotals): array {
            $day = $activityStart->copy()->addDays($offset);

            return [
                'label' => $day->format('D'),
                'date' => $day->format('M j'),
                'total' => (int) ($dailyTotals[$day->toDateString()] ?? 0),
            ];
        });

        return view('admin.dashboard', compact('statistics', 'recentTickets', 'byCategory', 'byStatus', 'byPriority', 'activity'));
    }
}
