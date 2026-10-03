<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Support\Audit;
use App\Support\QuerySort;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('roles.view');

        $query = Role::query()
            ->withCount('users')
            ->with('permissions');

        QuerySort::apply($query, $request, [
            'name' => 'name',
            'users' => 'users_count',
        ], 'name');

        $roles = $query->paginate(10)->withQueryString();

        return view('roles.index', compact('roles'));
    }

    public function create(): View
    {
        $this->authorize('roles.edit');

        $permissions = Permission::query()->orderBy('name')->get();

        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('roles.edit');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:125', Rule::unique('roles', 'name')->where(fn ($q) => $q->where('guard_name', 'web'))],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        Audit::log('created', 'roles', $role->name, $role);

        return redirect()->route('roles.index')->with('success', 'Role created.');
    }

    public function edit(Role $role): View
    {
        $this->authorize('roles.edit');

        $permissions = Permission::query()->orderBy('name')->get();
        $role->load('permissions');

        return view('roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $this->authorize('roles.edit');

        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        // Role names are fixed; only permissions can be updated.
        if ($role->name === 'Administrator') {
            $role->syncPermissions(Permission::query()->pluck('name')->all());
        } else {
            $role->syncPermissions($validated['permissions'] ?? []);
        }

        Audit::log('updated', 'roles', $role->name, $role);

        return redirect()->route('roles.index')->with('success', 'Role updated.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->authorize('roles.edit');

        if ($role->name === 'Administrator') {
            return redirect()->route('roles.index')->with('error', 'The Administrator role cannot be removed.');
        }

        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')->with('error', 'Remove this role from all users before deleting it.');
        }

        $name = $role->name;
        $role->delete();
        Audit::log('deleted', 'roles', $name);

        return redirect()->route('roles.index')->with('success', 'Role removed.');
    }
}
