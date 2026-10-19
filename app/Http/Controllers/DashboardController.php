<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $baseQuery = Ticket::query()->whereBelongsTo($user);

        $statistics = [
            'Total Tickets' => (clone $baseQuery)->count(),
            'Open Tickets' => (clone $baseQuery)->where('status', 'Open')->count(),
            'In Progress Tickets' => (clone $baseQuery)->where('status', 'In Progress')->count(),
            'Resolved Tickets' => (clone $baseQuery)->where('status', 'Resolved')->count(),
            'Closed Tickets' => (clone $baseQuery)->where('status', 'Closed')->count(),
        ];

        $recentTickets = Ticket::query()
            ->whereBelongsTo($user)
            ->with(['user', 'assignee'])
            ->latest()
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view('dashboard', compact('statistics', 'recentTickets'));
    }
}
