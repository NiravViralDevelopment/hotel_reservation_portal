<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (['activity.view', 'activity.view_all'] as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $admin = Role::findOrCreate('Administrator', 'web');
        $admin->givePermissionTo(['activity.view', 'activity.view_all']);

        $user = Role::findOrCreate('User', 'web');
        $user->givePermissionTo(['activity.view']);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = Permission::query()
            ->where('guard_name', 'web')
            ->whereIn('name', ['activity.view', 'activity.view_all'])
            ->get();

        foreach (Role::query()->where('guard_name', 'web')->get() as $role) {
            $role->revokePermissionTo($permissions);
        }

        Permission::query()
            ->where('guard_name', 'web')
            ->whereIn('name', ['activity.view', 'activity.view_all'])
            ->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
