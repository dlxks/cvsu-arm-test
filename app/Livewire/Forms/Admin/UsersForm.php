<?php

namespace App\Livewire\Forms\Admin;

use App\Models\College;
use App\Models\Department;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Form;

class UsersForm extends Form
{
    public ?User $user = null;

    public ?string $first_name = '';

    public ?string $middle_name = '';

    public ?string $last_name = '';

    public ?string $email = '';

    public array $roles = [];

    public array $direct_permissions = [];

    public ?string $type = 'standard';

    public ?int $campus_id = null;

    public ?int $college_id = null;

    public ?int $department_id = null;

    public ?string $academic_rank = '';

    public ?string $contactno = '';

    public ?string $address = '';

    public ?string $sex = '';

    public ?string $birthday = '';

    // Employee Specific Fields
    public ?string $position = '';

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')
                    ->ignore($this->user?->id)
                    ->whereNull('deleted_at'),
            ],
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,name',
            'direct_permissions' => 'nullable|array',
            'direct_permissions.*' => 'exists:permissions,name',
            'type' => 'required|in:faculty,employee,standard',

            'campus_id' => 'exclude_if:type,standard|required|exists:campuses,id',
            'college_id' => [
                'exclude_if:type,standard',
                'required',
                Rule::exists('colleges', 'id')->where(
                    fn ($query) => $query->where('campus_id', $this->campus_id)
                ),
            ],
            'department_id' => [
                'exclude_if:type,standard',
                'nullable',
                Rule::requiredIf(fn () => $this->type === 'faculty'),
                Rule::exists('departments', 'id')->where(
                    fn ($query) => $query->where('college_id', $this->college_id)
                ),
            ],

            'position' => Rule::requiredIf(fn () => $this->type === 'employee'),
            'sex' => 'nullable|in:Male,Female',
            'birthday' => 'nullable|date',
        ];
    }

    public function validateForm(): array
    {
        return $this->validate($this->rules());
    }

    public function setValues(User $user)
    {
        $this->user = $user;
        $this->email = $user->email ?? '';
        $this->roles = $user->roles->pluck('name')->toArray();
        $this->direct_permissions = $user->getDirectPermissions()->pluck('name')->toArray();
        $this->resetProfileFields();

        $faculty = $user->facultyProfile;
        $employee = $user->employeeProfile;

        if ($faculty) {
            $this->type = 'faculty';
            $profile = $faculty;
        } elseif ($employee) {
            $this->type = 'employee';
            $profile = $employee;
        } else {
            $this->type = 'standard';
            $profile = null;
        }

        if ($profile) {
            $this->first_name = $profile->first_name ?? '';
            $this->middle_name = $profile->middle_name ?? '';
            $this->last_name = $profile->last_name ?? '';
            $this->campus_id = $profile->campus_id;
            $this->college_id = $profile->college_id;
            $this->department_id = $profile->department_id;

            if ($this->type === 'faculty') {
                $this->academic_rank = $profile->academic_rank ?? '';
                $this->contactno = $profile->contactno ?? '';
                $this->address = $profile->address ?? '';
                $this->sex = $profile->sex ?? '';
                $this->birthday = $profile->birthday ?? '';
            } else {
                $this->position = $profile->position ?? '';
            }
        } else {
            $nameParts = explode(' ', $user->name, 2);
            $this->first_name = $nameParts[0] ?? '';
            $this->last_name = $nameParts[1] ?? '';
            $this->middle_name = '';
            $this->campus_id = null;
            $this->college_id = null;
            $this->department_id = null;
        }
    }

    public function resetProfileFields(): void
    {
        $this->academic_rank = '';
        $this->contactno = '';
        $this->address = '';
        $this->sex = '';
        $this->birthday = '';
        $this->position = '';
    }

    public function fullName(): string
    {
        return trim($this->first_name.' '.($this->middle_name ? $this->middle_name.' ' : '').$this->last_name);
    }

    public function resolveAcademicAssignment(): array
    {
        if ($this->department_id) {
            $department = Department::query()
                ->whereKey($this->department_id)
                ->where('college_id', $this->college_id)
                ->where('campus_id', $this->campus_id)
                ->firstOrFail();

            return [
                'campus_id' => (int) $this->campus_id,
                'college_id' => (int) $this->college_id,
                'department_id' => $department->id,
            ];
        }

        $college = College::query()
            ->whereKey($this->college_id)
            ->where('campus_id', $this->campus_id)
            ->firstOrFail();

        return [
            'campus_id' => (int) $this->campus_id,
            'college_id' => $college->id,
            'department_id' => null,
        ];
    }

    public function resolveDirectPermissions()
    {
        return Permission::query()
            ->where('guard_name', 'web')
            ->whereIn('name', $this->direct_permissions)
            ->get();
    }

    public function profileData(): array
    {
        return [
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
        ];
    }
}
