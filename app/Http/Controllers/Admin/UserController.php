<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $users = User::query()
            ->withCount('tickets')
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();

                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('department', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.users.index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        abort_unless(request()->user()?->isSuperAdmin(), 403);

        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = User::create($request->validated());

        return redirect()->route('admin.users.show', $user)->with('status', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): View
    {
        abort_unless(request()->user()?->isSuperAdmin(), 403);

        $user->loadCount('tickets');
        $tickets = $user->tickets()->latest()->paginate(10);

        return view('admin.users.show', compact('user', 'tickets'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): never
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $wouldRemoveLastSuperAdmin = $user->isSuperAdmin()
            && ($data['role'] !== 'superadmin' || ! (bool) $data['is_active'])
            && User::where('role', 'superadmin')->where('is_active', true)->whereKeyNot($user->id)->doesntExist();

        if ($wouldRemoveLastSuperAdmin) {
            return back()->withErrors(['role' => 'At least one active super administrator must remain.']);
        }

        $user->update($data);

        return back()->with('status', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): never
    {
        abort(404);
    }
}
