<?php

use App\Livewire\Forms\Admin\CampusForm;
use App\Models\Campus;
use App\Traits\CanManage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

new class extends Component {
    use CanManage, Interactions;

    public CampusForm $form;

    public bool $createModal = false;

    public function mount(): void
    {
        $this->ensureCanManage('campuses.view');
        $this->form->resetForm();
    }

    #[Computed]
    public function stats(): array
    {
        return [
            'total' => Campus::query()->count(),
            'active' => Campus::query()->where('is_active', true)->count(),
            'inactive' => Campus::query()->where('is_active', false)->count(),
        ];
    }

    public function openCreateModal(): void
    {
        $this->ensureCanManage('campuses.create');
        $this->resetValidation();
        $this->form->resetForm();
        $this->createModal = true;
    }

    public function reopenCreateModal(): void
    {
        $this->createModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->createModal = false;
        $this->resetValidation();
        $this->form->resetForm();
    }

    public function confirmSave(): void
    {
        $this->ensureCanManage('campuses.create');
        $this->form->validateForm();
        $this->createModal = false;

        $this->dialog()->question('Create Campus?', 'Are you sure you want to add this new campus?')->confirm('Yes, create', 'save')->cancel('Cancel', 'reopenCreateModal')->send();
    }

    public function save(): void
    {
        $this->ensureCanManage('campuses.create');

        try {
            $validated = $this->form->validateForm();
            Campus::query()->create($this->form->payload($validated));
            $this->createModal = false;
            $this->form->resetForm();
            $this->dispatch('pg:eventRefresh-campusesTable');
            $this->toast()->success('Success', 'Campus created successfully.')->send();
        } catch (ValidationException $e) {
            $this->reopenCreateModal();
            throw $e;
        } catch (Exception $e) {
            $this->reopenCreateModal();
            Log::error('Campus creation failed: ' . $e->getMessage());
            $this->toast()->error('Error', 'An unexpected error occurred while creating the campus.')->send();
        }
    }
};
?>

<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
        <div class="space-y-1">
            <h1 class="text-xl font-semibold text-zinc-900 dark:text-white">Campus Management</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                Manage all CvSU campuses, their details, and associated colleges.
            </p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <x-card>
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Campuses</p>
                    <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ $this->stats['total'] }}</p>
                </div>

                <div class="rounded-lg bg-blue-50 p-2 text-blue-600 dark:bg-blue-950/40 dark:text-blue-300">
                    <x-icon icon="map-pin" class="h-5 w-5" />
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Active</p>
                    <p class="mt-1 text-2xl font-bold text-green-600">{{ $this->stats['active'] }}</p>
                </div>

                <div class="rounded-lg bg-green-50 p-2 text-green-600 dark:bg-green-950/40 dark:text-green-300">
                    <x-icon icon="check-circle" class="h-5 w-5" />
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Inactive</p>
                    <p class="mt-1 text-2xl font-bold text-red-500">{{ $this->stats['inactive'] }}</p>
                </div>

                <div class="rounded-lg bg-red-50 p-2 text-red-600 dark:bg-red-950/40 dark:text-red-300">
                    <x-icon icon="x-circle" class="h-5 w-5" />
                </div>
            </div>
        </x-card>
    </div>

    {{-- Main Campus Body --}}
    <x-card>
        <div class="flex flex-col gap-4 border-b border-zinc-200 pb-4 md:flex-row md:items-start md:justify-between">
            <div class="space-y-1">
                <h2 class="text-lg font-semibold dark:text-white">Campus List</h2>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Review all campuses and open a campus record to manage its colleges.
                </p>
            </div>

            @can('campuses.create')
                <x-button color="primary" text="New Campus" icon="plus" wire:click="openCreateModal" sm />
            @endcan
        </div>

        <div class="p-6">
            <livewire:tables.admin.campuses-table />
        </div>
    </x-card>

    {{-- Create Campus Modal --}}
    <x-modal wire="createModal" title="Add New Campus" size="3xl">
        <div class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <x-input label="Campus Code" wire:model="form.code" hint="Use a short code like CvSU-MAIN." />
                <x-input label="Campus Name" wire:model="form.name" />
            </div>

            <x-textarea label="Description" wire:model="form.description"
                hint="Optional short description for this campus." />

            <div class="rounded-lg border border-zinc-200 px-4 py-3 dark:border-zinc-700">
                <x-toggle wire:model="form.is_active" label="Campus is active" />
                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ $form->is_active ? 'This campus will be available for active assignments.' : 'This campus will be marked as inactive.' }}
                </p>
            </div>
        </div>

        <x-slot:footer>
            @can('campuses.create')
                <x-button flat text="Cancel" wire:click="closeCreateModal" sm />
                <x-button color="primary" text="Create Campus" wire:click="confirmSave" sm />
            @endcan
        </x-slot:footer>
    </x-modal>

</div>
