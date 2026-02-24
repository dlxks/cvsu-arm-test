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
            // --- MAIN CAMPUS COLLEGES (CvSU-M...) ---
            [
                'branch_id' => 'CvSU-M001',
                'code' => 'CEIT',
                'name' => 'CvSU Main - College of Engineering and Information Technology',
                'type' => 'Main',
                'address' => 'Indang, Cavite',
            ],
            [
                'branch_id' => 'CvSU-M002',
                'code' => 'CAS',
                'name' => 'CvSU Main - College of Arts and Sciences',
                'type' => 'Main',
                'address' => 'Indang, Cavite',
            ],
            [
                'branch_id' => 'CvSU-M003',
                'code' => 'CAFENR',
                'name' => 'CvSU Main - College of Agriculture, Food, Environment and Natural Resources',
                'type' => 'Main',
                'address' => 'Indang, Cavite',
            ],
            [
                'branch_id' => 'CvSU-M004',
                'code' => 'CCJ',
                'name' => 'CvSU Main - College of Criminal Justice',
                'type' => 'Main',
                'address' => 'Indang, Cavite',
            ],
            [
                'branch_id' => 'CvSU-M005',
                'code' => 'CED',
                'name' => 'CvSU Main - College of Education',
                'type' => 'Main',
                'address' => 'Indang, Cavite',
            ],
            [
                'branch_id' => 'CvSU-M006',
                'code' => 'CEMDS',
                'name' => 'CvSU Main - College of Economics, Management and Development Studies',
                'type' => 'Main',
                'address' => 'Indang, Cavite',
            ],
            [
                'branch_id' => 'CvSU-M007',
                'code' => 'CON',
                'name' => 'CvSU Main - College of Nursing',
                'type' => 'Main',
                'address' => 'Indang, Cavite',
            ],
            [
                'branch_id' => 'CvSU-M008',
                'code' => 'CSPEAR',
                'name' => 'CvSU Main - College of Sports, Physical Education and Recreation',
                'type' => 'Main',
                'address' => 'Indang, Cavite',
            ],
            [
                'branch_id' => 'CvSU-M009',
                'code' => 'CVMBS',
                'name' => 'CvSU Main - College of Veterinary Medicine and Biomedical Sciences',
                'type' => 'Main',
                'address' => 'Indang, Cavite',
            ],
            [
                'branch_id' => 'CvSU-M010',
                'code' => 'GS',
                'name' => 'CvSU Main - Graduate School',
                'type' => 'Main',
                'address' => 'Indang, Cavite',
            ],

            // --- SATELLITE CAMPUSES (CvSU-S...) ---
            [
                'branch_id' => 'CvSU-S001',
                'code' => 'SILANG',
                'name' => 'CvSU Silang Campus',
                'type' => 'Satellite',
                'address' => 'Silang, Cavite',
            ],
            [
                'branch_id' => 'CvSU-S002',
                'code' => 'BACOOR',
                'name' => 'CvSU Bacoor City Campus',
                'type' => 'Satellite',
                'address' => 'Bacoor, Cavite',
            ],
            [
                'branch_id' => 'CvSU-S003',
                'code' => 'CAVITE',
                'name' => 'CvSU Cavite City Campus',
                'type' => 'Satellite',
                'address' => 'Cavite City, Cavite',
            ],
            [
                'branch_id' => 'CvSU-S004',
                'code' => 'CARMONA',
                'name' => 'CvSU Carmona Campus',
                'type' => 'Satellite',
                'address' => 'Carmona, Cavite',
            ],
            [
                'branch_id' => 'CvSU-S005',
                'code' => 'GEN-TRIAS',
                'name' => 'CvSU General Trias City Campus',
                'type' => 'Satellite',
                'address' => 'General Trias, Cavite',
            ],
            [
                'branch_id' => 'CvSU-S006',
                'code' => 'IMUS',
                'name' => 'CvSU Imus Campus',
                'type' => 'Satellite',
                'address' => 'Imus, Cavite',
            ],
            [
                'branch_id' => 'CvSU-S007',
                'code' => 'TRECE',
                'name' => 'CvSU Trece Martires City Campus',
                'type' => 'Satellite',
                'address' => 'Trece Martires, Cavite',
            ],
            [
                'branch_id' => 'CvSU-S008',
                'code' => 'NAIC',
                'name' => 'CvSU Naic Campus',
                'type' => 'Satellite',
                'address' => 'Naic, Cavite',
            ],
            [
                'branch_id' => 'CvSU-S009',
                'code' => 'ROSARIO',
                'name' => 'CvSU Rosario Campus',
                'type' => 'Satellite',
                'address' => 'Rosario, Cavite',
            ],
            [
                'branch_id' => 'CvSU-S010',
                'code' => 'TANZA',
                'name' => 'CvSU Tanza Campus',
                'type' => 'Satellite',
                'address' => 'Tanza, Cavite',
            ],
            [
                'branch_id' => 'CvSU-S011',
                'code' => 'MARAGONDON',
                'name' => 'CvSU Maragondon Campus',
                'type' => 'Satellite',
                'address' => 'Maragondon, Cavite',
            ],
        ];

        foreach ($branches as $branch) {
            Branch::updateOrCreate(
                ['branch_id' => $branch['branch_id']],
                $branch
            );
        }
    }
}
