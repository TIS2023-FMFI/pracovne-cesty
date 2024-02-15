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
                'fund' => '111',
                'functional_region' => '09413',
                'grantee' => 'doc. RNDr. Tatiana Jajcayová, PhD.'
            ],
            [
                'spp_symbol' => 'O-06-107/0008-00',
                'fund' => '46',
                'functional_region' => '09413',
                'grantee' => 'doc. RNDr. Tatiana Jajcayová, PhD.'
            ],
            [
                'spp_symbol' => 'Z-22-107/0001-04',
                'fund' => '13GR',
                'functional_region' => '09413',
                'grantee' => 'prof. Ing. Igor Farkaš, Dr.'
            ]
        ]);
    }
}
