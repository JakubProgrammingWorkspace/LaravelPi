<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::firstOrCreate(
            ['nip' => '5250001394'],
            [
                'name'        => 'Przedsiębiorstwo Hazlowe „Baltyk" Sp. z o.o.',
                'street'      => 'ul. Portowa 15',
                'city'        => 'Gdańsk',
                'postal_code' => '80-123',
            ]
        );

        Company::firstOrCreate(
            ['nip' => '6462361421'],
            [
                'name'        => 'Fabryka Maszyn S.A.',
                'street'      => 'ul. Przemysłowa 42',
                'city'        => 'Katowice',
                'postal_code' => '40-567',
            ]
        );
    }
}
