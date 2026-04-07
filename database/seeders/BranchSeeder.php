<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [
            // --- MAIN CAMPUS COLLEGES ---
            [
                'code' => 'CEIT',
                'name' => 'College of Engineering and Information Technology',
                'type' => 'Main',
                'address' => 'Indang, Cavite',
            ],
            [
                'code' => 'CON',
                'name' => 'College of Nursing',
                'type' => 'Main',
                'address' => 'Indang, Cavite',
            ],
            [
                'code' => 'CAFENR',
                'name' => 'College of Agriculture, Food, Environment and Natural Resources',
                'type' => 'Main',
                'address' => 'Indang, Cavite',
            ],
            [
                'code' => 'CAS',
                'name' => 'College of Arts and Sciences',
                'type' => 'Main',
                'address' => 'Indang, Cavite',
            ],
            [
                'code' => 'CCJ',
                'name' => 'College of Criminal Justice',
                'type' => 'Main',
                'address' => 'Indang, Cavite',
            ],
            [
                'code' => 'CEMDS',
                'name' => 'College of Economics, Management and Development Studies',
                'type' => 'Main',
                'address' => 'Indang, Cavite',
            ],
            [
                'code' => 'CED',
                'name' => 'College of Education',
                'type' => 'Main',
                'address' => 'Indang, Cavite',
            ],
            [
                'code' => 'COM',
                'name' => 'College of Medicine',
                'type' => 'Main',
                'address' => 'Indang, Cavite',
            ],
            [
                'code' => 'CSPEAR',
                'name' => 'College of Sports, Physical Education and Recreation',
                'type' => 'Main',
                'address' => 'Indang, Cavite',
            ],
            [
                'code' => 'CVMBS',
                'name' => 'College of Veterinary Medicine and Biomedical Sciences',
                'type' => 'Main',
                'address' => 'Indang, Cavite',
            ],
            [
                'code' => 'GS-OLC',
                'name' => 'Graduate School and Open Learning College',
                'type' => 'Main',
                'address' => 'Indang, Cavite',
            ],
            [
                'code' => 'CAPS',
                'name' => 'College of Advanced and Professional Studies',
                'type' => 'Main',
                'address' => 'Indang, Cavite',
            ],
            [
                'code' => 'CTHM',
                'name' => 'College of Tourism and Hospitality Management',
                'type' => 'Main',
                'address' => 'Indang, Cavite',
            ],

            // --- SATELLITE CAMPUSES ---
            [
                'code' => 'CvSU-Bacoor',
                'name' => 'CvSU Bacoor City Campus',
                'type' => 'Satellite',
                'address' => 'Bacoor, Cavite',
            ],
            [
                'code' => 'CvSU-Carmona',
                'name' => 'CvSU Carmona Campus',
                'type' => 'Satellite',
                'address' => 'Carmona, Cavite',
            ],
            [
                'code' => 'CvSU-CCC',
                'name' => 'CvSU Cavite City Campus',
                'type' => 'Satellite',
                'address' => 'Cavite City, Cavite',
            ],
            [
                'code' => 'CvSU-Dasma',
                'name' => 'CvSU Dasmarinas City Campus',
                'type' => 'Satellite',
                'address' => 'Dasmarinas, Cavite',
            ],
            [
                'code' => 'CvSU-Gen. Tri',
                'name' => 'CvSU General Trias City Campus',
                'type' => 'Satellite',
                'address' => 'General Trias, Cavite',
            ],
            [
                'code' => 'CvSU-Imus',
                'name' => 'CvSU Imus Campus',
                'type' => 'Satellite',
                'address' => 'Imus, Cavite',
            ],
            [
                'code' => 'CvSU-Naic',
                'name' => 'CvSU Naic Campus',
                'type' => 'Satellite',
                'address' => 'Naic, Cavite',
            ],
            [
                'code' => 'CvSU-CCAT',
                'name' => 'CvSU Rosario Campus',
                'type' => 'Satellite',
                'address' => 'Rosario, Cavite',
            ],
            [
                'code' => 'CvSU-Silang',
                'name' => 'CvSU Silang Campus',
                'type' => 'Satellite',
                'address' => 'Silang, Cavite',
            ],
            [
                'code' => 'CvSU-Tanza',
                'name' => 'CvSU Tanza Campus',
                'type' => 'Satellite',
                'address' => 'Tanza, Cavite',
            ],
            [
                'code' => 'CvSU-Trece',
                'name' => 'CvSU Trece Martires City Campus',
                'type' => 'Satellite',
                'address' => 'Trece Martires, Cavite',
            ],
        ];

        foreach ($branches as $branch) {
            $record = Branch::withTrashed()->updateOrCreate(
                ['code' => $branch['code']],
                array_merge($branch, ['is_active' => true])
            );

            if ($record->trashed()) {
                $record->restore();
            }
        }
    }
}
