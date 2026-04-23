<?php

use App\Livewire\Forms\Admin\PermissionsForm;
use App\Models\Permission;
use App\Traits\CanManage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

new class extends Component {
    use CanManage, Interactions;

    public function mount(): void
    {
        $this->ensureCanManage('permissions.view');
    }

    public PermissionsForm $form;

    public bool $permissionModal = false;

    public bool $isEditing = false;

    #[Computed]
    public function stats(): array
    {
        return [
            'total' => Permission::query()->count(),
            'assigned' => Permission::query()->whereHas('roles')->count(),
            'guards' => Permission::query()->distinct('guard_name')->count('guard_name'),
        ];
    }

    public function openCreateModal()
    {
        $this->ensureCanManage('permissions.create');

        $this->form->resetForm();
        $this->isEditing = false;
        $this->permissionModal = true;
    }

    #[On('editPermission')]
    public function openEditModal(Permission $permission)
    {
        $this->ensureCanManage('permissions.update');

        $this->form->setPermission($permission);
        $this->isEditing = true;
        $this->permissionModal = true;
    }

    public function save(): void
    {
        $this->ensureCanManage($this->isEditing ? 'permissions.update' : 'permissions.create');

        try {
            $validated = $this->form->validateForm();

            if ($this->isEditing) {
                $this->form->permission->update([
                    'name' => $validated['name'],
                    'guard_name' => $validated['guard_name'],
                ]);
                $this->permissionModal = false;
                $this->dispatch('pg:eventRefresh-permissionsTable');
                $this->toast()->success('Success', 'Permission updated successfully.')->send();

                return;
            }

            Permission::create([
                'name' => $validated['name'],
                'guard_name' => $validated['guard_name'],
            ]);
            $this->permissionModal = false;
            $this->dispatch('pg:eventRefresh-permissionsTable');
            $this->toast()->success('Success', 'Permission created successfully.')->send();
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Permission Save Failed: ' . $e->getMessage());
            $this->toast()->error('Error', 'An unexpected error occurred while saving the permission.')->send();
        }
    }
};
?>

<div class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
        <div class="space-y-1">
            <h1 class="text-xl font-semibold text-zinc-900 dark:text-white">Permission Management</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                Manage system permissions that control access across dashboards, records, and workflows.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <x-card>
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Permissions</p>
                    <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ $this->stats['total'] }}</p>
                </div>

                <div class="rounded-lg bg-blue-50 p-2 text-blue-600 dark:bg-blue-950/40 dark:text-blue-300">
                    <x-icon icon="key" class="h-5 w-5" />
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Assigned to Roles</p>
                    <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ $this->stats['assigned'] }}</p>
                </div>

                <div class="rounded-lg bg-emerald-50 p-2 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-300">
                    <x-icon icon="shield-check" class="h-5 w-5" />
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Guards</p>
                    <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ $this->stats['guards'] }}</p>
                </div>

                <div class="rounded-lg bg-amber-50 p-2 text-amber-600 dark:bg-amber-950/40 dark:text-amber-300">
                    <x-icon icon="lock-closed" class="h-5 w-5" />
                </div>
            </div>
        </x-card>
    </div>

    <x-card>
        <div class="flex flex-col gap-4 border-b border-zinc-200 pb-4 md:flex-row md:items-start md:justify-between">
            <div class="space-y-1">
                <h2 class="text-lg font-semibold dark:text-white">Permission List</h2>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Review available permission keys and update access controls when needed.
                </p>
            </div>

            @can('permissions.create')
                <x-button wire:click="openCreateModal" sm color="primary" icon="plus" text="New Permission" />
            @endcan
        </div>

        <div class="p-6">
            <livewire:tables.admin.permissions-table />
        </div>
    </x-card>

    <x-modal wire="permissionModal" title="{{ $isEditing ? 'Edit Permission' : 'New Permission' }}">
        <div class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <x-input label="Name" wire:model="form.name"
                    hint="Use the system permission key, e.g. users.view or campuses.view" />
                <x-input label="Guard" wire:model="form.guard_name" />
            </div>
        </div>

        <x-slot:footer>
            @canany(['permissions.update', 'permissions.create'])
                <x-button flat text="Cancel" wire:click="$set('permissionModal', false)" sm />
                <x-button color="primary" :text="$isEditing ? 'Save Changes' : 'Save Permission'" wire:click="save" sm />
            @endcanany
        </x-slot:footer>
    </x-modal>
</div>
