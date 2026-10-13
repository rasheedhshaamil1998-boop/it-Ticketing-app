<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RolePermissionController extends Controller
{
    public function __invoke(Request $request): View
    {
        $roleCounts = User::query()
            ->selectRaw('role, count(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role');

        $roles = collect([
            [
                'name' => 'User',
                'key' => 'user',
                'badge' => 'Employee access',
                'icon' => 'bi-person',
                'groups' => [
                    'Tickets' => [
                        'Create new support tickets',
                        'View only tickets they created',
                        'Search and filter their own tickets',
                    ],
                    'Conversation' => [
                        'Add comments to their own tickets',
                        'View replies from IT staff',
                        'Download authorized attachments',
                    ],
                    'Restricted' => [
                        'Cannot access admin pages',
                        'Cannot change ticket status or assignment',
                        'Cannot manage users or reports',
                    ],
                ],
            ],
            [
                'name' => 'Admin',
                'key' => 'admin',
                'badge' => 'Ticket operations',
                'icon' => 'bi-tools',
                'groups' => [
                    'Tickets' => [
                        'View and review all tickets',
                        'Assign tickets to IT administrators',
                        'Update priority and status',
                    ],
                    'Resolution' => [
                        'Reply to ticket conversations',
                        'Resolve tickets with required notes',
                        'Close and reopen tickets',
                    ],
                    'Reports' => [
                        'View ticket reports',
                        'Filter reports by date',
                        'Export ticket reports to CSV',
                    ],
                ],
            ],
            [
                'name' => 'Super Admin',
                'key' => 'superadmin',
                'badge' => 'Full control',
                'icon' => 'bi-shield-lock',
                'groups' => [
                    'All Admin Controls' => [
                        'Includes every admin ticket permission',
                        'Can view dashboards and reports',
                        'Can resolve, close, and reopen tickets',
                    ],
                    'User Access' => [
                        'Create users, admins, and super admins',
                        'Change roles and account status',
                        'View user details and submitted tickets',
                    ],
                    'Safeguards' => [
                        'Cannot remove the last active super admin',
                        'Controls access to user management',
                        'Keeps administration separate from ticket work',
                    ],
                ],
            ],
        ])
            ->map(function (array $role) use ($roleCounts): array {
                $role['users_count'] = (int) ($roleCounts[$role['key']] ?? 0);

                return $role;
            })
            ->all();

        return view('admin.roles-permissions', ['roles' => $roles]);
    }
}
