<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Surveillance;

class SurveillanceSeeder extends Seeder
{
    public function run(): void
    {
        Surveillance::insert([
            [
                'code' => 'SARI',
                'name_en' => 'Severe Acute Respiratory Infection',
                'name_kh' => 'SARI',
                'start_date' => '2026-01-01',
                'end_date' => null
            ],
            [
                'code' => 'ILI',
                'name_en' => 'Influenza Like Illness',
                'name_kh' => 'ILI',
                'start_date' => '2026-01-01',
                'end_date' => null
            ],
            [
                'code' => 'AFI',
                'name_en' => 'Acute Febrile Illness',
                'name_kh' => 'AFI',
                'start_date' => '2026-01-01',
                'end_date' => null
            ],
        ]);
    }
}