<?php

namespace App\Livewire\Admin;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use TallStackUi\Traits\Interactions;

final class BranchesTable extends PowerGridComponent
{
    use Interactions, WithExport;

    public string $tableName = 'branchTable';

    public function boot(): void
    {
        config(['livewire-powergrid.filter' => 'outside']);
    }

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::exportable(fileName: 'branches-list')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),

            PowerGrid::header()
                ->showSearchInput()
                ->showToggleColumns()
                ->showSoftDeletes(showMessage: true),

            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Branch::query()
            ->when($this->softDeletes === 'withTrashed', fn ($query) => $query->withTrashed())
            ->when($this->softDeletes === 'onlyTrashed', fn ($query) => $query->onlyTrashed());
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('deleted_at')
            ->add('code')
            ->add('code_link', fn (Branch $model) => '<a href="'.route('admin.branches.show', $model->id).'" class="text-primary-500 hover:text-primary-700 dark:hover:text-primary-400 hover:underline font-medium transition-colors">'.e($model->code).'</a>')
            ->add('name')
            ->add('name_link', fn (Branch $model) => '<a href="'.route('admin.branches.show', $model->id).'" class="text-primary-500 hover:text-primary-700 dark:hover:text-primary-400 hover:underline font-medium transition-colors">'.e($model->name).'</a>')
            ->add('type')
            ->add('address');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id'),
            Column::make('Code', 'code_link', 'code')->sortable()->searchable(),
            Column::make('Name', 'name_link', 'name')->sortable()->searchable(),
            Column::make('Campus', 'type')->sortable()->searchable(),
            Column::make('Address', 'address')->sortable()->searchable(),
            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::select('type', 'type')
                ->dataSource([
                    ['name' => 'Main', 'id' => 'Main'],
                    ['name' => 'Satellite', 'id' => 'Satellite'],
                ])
                ->optionLabel('name')
                ->optionValue('id'),
        ];
    }

    public function actions($row): array
    {
        return [Button::add('view-branch')
            ->slot('View')
            ->icon('default-eye', ['class' => 'w-4 h-4 text-primary-500 group-hover:text-primary-700 dark:group-hover:text-primary-400'])
            ->class('group flex items-center gap-1 text-xs font-bold text-primary-500 rounded border border-primary-500 px-2 py-1 hover:text-primary-700 hover:bg-zinc-100 dark:hover:bg-primary-800 dark:hover:text-primary-400 transition-all duration-300 cursor-pointer')
            ->route('admin.branches.show', ['branch' => $row->id]),

            Button::add('delete-branch')
                ->slot('Remove')
                ->icon('default-trash', ['class' => 'w-4 h-4 text-red-500 group-hover:text-red-700 dark:group-hover:text-red-400'])
                ->class('group flex items-center gap-1 text-xs font-bold text-red-500 rounded border border-red-500 px-2 py-1 hover:text-red-700 hover:bg-zinc-100 dark:hover:bg-red-800 dark:hover:text-red-400 transition-all duration-300 cursor-pointer')
                ->call('confirmDeleteBranch', ['id' => $row->id]),

            Button::add('restore-branch')
                ->slot('Restore')
                ->icon('default-arrow-path', ['class' => 'w-4 h-4 text-amber-500 group-hover:text-amber-700 dark:group-hover:text-amber-400'])
                ->class('group flex items-center gap-1 text-xs font-bold text-amber-500 rounded border border-amber-500 px-2 py-1 hover:text-amber-700 hover:bg-zinc-100 dark:hover:bg-amber-800 dark:hover:text-amber-400 transition-all duration-300 cursor-pointer')
                ->call('confirmRestoreBranch', ['id' => $row->id]),
        ];

    }

    public function actionRules($row): array
    {
        return [
            Rule::button('view-branch')
                ->when(fn ($row) => $row->trashed())
                ->hide(),

            Rule::button('delete-branch')
                ->when(fn ($row) => $row->trashed())
                ->hide(),

            Rule::button('restore-branch')
                ->when(fn ($row) => ! $row->trashed())
                ->hide(),
        ];
    }

    private function isTrashedRow(mixed $row): bool
    {
        if (method_exists($row, 'trashed')) {
            return $row->trashed();
        }

        return filled(data_get($row, 'deleted_at'));
    }

    /**
     * Action Functions
     */
    public function confirmDeleteBranch(array $params): void
    {
        $branchId = (int) $params['id'];
        $this->dialog()->question('Warning!', 'Are you sure you want to delete this branch?')->confirm('Yes, delete', 'delete', $branchId)->cancel('Cancel')->send();
    }

    public function delete($id): void
    {
        Branch::findOrFail($id)->delete();
        $this->toast()->success('Deleted', 'Branch moved to trash.')->send();
        $this->dispatch('pg:eventRefresh-'.$this->tableName);
    }

    public function confirmRestoreBranch(array $params): void
    {
        $branchId = (int) $params['id'];
        $this->dialog()->question('Restore?', 'Are you sure you want to restore this branch?')->confirm('Yes, restore', 'restore', $branchId)->cancel('Cancel')->send();
    }

    public function restore($id): void
    {
        Branch::withTrashed()->findOrFail($id)->restore();
        $this->toast()->success('Restored', 'Branch has been restored.')->send();
        $this->dispatch('pg:eventRefresh-'.$this->tableName);
    }
}
