<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\EmployeeProfile;
use App\Models\FacultyProfile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function faculty(): static
    {
        return $this->afterCreating(function (User $user) {
            $this->ensureRoleExists('faculty');
            $department = Department::query()->inRandomOrder()->first() ?? Department::factory()->create();
            $nameParts = $this->nameParts($user);

            $user->assignRole('faculty');
            FacultyProfile::factory()->create([
                'user_id' => $user->id,
                'email' => $user->email,
                'first_name' => $nameParts['first_name'],
                'middle_name' => $nameParts['middle_name'],
                'last_name' => $nameParts['last_name'],
                'branch_id' => $department->branch_id,
                'department_id' => $department->id,
            ]);
        });
    }

    public function superAdmin(): static
    {
        return $this->afterCreating(function (User $user) {
            $this->ensureRoleExists('superAdmin');
            $user->assignRole('superAdmin');
        });
    }

    public function collegeAdmin(): static
    {
        return $this->afterCreating(function (User $user) {
            $this->ensureRoleExists('collegeAdmin');
            $department = Department::query()->inRandomOrder()->first() ?? Department::factory()->create();
            $nameParts = $this->nameParts($user);

            $user->assignRole('collegeAdmin');
            EmployeeProfile::factory()->create([
                'user_id' => $user->id,
                'first_name' => $nameParts['first_name'],
                'middle_name' => $nameParts['middle_name'],
                'last_name' => $nameParts['last_name'],
                'position' => 'College Administrator',
                'branch_id' => $department->branch_id,
                'department_id' => $department->id,
            ]);
        });
    }

    public function deptAdmin(): static
    {
        return $this->afterCreating(function (User $user) {
            $this->ensureRoleExists('deptAdmin');
            $department = Department::query()->inRandomOrder()->first() ?? Department::factory()->create();
            $nameParts = $this->nameParts($user);

            $user->assignRole('deptAdmin');

            EmployeeProfile::factory()->create([
                'user_id' => $user->id,
                'first_name' => $nameParts['first_name'],
                'middle_name' => $nameParts['middle_name'],
                'last_name' => $nameParts['last_name'],
                'position' => 'Department Administrator',
                'branch_id' => $department->branch_id,
                'department_id' => $department->id,
            ]);
        });
    }

    public function dualRole(): static
    {
        return $this->afterCreating(function (User $user) {
            $this->ensureRoleExists('faculty');
            $this->ensureRoleExists('deptAdmin');
            $department = Department::query()->inRandomOrder()->first() ?? Department::factory()->create();
            $nameParts = $this->nameParts($user);

            $user->assignRole(['faculty', 'deptAdmin']);
            FacultyProfile::factory()->create([
                'user_id' => $user->id,
                'email' => $user->email,
                'first_name' => $nameParts['first_name'],
                'middle_name' => $nameParts['middle_name'],
                'last_name' => $nameParts['last_name'],
                'branch_id' => $department->branch_id,
                'department_id' => $department->id,
            ]);
            EmployeeProfile::factory()->create([
                'user_id' => $user->id,
                'first_name' => $nameParts['first_name'],
                'middle_name' => $nameParts['middle_name'],
                'last_name' => $nameParts['last_name'],
                'position' => 'Department Administrator',
                'branch_id' => $department->branch_id,
                'department_id' => $department->id,
            ]);
        });
    }

    protected function ensureRoleExists(string $roleName): void
    {
        $role = Role::withTrashed()->firstOrNew([
            'name' => $roleName,
            'guard_name' => 'web',
        ]);

        $role->guard_name = 'web';
        $role->save();

        if ($role->trashed()) {
            $role->restore();
        }
    }

    /**
     * Keep generated profile names aligned with the owning user.
     *
     * @return array{first_name: string, middle_name: string, last_name: string}
     */
    protected function nameParts(User $user): array
    {
        $parts = preg_split('/\s+/', trim($user->name)) ?: [];
        $firstName = $parts[0] ?? 'User';
        $lastName = count($parts) > 1 ? array_pop($parts) : 'Account';
        $middleName = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';

        return [
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName,
        ];
    }
}
