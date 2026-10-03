<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Hotel;
use App\Models\User;
use App\Support\Audit;
use App\Support\QuerySort;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $query = User::query()
            ->with(['roles', 'hotels']);

        if ($request->filled('q')) {
            $search = $request->string('q')->trim()->toString();
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('job_title', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('role')) {
            $query->role($request->string('role')->toString());
        }

        if ($request->filled('hotel_id')) {
            $hotelId = $request->integer('hotel_id');
            $query->whereHas('hotels', fn ($q) => $q->where('hotels.id', $hotelId));
        }

        QuerySort::apply($query, $request, [
            'name' => 'name',
            'email' => 'email',
            'phone' => 'phone',
            'job_title' => 'job_title',
            'status' => 'status',
        ], 'name');

        $users = $query->paginate(10)->withQueryString();
        $roles = Role::query()->orderBy('name')->get(['id', 'name']);
        $hotels = Hotel::query()->orderBy('name')->get(['id', 'name', 'code']);

        return view('users.index', compact('users', 'roles', 'hotels'));
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        $roles = Role::query()->orderBy('name')->get();
        $hotels = Hotel::query()->orderBy('name')->get(['id', 'name', 'code']);
        $existingEmails = User::query()->pluck('email')->map(fn ($email) => mb_strtolower($email))->values();

        return view('users.create', compact('roles', 'hotels', 'existingEmails'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $data = $request->safe()->except(['roles', 'hotels', 'password_confirmation', 'department']);
        $user = User::query()->create($data);

        if ($request->filled('roles')) {
            $user->syncRoles($request->input('roles'));
        } else {
            $user->syncRoles([]);
        }

        $user->hotels()->sync($request->input('hotels', []));

        Audit::log('created', 'users', $user->email, $user);

        return redirect()->route('users.index')->with('success', 'User created.');
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        $roles = Role::query()->orderBy('name')->get();
        $hotels = Hotel::query()->orderBy('name')->get(['id', 'name', 'code']);
        $user->load(['roles', 'hotels']);
        $existingEmails = User::query()
            ->where('id', '!=', $user->id)
            ->pluck('email')
            ->map(fn ($email) => mb_strtolower($email))
            ->values();

        return view('users.edit', compact('user', 'roles', 'hotels', 'existingEmails'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $data = $request->safe()->except(['roles', 'hotels', 'password', 'password_confirmation', 'department']);

        if ($request->filled('password')) {
            $data['password'] = $request->validated('password');
        }

        $user->update($data);

        if ($request->has('roles')) {
            $user->syncRoles($request->input('roles', []));
        }

        $user->hotels()->sync($request->input('hotels', []));

        Audit::log('updated', 'users', $user->email, $user);

        return redirect()->route('users.index')->with('success', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'You cannot delete your own account.');
        }

        $user->load(['roles', 'hotels']);

        if (! $user->canBeDeleted()) {
            return redirect()->route('users.index')->with(
                'error',
                'This user has roles or hotel access assigned. Remove those first, or deactivate the account instead.'
            );
        }

        $email = $user->email;
        $user->delete();

        Audit::log('deleted', 'users', $email);

        return redirect()->route('users.index')->with('success', 'User deleted.');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'You cannot deactivate your own account.');
        }

        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        Audit::log($newStatus === 'active' ? 'activated' : 'deactivated', 'users', $user->email, $user);

        return redirect()->route('users.index')->with(
            'success',
            $newStatus === 'active' ? 'User activated.' : 'User deactivated. They can no longer sign in.'
        );
    }
}
