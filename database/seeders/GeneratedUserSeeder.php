<?php

namespace Database\Seeders;

use App\Models\College;
use App\Models\Department;
use App\Models\EmployeeProfile;
use App\Models\FacultyProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RuntimeException;

class GeneratedUserSeeder extends Seeder
{
    public function run(): void
    {
        $departments = Department::query()
            ->where('is_active', true)
            ->with(['campus', 'college'])
            ->orderBy('campus_id')
            ->orderBy('college_id')
            ->orderBy('name')
            ->get();

        if ($departments->isEmpty()) {
            throw new RuntimeException('Unable to seed generated users because no active departments are available.');
        }

        $updatedBy = User::query()
            ->where('email', 'tristan.sangangbayan@cvsu.edu.ph')
            ->first()
            ?? User::query()->role('superAdmin')->first();

        if (! $updatedBy) {
            throw new RuntimeException('Unable to seed generated users because no super admin account is available.');
        }

        $departments->each(fn (Department $department) => $this->seedFacultyBatch($department, 10, $updatedBy));
        $departments->each(fn (Department $department) => $this->seedDeptAdminBatch($department, 5, $updatedBy));

        $departments
            ->groupBy('college_id')
            ->each(function (Collection $collegeDepartments): void {
                /** @var Department $sampleDepartment */
                $sampleDepartment = $collegeDepartments->first();

                $this->seedCollegeAdminBatch($sampleDepartment->college, $collegeDepartments->values(), 5);
            });
    }

    protected function seedFacultyBatch(Department $department, int $count, User $updatedBy): void
    {
        foreach (range(1, $count) as $index) {
            $name = sprintf('%s Faculty %02d', $department->code, $index);
            $email = sprintf(
                'generated.faculty.%s.%s.%02d@cvsu-arm.test',
                $this->slug($department->college->code),
                $this->slug($department->code),
                $index
            );

            $user = $this->upsertUser($email, $name, ['faculty']);

            $this->upsertFacultyProfile($user, $department, $updatedBy, [
                'employee_no' => sprintf('FAC-%03d-%02d', $department->id, $index),
                'first_name' => $department->code,
                'middle_name' => 'Faculty',
                'last_name' => sprintf('%02d', $index),
                'academic_rank' => 'Instructor I',
                'email' => $user->email,
                'contactno' => sprintf('09%09d', ($department->id * 100) + $index),
                'address' => trim(sprintf('%s, %s', $department->name, $department->college->name)),
                'sex' => $index % 2 === 0 ? 'Female' : 'Male',
                'birthday' => now()->subYears(24 + (($department->id + $index) % 20))->toDateString(),
            ]);

            $this->deleteEmployeeProfile($user->id);
        }
    }

    protected function seedCollegeAdminBatch(College $college, Collection $departments, int $count): void
    {
        foreach (range(1, $count) as $index) {
            /** @var Department $department */
            $department = $departments[($index - 1) % $departments->count()];
            $roles = $this->collegeAdminRoles($index);
            $name = sprintf('%s College Admin %02d', $college->code, $index);
            $email = sprintf(
                'generated.college-admin.%s.%02d@cvsu-arm.test',
                $this->slug($college->code),
                $index
            );

            $user = $this->upsertUser($email, $name, $roles);

            $this->upsertEmployeeProfile($user, $department, [
                'employee_no' => sprintf('CADM-%03d-%02d', $college->id, $index),
                'first_name' => $college->code,
                'middle_name' => 'College',
                'last_name' => sprintf('Admin %02d', $index),
                'position' => in_array('deptAdmin', $roles, true)
                    ? 'College and Department Administrator'
                    : 'College Administrator',
                'department_id' => in_array('deptAdmin', $roles, true) ? $department->id : null,
            ]);

            if (in_array('faculty', $roles, true)) {
                $this->upsertFacultyProfile($user, $department, $user, [
                    'employee_no' => sprintf('FCA-%03d-%02d', $college->id, $index),
                    'first_name' => $college->code,
                    'middle_name' => 'Faculty',
                    'last_name' => sprintf('Admin %02d', $index),
                    'academic_rank' => 'Assistant Professor I',
                    'email' => $user->email,
                    'contactno' => sprintf('09%09d', ($college->id * 1_000) + $index),
                    'address' => trim(sprintf('%s, %s', $college->name, $department->name)),
                    'sex' => $index % 2 === 0 ? 'Female' : 'Male',
                    'birthday' => now()->subYears(30 + $index)->toDateString(),
                ]);
            } else {
                $this->deleteFacultyProfile($user->id);
            }
        }
    }

