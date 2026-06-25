<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UdblStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faculties = [
            'Sciences Informatiques' => [
                'code' => 'INFO',
                'departments' => [
                    'Génie Logiciel' => 'GL',
                    'Administration Réseaux et Télécommunication' => 'ART',
                    'Intelligence Artificielle' => 'IA',
                ]
            ],
            'Sciences Économiques et de Gestion' => [
                'code' => 'ECO',
                'departments' => [
                    'Économie Mathématique' => 'EM',
                    'Gestion Financière' => 'GF',
                    'Marketing' => 'MKT',
                ]
            ],
            'Droit' => [
                'code' => 'DROIT',
                'departments' => [
                    'Droit Privé et Judiciaire' => 'DPJ',
                    'Droit Public' => 'DP',
                    'Droit Économique et Social' => 'DES',
                ]
            ],
            'Théologie' => [
                'code' => 'THEO',
                'departments' => [
                    'Théologie Biblique' => 'TB',
                    'Théologie Systématique' => 'TS',
                ]
            ],
        ];

        foreach ($faculties as $facultyName => $facData) {
            $faculty = \App\Models\Faculty::updateOrCreate(
                ['name' => $facultyName],
                ['code' => $facData['code']]
            );

            foreach ($facData['departments'] as $departmentName => $deptCode) {
                \App\Models\Department::updateOrCreate(
                    ['name' => $departmentName],
                    [
                        'code' => $deptCode,
                        'faculty_id' => $faculty->id,
                    ]
                );
            }
        }
    }
}
