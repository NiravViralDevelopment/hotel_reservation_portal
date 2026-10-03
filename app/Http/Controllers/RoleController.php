<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Support\Audit;
use App\Support\QuerySort;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * @var array<string, string>
     */
    private array $moduleLabels = [
        'dashboard' => 'Dashboard',
        'companies' => 'Companies',
        'hotels' => 'Hotels',
        'agencies' => 'Travel agencies',
        'bookings' => 'Group bookings',
        'enquiries' => 'Enquiries',
        'documents' => 'Documents',
        'revenue' => 'Revenue',
        'reports' => 'Reports',
        'users' => 'Users',
        'roles' => 'Roles',
        'statuses' => 'Status master',
        'settings' => 'Settings',
        'audit' => 'Audit logs',
    ];

    /**
     * @var array<string, string>
     */
    private array $actionLabels = [
        'view' => 'View',
        'create' => 'Create',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'cancel' => 'Cancel',
        'convert' => 'Convert',
        'upload' => 'Upload',
        'download' => 'Download',
        'generate' => 'Generate',
        'manage' => 'Manage',
    ];

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

    public function show(Role $role): View
    {
        $this->authorize('roles.view');

        $role->load(['permissions', 'users']);
        $role->loadCount('users');
        $permissionGroups = $this->permissionGroups(Permission::query()->orderBy('name')->get());

        return view('roles.show', compact('role', 'permissionGroups'));
    }

    public function create(): View
    {
        $this->authorize('roles.edit');

        $permissions = Permission::query()->orderBy('name')->get();
        $permissionGroups = $this->permissionGroups($permissions);

        return view('roles.create', compact('permissions', 'permissionGroups'));
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
        $permissionGroups = $this->permissionGroups($permissions);
        $role->load('permissions');

        return view('roles.edit', compact('role', 'permissions', 'permissionGroups'));
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

        return redirect()->route('roles.show', $role)->with('success', 'Role updated.');
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

    /**
     * @param  Collection<int, Permission>  $permissions
     * @return Collection<string, array{key: string, label: string, permissions: Collection<int, array{id: int, name: string, action: string, label: string}>}>
     */
    private function permissionGroups(Collection $permissions): Collection
    {
        $actionOrder = array_keys($this->actionLabels);

        return $permissions
            ->map(function (Permission $permission) {
                [$module, $action] = array_pad(explode('.', $permission->name, 2), 2, $permission->name);

                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'module' => $module,
                    'action' => $action,
                    'label' => $this->actionLabels[$action] ?? ucfirst(str_replace('_', ' ', $action)),
                ];
            })
            ->groupBy('module')
            ->map(function (Collection $items, string $module) use ($actionOrder) {
                $sorted = $items->sortBy(function (array $item) use ($actionOrder) {
                    $index = array_search($item['action'], $actionOrder, true);

                    return $index === false ? 999 : $index;
                })->values();

                return [
                    'key' => $module,
                    'label' => $this->moduleLabels[$module] ?? ucfirst(str_replace('_', ' ', $module)),
                    'permissions' => $sorted,
                ];
            })
            ->sortBy(function (array $group) {
                $keys = array_keys($this->moduleLabels);
                $index = array_search($group['key'], $keys, true);

                return $index === false ? 999 : $index;
            })
            ->values();
    }
}
