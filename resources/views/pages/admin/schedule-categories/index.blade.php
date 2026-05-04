<?php

use App\Livewire\Forms\Admin\ScheduleCategoryForm;
use App\Models\ScheduleCategory;
use App\Traits\CanManage;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

new class extends Component {
    use CanManage, Interactions;

    public ScheduleCategoryForm $form;

    public bool $scheduleCategoryModal = false;

    public bool $isEditing = false;

    public function mount(): void
    {
        $this->ensureCanManage('schedule_categories.view');
    }

    #[Computed]
    public function stats(): array
    {
        return [
            'total' => ScheduleCategory::query()->count('*'),
            'active' => ScheduleCategory::query()->where('is_active', true)->count('*'),
            'inactive' => ScheduleCategory::query()->where('is_active', false)->count('*'),
        ];
    }

    public function openCreateModal(): void
    {
        $this->ensureCanManage('schedule_categories.create');

        $this->resetValidation();
        $this->form->resetForm();
        $this->isEditing = false;
        $this->scheduleCategoryModal = true;
    }

    #[On('editScheduleCategory')]
    public function openEditModal(ScheduleCategory $scheduleCategory): void
    {
        $this->ensureCanManage('schedule_categories.update');

        $this->resetValidation();
        $this->form->setScheduleCategory($scheduleCategory);
        $this->isEditing = true;
        $this->scheduleCategoryModal = true;
    }

    public function save(): void
    {
        $this->ensureCanManage($this->isEditing ? 'schedule_categories.update' : 'schedule_categories.create');

        try {
            $validated = $this->form->validateForm();

            if ($this->isEditing) {
                $this->form->scheduleCategory->update($validated);
                $message = 'Schedule category updated successfully.';
            } else {
                ScheduleCategory::query()->create($validated);
                $message = 'Schedule category created successfully.';
            }

            $this->scheduleCategoryModal = false;
            $this->dispatch('pg:eventRefresh-scheduleCategoriesTable');
            $this->toast()->success('Success', $message)->send();
        } catch (ValidationException $exception) {
            throw $exception;
        }
    }
};
?>

<div class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
        <div class="space-y-1">
            <h1 class="text-xl font-semibold text-zinc-900 dark:text-white">Schedule Category Management</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                Manage class assignment categories used by schedule plotting, faculty assignment, and room-time blocks.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <x-card>
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Categories</p>
                    <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ $this->stats['total'] }}</p>
                </div>

                <div class="rounded-lg bg-blue-50 p-2 text-blue-600 dark:bg-blue-950/40 dark:text-blue-300">
                    <x-icon icon="queue-list" class="h-5 w-5" />
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Active</p>
                    <p class="mt-1 text-2xl font-bold text-green-600">{{ $this->stats['active'] }}</p>
                </div>

                <div class="rounded-lg bg-emerald-50 p-2 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-300">
                    <x-icon icon="check-circle" class="h-5 w-5" />
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Inactive</p>
                    <p class="mt-1 text-2xl font-bold text-amber-600">{{ $this->stats['inactive'] }}</p>
                </div>

                <div class="rounded-lg bg-amber-50 p-2 text-amber-600 dark:bg-amber-950/40 dark:text-amber-300">
                    <x-icon icon="pause-circle" class="h-5 w-5" />
                </div>
            </div>
        </x-card>
    </div>

    <x-card>
        <div class="flex flex-col gap-4 border-b border-zinc-200 pb-4 md:flex-row md:items-start md:justify-between">
            <div class="space-y-1">
                <h2 class="text-lg font-semibold dark:text-white">Schedule Category List</h2>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Keep plotting categories standardized and aligned with scheduling workflow requirements.
                </p>
            </div>

            @can('schedule_categories.create')
                <x-button wire:click="openCreateModal" sm color="primary" icon="plus" text="New Schedule Category" />
            @endcan
        </div>

        <div class="p-6">
            <livewire:tables.admin.schedule-categories-table />
        </div>
    </x-card>

    <x-modal wire="scheduleCategoryModal" title="{{ $isEditing ? 'Edit Schedule Category' : 'New Schedule Category' }}">
        <div class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <x-input label="Name" wire:model="form.name" />
                <x-input label="Slug" wire:model="form.slug"
                    hint="Use a stable lowercase slug, e.g. lecture or student-teaching" />
            </div>

            <div class="rounded-lg border border-zinc-200 px-4 py-3 dark:border-zinc-700">
                <x-toggle wire:model="form.is_active" label="Category is active" />
                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ $form->is_active ? 'This category can be used for schedule plotting.' : 'This category will be hidden from new schedule assignments.' }}
                </p>
            </div>
        </div>

        <x-slot:footer>
            @canany(['schedule_categories.create', 'schedule_categories.update'])
                <x-button flat text="Cancel" wire:click="$set('scheduleCategoryModal', false)" sm />
                <x-button color="primary" :text="$isEditing ? 'Save Changes' : 'Save Schedule Category'" wire:click="save" sm />
            @endcanany
        </x-slot:footer>
    </x-modal>
</div>
