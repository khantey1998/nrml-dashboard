<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Surveillance;

class LongitudinalSurveillanceSeeder extends Seeder
{
    public function run(): void
    {
        $start = Carbon::create(2020, 1, 1);
        $end = Carbon::now();

        $provinces = [
            'Phnom Penh',
            'Siem Reap',
            'Battambang',
            'Kampong Cham',
            'Preah Sihanouk'
        ];

        $surveillances = Surveillance::all();

        $current = $start->copy();

        while ($current <= $end) {

            foreach ($surveillances as $surveillance) {

                // seasonal influenza peak (Nov–Feb)
                $month = $current->month;
                $seasonFactor = in_array($month, [11, 12, 1, 2]) ? 1.5 : 1;

                // covid surge 2020–2022
                $covidFactor = ($current->year <= 2022) ? 1.8 : 1;

                $baseCases = rand(5, 15);
                $weeklyCases = (int) ($baseCases * $seasonFactor * $covidFactor);

                for ($i = 0; $i < $weeklyCases; $i++) {

                    $labCode = strtoupper($surveillance->code) .
                        '-' . $current->format('Y') .
                        '-' . uniqid();

                    DB::table('analytic.surveillance_cases')->insert([
                        'lab_code' => $labCode,
                        'is_newcase' => 1,
                        'sentinel_site_name' => 'Sentinel A',
                        'site_province_name' => $provinces[array_rand($provinces)],
                        'surveillance_id' => $surveillance->id,
                        'year_data' => $current->year,
                        'week_data' => $current->week,
                        'case_date' => $current->copy(),
                        'patient_age_inday' => rand(100, 25000),
                        'patient_sex' => rand(0, 1) ? 'M' : 'F',
                        'is_alive' => rand(0, 10) > 1 ? 1 : 0,
                        'patient_privince' => $provinces[array_rand($provinces)]
                    ]);

                    $pathogen = rand(0, 1)
                        ? 'Influenza A'
                        : 'SARS-CoV-2';

                    $isPositive = rand(0, 100) < 35 ? 1 : 0;

                    DB::table('analytic.case_lab_results')->insert([
                        'lab_code' => $labCode,
                        'is_positive' => $isPositive,
                        'pathogen_name' => $pathogen,
                        'subtype' => null,
                        'indicator' => null
                    ]);
                }
            }

            $current->addWeek();
        }
    }
}