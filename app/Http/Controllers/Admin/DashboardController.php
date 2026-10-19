<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $byCategory = Ticket::selectRaw('category, count(*) as total')->groupBy('category')->orderByDesc('total')->get();
        $byStatus = Ticket::selectRaw('status, count(*) as total')->groupBy('status')->get();
        $byPriority = Ticket::selectRaw('priority, count(*) as total')->groupBy('priority')->get();
        $statusTotals = $byStatus->pluck('total', 'status');
        $priorityTotals = $byPriority->pluck('total', 'priority');

        $statistics = [
            'Total Tickets' => (int) $byStatus->sum('total'),
            'New Tickets' => Ticket::where('status', 'Open')->whereDate('created_at', today())->count(),
            'Open Tickets' => (int) ($statusTotals['Open'] ?? 0),
            'In Progress Tickets' => (int) ($statusTotals['In Progress'] ?? 0),
            'Resolved Tickets' => (int) ($statusTotals['Resolved'] ?? 0),
            'Closed Tickets' => (int) ($statusTotals['Closed'] ?? 0),
            'Critical Tickets' => (int) ($priorityTotals['Critical'] ?? 0),
            'High Priority' => (int) ($priorityTotals['High'] ?? 0),
        ];

        $totalUsers = $request->user()->isSuperAdmin() ? User::count() : null;
        $recentTickets = Ticket::with(['user', 'assignee'])->latest()->orderByDesc('id')->limit(10)->get();
        $assignedStatistics = $request->user()->assignedTickets()
            ->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        $assignedTickets = $request->user()->assignedTickets()
            ->with(['user', 'assignee'])->latest()->limit(5)->get();

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

        $monthStart = today()->startOfMonth()->subMonths(5);
        $monthlyTotals = Ticket::query()
            ->where('created_at', '>=', $monthStart)
            ->where('created_at', '<', today()->addDay())
            ->selectRaw('SUBSTR(created_at, 1, 7) as month, count(*) as total')
            ->groupByRaw('SUBSTR(created_at, 1, 7)')->pluck('total', 'month');
        $monthlyActivity = collect(range(0, 5))->map(function (int $offset) use ($monthStart, $monthlyTotals): array {
            $month = $monthStart->copy()->addMonths($offset);

            return ['label' => $month->format('M Y'), 'total' => (int) ($monthlyTotals[$month->format('Y-m')] ?? 0)];
        });
        $chartData = [
            'status' => ['labels' => Ticket::STATUSES, 'values' => array_map(fn (string $status): int => (int) ($statusTotals[$status] ?? 0), Ticket::STATUSES)],
            'monthly' => ['labels' => $monthlyActivity->pluck('label')->all(), 'values' => $monthlyActivity->pluck('total')->all()],
            'priority' => ['labels' => Ticket::PRIORITIES, 'values' => array_map(fn (string $priority): int => (int) ($priorityTotals[$priority] ?? 0), Ticket::PRIORITIES)],
        ];

        return view('admin.dashboard', compact('statistics', 'recentTickets', 'byCategory', 'byStatus', 'byPriority', 'activity', 'totalUsers', 'assignedStatistics', 'assignedTickets', 'monthlyActivity', 'chartData'));
    }
}
