<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Seed the application's default permissions.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'view.dashboard',
            'manage.users',
            'manage.roles',
            'manage.permissions',
            'manage.branches',
            'manage.departments',
            'manage.faculty_profiles',
        ];

        foreach ($permissions as $permissionName) {
            $permission = Permission::withTrashed()->firstOrNew([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);

            $permission->guard_name = 'web';
            $permission->save();

            if ($permission->trashed()) {
                $permission->restore();
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
