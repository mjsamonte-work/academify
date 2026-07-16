<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->with('roles')
            ->withMax('loginLogs', 'logged_in_at')
            ->when($request->string('search')->isNotEmpty(), function ($query) use ($request): void {
                $search = $request->string('search')->toString();

                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('role'), fn ($query) => $query->role($request->string('role')->toString()))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => Role::query()->orderBy('name')->pluck('name'),
            'statuses' => [User::STATUS_ACTIVE, User::STATUS_INACTIVE],
        ]);
    }

    public function create(): View
    {
        abort_unless(request()->user()?->can('users.create'), 403);

        return view('admin.users.create', [
            'roles' => Role::query()->orderBy('name')->pluck('name'),
            'statuses' => [User::STATUS_ACTIVE, User::STATUS_INACTIVE],
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $roles = $validated['roles'];
        unset($validated['roles']);

        $user = User::create($validated);
        $user->syncRoles($roles);

        return redirect()->route('admin.users.show', $user)
            ->with('status', 'User account created.');
    }

    public function show(User $user): View
    {
        $user->load(['roles', 'loginLogs' => fn ($query) => $query->latest('logged_in_at')->limit(10)]);

        return view('admin.users.show', [
            'user' => $user,
        ]);
    }

    public function edit(User $user): View
    {
        abort_unless(request()->user()?->can('users.update'), 403);

        return view('admin.users.edit', [
            'user' => $user->load('roles'),
            'roles' => Role::query()->orderBy('name')->pluck('name'),
            'statuses' => [User::STATUS_ACTIVE, User::STATUS_INACTIVE],
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();
        $roles = $validated['roles'];
        unset($validated['roles']);

        if (blank($validated['password'] ?? null)) {
            unset($validated['password']);
        }

        $user->update($validated);
        $user->syncRoles($roles);

        return redirect()->route('admin.users.show', $user)
            ->with('status', 'User account updated.');
    }
}
