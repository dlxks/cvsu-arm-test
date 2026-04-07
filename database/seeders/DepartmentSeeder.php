<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Department;
use Illuminate\Database\Seeder;
use RuntimeException;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departmentsByBranch = [
            'CEIT' => [
                ['code' => 'DCS', 'name' => 'Department of Computer Science'],
                ['code' => 'DIT', 'name' => 'Department of Information Technology'],
                ['code' => 'DECE', 'name' => 'Department of Electronics Engineering'],
                ['code' => 'DME', 'name' => 'Department of Mechanical Engineering'],
            ],
            'CAS' => [
                ['code' => 'DBIO', 'name' => 'Department of Biology'],
                ['code' => 'DCHEM', 'name' => 'Department of Chemistry'],
                ['code' => 'DMATH', 'name' => 'Department of Mathematics'],
            ],
            'CEMDS' => [
                ['code' => 'DBA', 'name' => 'Department of Business Administration'],
                ['code' => 'DECON', 'name' => 'Department of Economics'],
                ['code' => 'DDEV', 'name' => 'Department of Development Management'],
            ],
            'CED' => [
                ['code' => 'DEL', 'name' => 'Department of English and Languages'],
                ['code' => 'DFIL', 'name' => 'Department of Filipino'],
                ['code' => 'DSE', 'name' => 'Department of Secondary Education'],
            ],
            'CTHM' => [
                ['code' => 'DHM', 'name' => 'Department of Hospitality Management'],
                ['code' => 'DTM', 'name' => 'Department of Tourism Management'],
            ],
            'CON' => [
                ['code' => 'DNUR', 'name' => 'Department of Nursing'],
            ],
            'CAFENR' => [
                ['code' => 'DAGRI', 'name' => 'Department of Agriculture'],
                ['code' => 'DENV', 'name' => 'Department of Environmental Science'],
                ['code' => 'DFT', 'name' => 'Department of Food Technology'],
            ],
            'CCJ' => [
                ['code' => 'DCRIM', 'name' => 'Department of Criminology'],
            ],
            'COM' => [
                ['code' => 'DMED', 'name' => 'Department of Medicine'],
            ],
            'CSPEAR' => [
                ['code' => 'DPED', 'name' => 'Department of Physical Education'],
                ['code' => 'DSPRT', 'name' => 'Department of Sports Studies'],
            ],
            'CVMBS' => [
                ['code' => 'DVET', 'name' => 'Department of Veterinary Medicine'],
            ],
            'GS-OLC' => [
                ['code' => 'DGRAD', 'name' => 'Graduate Studies Department'],
                ['code' => 'DOLC', 'name' => 'Open Learning Department'],
            ],
            'CAPS' => [
                ['code' => 'DCPE', 'name' => 'Continuing Professional Education Department'],
                ['code' => 'DPRO', 'name' => 'Professional Studies Department'],
            ],
            'CvSU-Bacoor' => [
                ['code' => 'ACAD', 'name' => 'Academic Programs Department'],
                ['code' => 'STUD', 'name' => 'Student Services Department'],
            ],
            'CvSU-Carmona' => [
                ['code' => 'ACAD', 'name' => 'Academic Programs Department'],
                ['code' => 'TECH', 'name' => 'Technology and Innovation Department'],
            ],
            'CvSU-CCC' => [
                ['code' => 'ACAD', 'name' => 'Academic Programs Department'],
                ['code' => 'COMM', 'name' => 'Community Extension Department'],
            ],
            'CvSU-Dasma' => [
                ['code' => 'ACAD', 'name' => 'Academic Programs Department'],
                ['code' => 'BUS', 'name' => 'Business and Entrepreneurship Department'],
            ],
            'CvSU-Gen. Tri' => [
                ['code' => 'ACAD', 'name' => 'Academic Programs Department'],
                ['code' => 'ENG', 'name' => 'Engineering Studies Department'],
            ],
            'CvSU-Imus' => [
                ['code' => 'ACAD', 'name' => 'Academic Programs Department'],
                ['code' => 'ICT', 'name' => 'Information and Communication Technology Department'],
            ],
            'CvSU-Naic' => [
                ['code' => 'ACAD', 'name' => 'Academic Programs Department'],
                ['code' => 'SERV', 'name' => 'Public Service Department'],
            ],
            'CvSU-CCAT' => [
                ['code' => 'ACAD', 'name' => 'Academic Programs Department'],
                ['code' => 'MAR', 'name' => 'Marine and Industrial Technology Department'],
            ],
            'CvSU-Silang' => [
                ['code' => 'ACAD', 'name' => 'Academic Programs Department'],
                ['code' => 'EDU', 'name' => 'Teacher Education Department'],
            ],
            'CvSU-Tanza' => [
                ['code' => 'ACAD', 'name' => 'Academic Programs Department'],
                ['code' => 'BUS', 'name' => 'Business and Hospitality Department'],
            ],
            'CvSU-Trece' => [
                ['code' => 'ACAD', 'name' => 'Academic Programs Department'],
                ['code' => 'ICT', 'name' => 'Information and Communication Technology Department'],
            ],
        ];

        foreach ($departmentsByBranch as $branchCode => $departments) {
            $branch = Branch::where('code', $branchCode)->first();

            if (! $branch) {
                throw new RuntimeException("Unable to seed departments because branch [{$branchCode}] was not found.");
            }

            foreach ($departments as $department) {
                $record = Department::withTrashed()->updateOrCreate(
                    [
                        'branch_id' => $branch->id,
                        'code' => $department['code'],
                    ],
                    [
                        'name' => $department['name'],
                        'is_active' => true,
                    ]
                );

                if ($record->trashed()) {
                    $record->restore();
                }
            }
        }
    }
}
