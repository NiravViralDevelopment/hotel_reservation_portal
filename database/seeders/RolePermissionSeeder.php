<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'dashboard.view',
            'companies.view', 'companies.create', 'companies.edit', 'companies.delete',
            'hotels.view', 'hotels.create', 'hotels.edit', 'hotels.delete',
            'agencies.view', 'agencies.create', 'agencies.edit', 'agencies.delete',
            'bookings.view', 'bookings.create', 'bookings.edit', 'bookings.delete', 'bookings.cancel',
            'enquiries.view', 'enquiries.create', 'enquiries.edit', 'enquiries.delete', 'enquiries.convert',
            'documents.view', 'documents.upload', 'documents.download', 'documents.delete',
            'revenue.view', 'reports.view', 'reports.generate',
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'roles.view', 'roles.edit',
            'statuses.view', 'statuses.create', 'statuses.edit', 'statuses.delete',
            'settings.manage', 'audit.view',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $userPermissions = [
            'dashboard.view',
            'companies.view', 'companies.create', 'companies.edit',
            'hotels.view', 'hotels.create', 'hotels.edit',
            'agencies.view', 'agencies.create', 'agencies.edit',
            'bookings.view', 'bookings.create', 'bookings.edit', 'bookings.cancel',
            'enquiries.view', 'enquiries.create', 'enquiries.edit', 'enquiries.convert',
            'documents.view', 'documents.upload', 'documents.download',
            'revenue.view', 'reports.view', 'reports.generate',
        ];

        $matrix = [
            'Administrator' => $permissions,
            'User' => $userPermissions,
        ];

        foreach ($matrix as $roleName => $perms) {
            $role = Role::findOrCreate($roleName, 'web');
            $role->syncPermissions($perms);
        }

        // Keep only Administrator and User — remove any legacy roles from older seeds.
        Role::query()
            ->where('guard_name', 'web')
            ->whereNotIn('name', ['Administrator', 'User'])
            ->get()
            ->each(function (Role $role): void {
                $role->syncPermissions([]);
                $role->delete();
            });

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
