<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $rolePermissions = [
            'superAdmin' => Permission::query()->pluck('name')->all(),
            'collegeAdmin' => [
                'view.dashboard',
                'manage.users',
                'manage.branches',
                'manage.departments',
                'manage.faculty_profiles',
            ],
            'deptAdmin' => [
                'view.dashboard',
                'manage.users',
                'manage.faculty_profiles',
            ],
            'faculty' => [
                'view.dashboard',
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissions) {
            $role = Role::withTrashed()->firstOrNew([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);

            $role->guard_name = 'web';
            $role->save();

            if ($role->trashed()) {
                $role->restore();
            }

            $role->syncPermissions($permissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
