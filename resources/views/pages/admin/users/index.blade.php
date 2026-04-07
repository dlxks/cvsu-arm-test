<?php

use App\Imports\UsersImport;
use App\Livewire\Forms\Admin\UsersForm;
use App\Models\Branch;
use App\Models\Department;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;
use TallStackUi\Traits\Interactions;

new class extends Component {
    use Interactions, WithFileUploads;

    public UsersForm $form;

    public bool $createModal = false;

    public bool $importModal = false;

    public $importFile;

    public Collection $branches;

    public Collection $departments;

    public Collection $roles;

    public function mount()
    {
        $this->branches = Branch::where('is_active', true)->get();
        $this->departments = collect();
        $this->roles = Role::all();
    }

    public function updatedFormBranchId($branchId)
    {
        $this->departments = Department::where('branch_id', $branchId)->where('is_active', true)->get();
        $this->form->department_id = null;
    }

    public function updatedFormType($value)
    {
        if ($value === 'standard') {
            $this->form->branch_id = null;
            $this->form->department_id = null;
        }
    }

    public function save()
    {
        $this->form->store();

        $this->createModal = false;
        $this->toast()->success('Success', 'User created successfully.')->send();
        $this->dispatch('pg:eventRefresh-usersTable');
    }

    public function import()
    {
        $this->validate(['importFile' => 'required|mimes:csv,xlsx,xls']);

        // Excel::import(new UsersImport, $this->importFile);
        $this->importModal = false;
        $this->importFile = null;
        $this->toast()->success('Imported', 'Users have been successfully imported.')->send();
        $this->dispatch('pg:eventRefresh-usersTable');
    }
}; ?>

<div>
    <div class="flex justify-between items-center w-full">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight dark:text-gray-200">
            {{ __('User Management') }}
        </h2>
        <div class="flex gap-2">
            <x-button color="slate" text="Import" icon="arrow-up-tray" wire:click="$set('importModal', true)" sm
                outline />
            <x-button color="primary" text="New User" icon="plus" wire:click="$set('createModal', true)" sm />
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <livewire:admin.users-table />
            </x-card>
        </div>
    </div>

    {{-- Create User Modal --}}
    <x-modal wire="createModal" title="Create New User" size="4xl">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-3">
                <h4
                    class="text-sm font-semibold text-gray-600 border-b pb-1 mb-2 dark:text-zinc-300 dark:border-zinc-700">
                    Account Details</h4>
            </div>

            <x-input label="First Name" wire:model="form.first_name" placeholder="Juan" />
            <x-input label="Middle Name" wire:model="form.middle_name" placeholder="Dela" />
            <x-input label="Last Name" wire:model="form.last_name" placeholder="Cruz" />

            <x-input label="Email Address" type="email" wire:model="form.email" placeholder="juan@example.com" />

            <x-select.styled label="Roles" wire:model="form.roles" multiple :options="$roles->map(fn($r) => ['label' => Str::headline($r->name), 'value' => $r->name])->toArray()"
                select="label:label|value:value" />

            <x-select.styled label="Profile Type" wire:model.live="form.type" :options="[
                ['label' => 'Standard User', 'value' => 'standard'],
                ['label' => 'Faculty', 'value' => 'faculty'],
                ['label' => 'Employee', 'value' => 'employee'],
            ]"
                select="label:label|value:value" />

            {{-- Only show these fields if they are NOT a Standard User --}}
            @if ($form->type !== 'standard')
                <div class="md:col-span-3 mt-4">
                    <h4
                        class="text-sm font-semibold text-gray-600 border-b pb-1 mb-2 dark:text-zinc-300 dark:border-zinc-700">
                        Assignment & Profile</h4>
                </div>

                <x-select.styled label="Campus / Branch" wire:model.live="form.branch_id" :options="$branches->map(fn($b) => ['label' => $b->name, 'value' => $b->id])->toArray()"
                    select="label:label|value:value" />

                <x-select.styled :label="$form->type === 'employee' ? 'Department (Optional)' : 'Department'" :hint="$form->type === 'employee' ? 'Leave this blank if the employee is not assigned to a department.' : null" :placeholder="$form->type === 'employee' ? 'Select a department if applicable' : 'Select a department'"
                    wire:model="form.department_id" :options="$departments->map(fn($d) => ['label' => $d->name, 'value' => $d->id])->toArray()" select="label:label|value:value"
                    :disabled="$departments->isEmpty()" :required="$form->type === 'faculty'" />

                @if ($form->type === 'faculty')
                    <x-input label="Academic Rank" wire:model="form.academic_rank" />
                    <x-input label="Contact No." wire:model="form.contactno" />
                    <x-select.styled label="Sex" wire:model="form.sex" :options="['Male', 'Female']" />
                    <x-input label="Birthday" type="date" wire:model="form.birthday" />
                    <div class="md:col-span-2">
                        <x-input label="Full Address" wire:model="form.address" />
                    </div>
                @elseif ($form->type === 'employee')
                    <x-input label="Job Position" wire:model="form.position" />
                @endif
            @endif
        </div>

        <x-slot:footer>
            <x-button flat text="Cancel" wire:click="$set('createModal', false)" sm />
            <x-button color="primary" text="Save User" wire:click="save" sm />
        </x-slot:footer>
    </x-modal>

    {{-- Import Modal --}}
    <x-modal wire="importModal" title="Import Users">
        <div class="space-y-4">
            <x-upload wire:model="importFile" label="Select Excel/CSV File" hint="Supported files: .xlsx, .csv" />
        </div>
        <x-slot:footer>
            <x-button flat text="Cancel" wire:click="$set('importModal', false)" sm />
            <x-button color="green" text="Run Import" wire:click="import" sm />
        </x-slot:footer>
    </x-modal>
</div>
