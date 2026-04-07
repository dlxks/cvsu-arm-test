<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Department;
use Illuminate\Support\Arr;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departmentTemplates = [
            ['code' => 'ACAD', 'name' => 'Academic Programs Department'],
            ['code' => 'STUD', 'name' => 'Student Services Department'],
            ['code' => 'REXT', 'name' => 'Research and Extension Department'],
            ['code' => 'QA', 'name' => 'Planning and Quality Assurance Department'],
            ['code' => 'ADMS', 'name' => 'Administrative Services Department'],
        ];

        Branch::query()
            ->orderBy('type')
            ->orderBy('name')
            ->get()
            ->each(function (Branch $branch) use ($departmentTemplates): void {
                foreach ($departmentTemplates as $template) {
                    $department = Department::factory()
                        ->forBranch($branch)
                        ->state([
                            'code' => sprintf('%s-%02d', $template['code'], $branch->id),
                            'name' => $template['name'],
                            'is_active' => true,
                        ])
                        ->make();

                    $record = Department::withTrashed()->updateOrCreate(
                        [
                            'branch_id' => $branch->id,
                            'code' => $department->code,
                        ],
                        Arr::only($department->getAttributes(), ['name', 'is_active'])
                    );

                    if ($record->trashed()) {
                        $record->restore();
                    }
                }
            });
    }
}
