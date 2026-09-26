<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Support\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        $users = User::query()
            ->with(['roles', 'hotels'])
            ->orderBy('name')
            ->paginate(20);

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        $roles = Role::query()->orderBy('name')->get();
        $hotels = \App\Models\Hotel::query()->orderBy('name')->get(['id', 'name', 'code']);

        return view('users.create', compact('roles', 'hotels'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $data = $request->safe()->except(['roles', 'hotels', 'password_confirmation']);
        $user = User::query()->create($data);

        if ($request->filled('roles')) {
            $user->syncRoles($request->input('roles'));
        }

        if ($request->has('hotels')) {
            $user->hotels()->sync($request->input('hotels', []));
        }

        Audit::log('created', 'users', $user->email, $user);

        return redirect()->route('users.index')->with('success', 'User created.');
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        $roles = Role::query()->orderBy('name')->get();
        $hotels = \App\Models\Hotel::query()->orderBy('name')->get(['id', 'name', 'code']);
        $user->load(['roles', 'hotels']);

        return view('users.edit', compact('user', 'roles', 'hotels'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $data = $request->safe()->except(['roles', 'hotels', 'password', 'password_confirmation']);

        if ($request->filled('password')) {
            $data['password'] = $request->validated('password');
        }

        $user->update($data);

        if ($request->has('roles')) {
            $user->syncRoles($request->input('roles', []));
        }

        if ($request->has('hotels')) {
            $user->hotels()->sync($request->input('hotels', []));
        }

        Audit::log('updated', 'users', $user->email, $user);

        return redirect()->route('users.index')->with('success', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $user->update(['status' => 'inactive']);
        Audit::log('deactivated', 'users', $user->email, $user);

        return redirect()->route('users.index')->with('success', 'User deactivated.');
    }
}
