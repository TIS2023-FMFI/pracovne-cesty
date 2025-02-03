<?php

namespace Database\Seeders;

use App\Models\SppSymbol;
use Illuminate\Database\Seeder;

class SppSymbolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SppSymbol::insert([
            [
                'spp_symbol' => '0771100',
                'functional_region' => '09413',
                'grantee' => $defaultUser->id ?? 1,
                'agency' => 'Ministerstvo školstva',
                'acronym' => 'MŠ'
            ],
            [
                'spp_symbol' => 'O-06-107/0008-00',
                'functional_region' => '09413',
                'grantee' => $defaultUser->id ?? 1,
                'agency' => 'Agentúra životného prostredia',
                'acronym' => 'AZP'
            ],
            [
                'spp_symbol' => 'Z-22-107/0001-04',
                'functional_region' => '09413',
                'grantee' => $defaultUser->id ?? 1,
                'agency' => 'Výskumný fond',
                'acronym' => 'VF'
            ]
        ]);
    }
}
