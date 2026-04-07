<?php

namespace App\Livewire\Forms\Admin;

use App\Models\EmployeeProfile;
use App\Models\FacultyProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Form;

class UsersForm extends Form
{
    public ?User $user = null;

    // 1. Added ? to make properties nullable
    public ?string $first_name = '';

    public ?string $middle_name = '';

    public ?string $last_name = '';

    public ?string $email = '';

    public array $roles = [];

    public ?string $type = 'standard';

    public $branch_id = null;

    public $department_id = null;

    // Faculty Specific Fields
    public ?string $academic_rank = '';

    public ?string $contactno = '';

    public ?string $address = '';

    public ?string $sex = '';

    public ?string $birthday = '';

    // Employee Specific Fields
    public ?string $position = '';

    public function rules()
    {
        return [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.($this->user ? $this->user->id : 'NULL'),
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,name',
            'type' => 'required|in:faculty,employee,standard',

            'branch_id' => 'exclude_if:type,standard|required|exists:branches,id',
            'department_id' => [
                'exclude_if:type,standard',
                'nullable',
                Rule::requiredIf(fn () => $this->type === 'faculty'),
                'exists:departments,id',
            ],

            'sex' => 'nullable|in:Male,Female',
            'birthday' => 'nullable|date',
        ];
    }

    public function setValues(User $user)
    {
        $this->user = $user;
        $this->email = $user->email ?? '';
        $this->roles = $user->roles->pluck('name')->toArray();

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
            // 2. Added ?? '' fallback for safe hydration
            $this->first_name = $profile->first_name ?? '';
            $this->middle_name = $profile->middle_name ?? '';
            $this->last_name = $profile->last_name ?? '';
            $this->branch_id = $profile->branch_id;
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
            $this->middle_name = ''; // Explicitly set to string to clear any old state
        }
    }

    public function store()
    {
        $this->validate();

        DB::transaction(function () {
            $fullName = trim($this->first_name.' '.($this->middle_name ? $this->middle_name.' ' : '').$this->last_name);

            $newUser = User::create([
                'name' => $fullName,
                'email' => $this->email,
                'password' => Hash::make('password123'),
            ]);

            $newUser->assignRole($this->roles);

            if ($this->type !== 'standard') {
                $profileData = [
                    'user_id' => $newUser->id,
                    'first_name' => $this->first_name,
                    'middle_name' => $this->middle_name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'branch_id' => $this->branch_id,
                    'department_id' => $this->department_id,
                ];

                if ($this->type === 'faculty') {
                    FacultyProfile::create(array_merge($profileData, [
                        'academic_rank' => $this->academic_rank,
                        'contactno' => $this->contactno,
                        'address' => $this->address,
                        'sex' => $this->sex,
                        'birthday' => $this->birthday,
                    ]));
                } elseif ($this->type === 'employee') {
                    EmployeeProfile::create(array_merge($profileData, [
                        'position' => $this->position,
                    ]));
                }
            }
        });

        $this->reset();
    }

    public function update()
    {
        $this->validate();

        DB::transaction(function () {
            $fullName = trim($this->first_name.' '.($this->middle_name ? $this->middle_name.' ' : '').$this->last_name);

            $this->user->update([
                'name' => $fullName,
                'email' => $this->email,
            ]);

            $this->user->syncRoles($this->roles);

            if ($this->type === 'standard') {
                FacultyProfile::where('user_id', $this->user->id)->delete();
                EmployeeProfile::where('user_id', $this->user->id)->delete();
            } else {
                $profileData = [
                    'first_name' => $this->first_name,
                    'middle_name' => $this->middle_name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'branch_id' => $this->branch_id,
                    'department_id' => $this->department_id,
                ];

                if ($this->type === 'faculty') {
                    EmployeeProfile::where('user_id', $this->user->id)->delete();

                    FacultyProfile::updateOrCreate(
                        ['user_id' => $this->user->id],
                        array_merge($profileData, [
                            'academic_rank' => $this->academic_rank,
                            'contactno' => $this->contactno,
                            'address' => $this->address,
                            'sex' => $this->sex,
                            'birthday' => $this->birthday,
                        ])
                    );
                } elseif ($this->type === 'employee') {
                    FacultyProfile::where('user_id', $this->user->id)->delete();

                    EmployeeProfile::updateOrCreate(
                        ['user_id' => $this->user->id],
                        array_merge($profileData, [
                            'position' => $this->position,
                        ])
                    );
                }
            }
        });
    }
}
