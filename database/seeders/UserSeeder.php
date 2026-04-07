<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\EmployeeProfile;
use App\Models\FacultyProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class UserSeeder extends Seeder
{
    /**
     * Seed deterministic demo users that match the current admin workflows.
     */
    public function run(): void
    {
        $dit = $this->findDepartment('CEIT', 'DIT');
        $dcs = $this->findDepartment('CEIT', 'DCS');
        $dbio = $this->findDepartment('CAS', 'DBIO');

        $superAdmin = $this->upsertUser(
            'superadmin@cvsu.edu.ph',
            'Super Admin',
            ['superAdmin']
        );

        $this->deleteProfiles($superAdmin->id);

        $collegeAdmin = $this->upsertUser(
            'college.admin@cvsu.edu.ph',
            'College Admin',
            ['collegeAdmin']
        );

        $this->upsertEmployeeProfile($collegeAdmin->id, [
            'first_name' => 'College',
            'middle_name' => '',
            'last_name' => 'Admin',
            'position' => 'College Administrator',
            'branch_id' => $dcs->branch_id,
            'department_id' => $dcs->id,
        ]);

        FacultyProfile::withTrashed()->where('user_id', $collegeAdmin->id)->delete();

        $departmentAdmin = $this->upsertUser(
            'department.admin@cvsu.edu.ph',
            'Department Admin',
            ['deptAdmin']
        );

        $this->upsertEmployeeProfile($departmentAdmin->id, [
            'first_name' => 'Department',
            'middle_name' => '',
            'last_name' => 'Admin',
            'position' => 'Department Administrator',
            'branch_id' => $dbio->branch_id,
            'department_id' => $dbio->id,
        ]);

        FacultyProfile::withTrashed()->where('user_id', $departmentAdmin->id)->delete();

        $faculty = $this->upsertUser(
            'faculty.member@cvsu.edu.ph',
            'Faculty Member',
            ['faculty']
        );

        $this->upsertFacultyProfile($faculty->id, [
            'first_name' => 'Faculty',
            'middle_name' => '',
            'last_name' => 'Member',
            'email' => $faculty->email,
            'branch_id' => $dit->branch_id,
            'department_id' => $dit->id,
            'academic_rank' => 'Instructor I',
            'contactno' => '09171234567',
            'address' => 'Indang, Cavite',
            'sex' => 'Female',
            'birthday' => '1995-04-18',
            'updated_by' => $superAdmin->id,
        ]);

        EmployeeProfile::withTrashed()->where('user_id', $faculty->id)->delete();

        $legacySuperAdmin = $this->upsertUser(
            'tristan.sangangbayan@cvsu.edu.ph',
            'Tristan Sangangbayan',
            ['superAdmin']
        );

        $this->deleteProfiles($legacySuperAdmin->id);

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
            'branch_id' => $dcs->branch_id,
            'department_id' => $dcs->id,
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
            'branch_id' => $dbio->branch_id,
            'department_id' => $dbio->id,
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
            'branch_id' => $dit->branch_id,
            'department_id' => $dit->id,
            'academic_rank' => 'Instructor I',
            'contactno' => '09179876543',
            'address' => 'Indang, Cavite',
            'sex' => 'Male',
            'birthday' => '1994-10-12',
            'updated_by' => $legacySuperAdmin->id,
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

        foreach (range(1, 12) as $index) {
            $department = $facultyDepartments[($index - 1) % $facultyDepartments->count()] ?? null;

            if (! $department) {
                throw new RuntimeException('Unable to seed faculty demo users because no active departments are available.');
            }

            $user = $this->upsertUser(
                sprintf('faculty%02d@cvsu.edu.ph', $index),
                sprintf('Faculty Demo %02d', $index),
                ['faculty']
            );

            $this->upsertFacultyProfile($user->id, [
                'first_name' => 'Faculty',
                'middle_name' => '',
                'last_name' => sprintf('Demo %02d', $index),
                'email' => $user->email,
                'branch_id' => $department->branch_id,
                'department_id' => $department->id,
                'academic_rank' => ['Instructor I', 'Instructor II', 'Assistant Professor I'][$index % 3],
                'contactno' => sprintf('0917%07d', 2000000 + $index),
                'address' => 'Cavite',
                'sex' => $index % 2 === 0 ? 'Male' : 'Female',
                'birthday' => now()->subYears(25 + $index)->format('Y-m-d'),
                'updated_by' => $superAdmin->id,
            ]);

            EmployeeProfile::withTrashed()->where('user_id', $user->id)->delete();
        }

        $employeeDepartments = Department::query()
            ->orderBy('branch_id')
            ->orderBy('name')
            ->get();

        if ($employeeDepartments->isEmpty()) {
            throw new RuntimeException('Unable to seed employee demo users because no departments are available.');
        }

        foreach (range(1, 4) as $index) {
            $department = $employeeDepartments[($index - 1) % $employeeDepartments->count()] ?? null;

            if (! $department) {
                throw new RuntimeException('Unable to seed employee demo users because no departments are available.');
            }

            $user = $this->upsertUser(
                sprintf('collegeadmin%02d@cvsu.edu.ph', $index),
                sprintf('College Admin Demo %02d', $index),
                ['collegeAdmin']
            );

            $this->upsertEmployeeProfile($user->id, [
                'first_name' => 'College',
                'middle_name' => '',
                'last_name' => sprintf('Admin %02d', $index),
                'position' => ['College Administrator', 'Campus Director', 'Program Coordinator'][$index % 3],
                'branch_id' => $department->branch_id,
                'department_id' => $department->id,
            ]);

            FacultyProfile::withTrashed()->where('user_id', $user->id)->delete();
        }

        foreach (range(1, 4) as $index) {
            $department = $employeeDepartments[($index + 3) % $employeeDepartments->count()] ?? null;

            if (! $department) {
                throw new RuntimeException('Unable to seed department-admin demo users because no departments are available.');
            }

            $user = $this->upsertUser(
                sprintf('deptadmin%02d@cvsu.edu.ph', $index),
                sprintf('Department Admin Demo %02d', $index),
                ['deptAdmin']
            );

            $this->upsertEmployeeProfile($user->id, [
                'first_name' => 'Department',
                'middle_name' => '',
                'last_name' => sprintf('Admin %02d', $index),
                'position' => ['Department Administrator', 'Department Chairperson', 'Program Lead'][$index % 3],
                'branch_id' => $department->branch_id,
                'department_id' => $department->id,
            ]);

            FacultyProfile::withTrashed()->where('user_id', $user->id)->delete();
        }
    }

    protected function upsertUser(string $email, string $name, array $roles): User
    {
        $user = User::withTrashed()->firstOrNew(['email' => $email]);

        $user->forceFill([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make('password123'),
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

    protected function findDepartment(string $branchCode, string $departmentCode): Department
    {
        $department = Department::query()
            ->where('code', $departmentCode)
            ->whereHas('branch', fn ($query) => $query->where('code', $branchCode))
            ->first();

        if (! $department) {
            throw new RuntimeException("Unable to locate department [{$departmentCode}] for branch [{$branchCode}].");
        }

        return $department;
    }
}
