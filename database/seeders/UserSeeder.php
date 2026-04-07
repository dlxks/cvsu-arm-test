<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\EmployeeProfile;
use App\Models\FacultyProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use RuntimeException;

class UserSeeder extends Seeder
{
    /**
     * Seed deterministic demo users that match the current admin workflows.
     */
    public function run(): void
    {
        $ceitPrimaryDepartment = $this->findDepartmentForBranch('CEIT');
        $ceitSecondaryDepartment = $this->findDepartmentForBranch('CEIT', 1);
        $casPrimaryDepartment = $this->findDepartmentForBranch('CAS');

        $namedSuperAdmin = $this->upsertUser(
            'tristan.sangangbayan@cvsu.edu.ph',
            'Tristan Sangangbayan',
            ['superAdmin']
        );

        $this->deleteProfiles($namedSuperAdmin->id);

        $legacyCollegeAdmin = $this->upsertUser(
            'dlxks.sangangbayan@gmail.com',
            'College Admin',
            ['collegeAdmin']
        );

        $this->upsertEmployeeProfile($legacyCollegeAdmin->id, [
            'first_name' => 'College',
            'middle_name' => '',
            'last_name' => 'Admin',
            'position' => 'College Administrator',
            'branch_id' => $ceitPrimaryDepartment->branch_id,
            'department_id' => $ceitPrimaryDepartment->id,
        ]);

        FacultyProfile::withTrashed()->where('user_id', $legacyCollegeAdmin->id)->delete();

        $legacyDepartmentAdmin = $this->upsertUser(
            'sky.shira7@gmail.com',
            'Department Admin',
            ['deptAdmin']
        );

        $this->upsertEmployeeProfile($legacyDepartmentAdmin->id, [
            'first_name' => 'Department',
            'middle_name' => '',
            'last_name' => 'Admin',
            'position' => 'Department Administrator',
            'branch_id' => $casPrimaryDepartment->branch_id,
            'department_id' => $casPrimaryDepartment->id,
        ]);

        FacultyProfile::withTrashed()->where('user_id', $legacyDepartmentAdmin->id)->delete();

        $legacyFaculty = $this->upsertUser(
            'sangangbayant@gmail.com',
            'Faculty Account',
            ['faculty', 'collegeAdmin']
        );

        $this->upsertFacultyProfile($legacyFaculty->id, [
            'first_name' => 'Faculty',
            'middle_name' => '',
            'last_name' => 'Account',
            'email' => $legacyFaculty->email,
            'branch_id' => $ceitSecondaryDepartment->branch_id,
            'department_id' => $ceitSecondaryDepartment->id,
            'academic_rank' => 'Instructor I',
            'contactno' => '09179876543',
            'address' => 'Indang, Cavite',
            'sex' => 'Male',
            'birthday' => '1994-10-12',
            'updated_by' => $namedSuperAdmin->id,
        ]);

        EmployeeProfile::withTrashed()->where('user_id', $legacyFaculty->id)->delete();

        $facultyDepartments = Department::query()
            ->where('is_active', true)
            ->orderBy('branch_id')
            ->orderBy('name')
            ->get();

        if ($facultyDepartments->isEmpty()) {
            throw new RuntimeException('Unable to seed faculty demo users because no active departments are available.');
        }

        User::factory()
            ->count(4)
            ->superAdmin()
            ->create();

        User::factory()
            ->count(4)
            ->collegeAdmin()
            ->create();

        User::factory()
            ->count(4)
            ->deptAdmin()
            ->create();

        $this->seedFacultyBatch($facultyDepartments, 99, $namedSuperAdmin);
    }

    protected function upsertUser(string $email, string $name, array $roles): User
    {
        $user = User::withTrashed()->firstOrNew(['email' => $email]);

        $user->forceFill([
            'name' => $name,
            'email' => $email,
            'password' => 'password123',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $user->save();

        if ($user->trashed()) {
            $user->restore();
        }

        $user->syncRoles($roles);

        return $user->fresh();
    }

    protected function upsertFacultyProfile(int $userId, array $attributes): void
    {
        $profile = FacultyProfile::withTrashed()->updateOrCreate(
            ['user_id' => $userId],
            $attributes
        );

        if ($profile->trashed()) {
            $profile->restore();
        }
    }

    protected function upsertEmployeeProfile(int $userId, array $attributes): void
    {
        $profile = EmployeeProfile::withTrashed()->updateOrCreate(
            ['user_id' => $userId],
            $attributes
        );

        if ($profile->trashed()) {
            $profile->restore();
        }
    }

    protected function deleteProfiles(int $userId): void
    {
        FacultyProfile::withTrashed()->where('user_id', $userId)->delete();
        EmployeeProfile::withTrashed()->where('user_id', $userId)->delete();
    }

    protected function findDepartmentForBranch(string $branchCode, int $offset = 0): Department
    {
        $department = Department::query()
            ->whereHas('branch', fn ($query) => $query->where('code', $branchCode))
            ->orderBy('id')
            ->skip($offset)
            ->first();

        if (! $department) {
            throw new RuntimeException("Unable to locate department offset [{$offset}] for branch [{$branchCode}].");
        }

        return $department;
    }

    protected function seedFacultyBatch(Collection $departments, int $count, User $updatedBy): void
    {
        User::factory()
            ->count($count)
            ->faculty()
            ->create()
            ->each(function (User $user, int $index) use ($departments, $updatedBy): void {
                $department = $departments[$index % $departments->count()];

                $user->facultyProfile?->update([
                    'email' => $user->email,
                    'branch_id' => $department->branch_id,
                    'department_id' => $department->id,
                    'updated_by' => $updatedBy->id,
                ]);
            });
    }
}
