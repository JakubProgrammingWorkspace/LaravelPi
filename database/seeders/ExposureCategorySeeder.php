<?php

namespace Database\Seeders;

use App\Models\ExposureCategory;
use App\Models\ExposureFactor;
use Illuminate\Database\Seeder;

class ExposureCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeds exactly 5 exposure categories matching Annex 3 of
     * Rozporządzenie Ministra Zdrowia i Opieki Społecznej z dnia 30 maja 1996 r.
     */
    public function run(): void
    {
        $categories = [
            [
                'code'       => 'I',
                'name'       => 'Czynniki fizyczne',
                'sort_order' => 1,
                'factors'    => [
                    'Hałas',
                    'Hałas ultradźwiękowy',
                    'Wibracje ogólne',
                    'Wibracje miejscowe',
                    'Mikroklimat gorący',
                    'Mikroklimat zimny',
                    'Promieniowanie optyczne',
                    'Promieniowanie laserowe',
                    'Promieniowanie jonizujące',
                    'Pole elektromagnetyczne',
                    'Praca przy monitorach >4h',
                ],
            ],
            [
                'code'       => 'II',
                'name'       => 'Pyły',
                'sort_order' => 2,
                'factors'    => [
                    'Azbest',
                    'Krzemionka',
                    'Pyły drewna',
                    'Rudy metali',
                    'Pyły węgla',
                    'Pyły mączne',
                    'Pyły bawełny',
                    'Pyły zwierzęce/roślinne',
                ],
            ],
            [
                'code'       => 'III',
                'name'       => 'Czynniki chemiczne',
                'sort_order' => 3,
                'factors'    => [
                    'Czynniki toksyczne',
                    'Czynniki drażniące',
                    'Czynniki uczulające',
                    'Czynniki rakotwórcze/mutagenne',
                    'Czynniki szkodliwe dla rozrodczości',
                    'Rozpuszczalniki organiczne',
                    'Metale i związki metali',
                    'Pestycydy',
                    'Tlenku węgla/azotu',
                ],
            ],
            [
                'code'       => 'IV',
                'name'       => 'Czynniki biologiczne',
                'sort_order' => 4,
                'factors'    => [
                    'Wirusy',
                    'Bakterie',
                    'Grzyby',
                    'Pasożyty',
                    'Materiał biologiczny ludzki',
                    'Mikroorganizmy rolnicze/leśne',
                    'Kontakt ze zwierzętami',
                ],
            ],
            [
                'code'       => 'V',
                'name'       => 'Inne czynniki niebezpieczne',
                'sort_order' => 5,
                'factors'    => [
                    'Praca na wysokości',
                    'Obsługa maszyn',
                    'Wymuszona pozycja',
                    'Obciążenie statyczne/dynamiczne',
                    'Praca nocna/zmianowa',
                    'Obciążenie psychiczne',
                    'Praca przy urządzeniach pod napięciem',
                    'Niewystarczająca widoczność',
                    'Ryzyko poślizgnięcia',
                ],
            ],
        ];

        foreach ($categories as $cat) {
            $category = ExposureCategory::firstOrCreate(
                ['code' => $cat['code']],
                [
                    'name'       => $cat['name'],
                    'sort_order' => $cat['sort_order'],
                ]
            );

            foreach ($cat['factors'] as $factorName) {
                ExposureFactor::firstOrCreate(
                    [
                        'exposure_category_id' => $category->id,
                        'name'                 => $factorName,
                    ],
                    ['name' => $factorName]
                );
            }
        }
    }
}