    protected function seedDeptAdminBatch(Department $department, int $count, User $updatedBy): void
    {
        foreach (range(1, $count) as $index) {
            $roles = $this->departmentAdminRoles($index);
            $name = sprintf('%s Department Admin %02d', $department->code, $index);
            $email = sprintf(
                'generated.dept-admin.%s.%s.%02d@cvsu-arm.test',
                $this->slug($department->college->code),
                $this->slug($department->code),
                $index
            );

            $user = $this->upsertUser($email, $name, $roles);

            $this->upsertEmployeeProfile($user, $department, [
                'employee_no' => sprintf('DADM-%03d-%02d', $department->id, $index),
                'first_name' => $department->code,
                'middle_name' => 'Department',
                'last_name' => sprintf('Admin %02d', $index),
                'position' => in_array('collegeAdmin', $roles, true)
                    ? 'Department and College Administrator'
                    : 'Department Administrator',
            ]);

            if (in_array('faculty', $roles, true)) {
                $this->upsertFacultyProfile($user, $department, $updatedBy, [
                    'employee_no' => sprintf('FDA-%03d-%02d', $department->id, $index),
                    'first_name' => $department->code,
                    'middle_name' => 'Faculty',
                    'last_name' => sprintf('Admin %02d', $index),
                    'academic_rank' => 'Assistant Professor I',
                    'email' => $user->email,
                    'contactno' => sprintf('09%09d', ($department->id * 10_000) + $index),
                    'address' => trim(sprintf('%s, %s', $department->name, $department->college->name)),
                    'sex' => $index % 2 === 0 ? 'Female' : 'Male',
                    'birthday' => now()->subYears(29 + $index)->toDateString(),
                ]);
            } else {
                $this->deleteFacultyProfile($user->id);
            }
        }
    }

    protected function upsertUser(string $email, string $name, array $roles): User
    {
        $generated = User::factory()->make([
            'name' => $name,
            'email' => $email,
        ]);

        $user = User::withTrashed()->firstOrNew(['email' => $email]);
        $user->forceFill(Arr::only($generated->getAttributes(), [
            'name',
            'email',
            'password',
            'email_verified_at',
            'is_active',
            'remember_token',
        ]));
        $user->save();

        if ($user->trashed()) {
            $user->restore();
        }

        $user->syncRoles($roles);

        return $user->fresh();
    }

    protected function upsertFacultyProfile(User $user, Department $department, User $updatedBy, array $attributes): void
    {
        $profile = FacultyProfile::factory()
            ->forDepartment($department)
            ->make(array_merge($attributes, [
                'user_id' => $user->id,
                'email' => $attributes['email'] ?? $user->email,
                'updated_by' => $updatedBy->id,
            ]));

        $record = FacultyProfile::withTrashed()->updateOrCreate(
            ['user_id' => $user->id],
            Arr::only($profile->getAttributes(), [
                'employee_no',
                'first_name',
                'middle_name',
                'last_name',
                'campus_id',
                'college_id',
                'department_id',
                'academic_rank',
                'email',
                'contactno',
                'address',
                'sex',
                'birthday',
                'updated_by',
            ])
        );

        if ($record->trashed()) {
            $record->restore();
        }
    }

    protected function upsertEmployeeProfile(User $user, Department $department, array $attributes): void
    {
        $profile = EmployeeProfile::factory()
            ->forDepartment($department)
            ->make(array_merge($attributes, [
                'user_id' => $user->id,
            ]));

        $record = EmployeeProfile::withTrashed()->updateOrCreate(
            ['user_id' => $user->id],
            Arr::only($profile->getAttributes(), [
                'employee_no',
                'first_name',
                'middle_name',
                'last_name',
                'position',
                'campus_id',
                'college_id',
                'department_id',
            ])
        );

        if ($record->trashed()) {
            $record->restore();
        }
    }

    protected function deleteFacultyProfile(int $userId): void
    {
        FacultyProfile::withTrashed()->where('user_id', $userId)->get()->each->delete();
    }

    protected function deleteEmployeeProfile(int $userId): void
    {
        EmployeeProfile::withTrashed()->where('user_id', $userId)->get()->each->delete();
    }

    /**
     * @return array<int, string>
     */
    protected function collegeAdminRoles(int $index): array
    {
        return match ($index) {
            1 => ['collegeAdmin'],
            2 => ['collegeAdmin', 'faculty'],
            3 => ['collegeAdmin', 'deptAdmin'],
            4 => ['collegeAdmin', 'faculty', 'deptAdmin'],
            default => ['collegeAdmin', 'faculty'],
        };
    }

    /**
     * @return array<int, string>
     */
    protected function departmentAdminRoles(int $index): array
    {
        return match ($index) {
            1 => ['deptAdmin'],
            2 => ['deptAdmin', 'faculty'],
            3 => ['deptAdmin', 'collegeAdmin'],
            4 => ['deptAdmin', 'faculty', 'collegeAdmin'],
            default => ['deptAdmin', 'faculty'],
        };
    }

    protected function slug(string $value): string
    {
        return Str::of($value)->lower()->replace(' ', '-')->replace('_', '-')->toString();
    }
}
