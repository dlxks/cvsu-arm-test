<?php

namespace App\Livewire\Admin\Tables;

use App\Models\Room;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class RoomsTable extends PowerGridComponent
{
    public string $tableName = 'roomsTable';

    public function boot(): void
    {
        config(['livewire-powergrid.filter' => 'outside']);
    }

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::exportable(fileName: 'rooms-list')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),

            PowerGrid::header()
                ->showSearchInput()
                ->showToggleColumns(),

            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Room::query()
            ->with(['campus', 'college', 'department']);
    }

    public function relationSearch(): array
    {
        return [
            'campus' => ['name'],
            'college' => ['name'],
            'department' => ['name'],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('display_name', fn (Room $model) => $model->display_name)
            ->add('floor_label', fn (Room $model) => filled($model->floor_no) ? 'Floor '.$model->floor_no : '-')
            ->add('type_label', fn (Room $model) => $model->type_label)
            ->add('campus_name', fn (Room $model) => $model->campus?->name ?? '-')
            ->add('college_name', fn (Room $model) => $model->college?->name ?? '-')
            ->add('department_name', fn (Room $model) => $model->department?->name ?? '-')
            ->add('location_text', fn (Room $model) => $model->location ?: '-')
            ->add('description_text', fn (Room $model) => $model->description ?: '-')
            ->add('availability', fn (Room $model) => $model->is_active ? 'Active' : 'Inactive')
            ->add('status_label', fn (Room $model) => $model->status_label);
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->hidden(isHidden: true, isForceHidden: false),
            Column::make('Room', 'display_name', 'name')
                ->sortable()
                ->searchable(),
            Column::make('Type', 'type_label', 'type')
                ->sortable()
                ->searchable(),
            Column::make('Floor', 'floor_label', 'floor_no')
                ->sortable()
                ->searchable(),
            Column::make('Campus', 'campus_name')
                ->hidden(isHidden: true, isForceHidden: false)
                ->searchable(),
            Column::make('College', 'college_name')
                ->hidden(isHidden: true, isForceHidden: false)
                ->searchable(),
            Column::make('Department', 'department_name')
                ->searchable(),
            Column::make('Location', 'location_text')
                ->searchable(),
            Column::make('Description', 'description_text')
                ->hidden(isHidden: true, isForceHidden: false)
                ->searchable(),
            Column::make('Availability', 'availability', 'is_active')
                ->sortable(),
            Column::make('Status', 'status_label', 'status')
                ->sortable()
                ->searchable(),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::select('type')
                ->dataSource($this->enumOptions(Room::TYPES))
                ->optionValue('id')
                ->optionLabel('name')
                ->builder(fn (Builder $query, $value) => filled($value) ? $query->where('type', $value) : $query),

            Filter::select('status')
                ->dataSource($this->enumOptions(Room::STATUSES))
                ->optionValue('id')
                ->optionLabel('name')
                ->builder(fn (Builder $query, $value) => filled($value) ? $query->where('status', $value) : $query),

            Filter::select('is_active')
                ->dataSource([
                    ['id' => 1, 'name' => 'Active'],
                    ['id' => 0, 'name' => 'Inactive'],
                ])
                ->optionValue('id')
                ->optionLabel('name')
                ->builder(fn (Builder $query, $value) => filled($value) ? $query->where('is_active', (int) $value) : $query),
        ];
    }

    protected function enumOptions(array $options): array
    {
        return collect($options)
            ->map(fn (string $name, string $id) => [
                'id' => $id,
                'name' => $name,
            ])
            ->values()
            ->all();
    }
}
