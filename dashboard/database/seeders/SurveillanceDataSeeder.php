<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\Surveillance;
use App\Models\SurveillanceCase;
use App\Models\CaseLabResult;

class SurveillanceDataSeeder extends Seeder
{
    public function run(): void
    {
        $provinces = ['Phnom Penh', 'Siem Reap', 'Battambang', 'Kampong Cham'];
        $pathogens = ['Influenza A', 'Influenza B', 'SARS-CoV-2'];

        $programs = Surveillance::all();

        foreach ($programs as $program) {

            for ($i = 0; $i < 300; $i++) {

                $date = Carbon::now()->subDays(rand(0, 90));

                $case = SurveillanceCase::create([
                    'lab_code' => uniqid('LAB'),
                    'case_date' => $date->toDateString(),
                    'is_newcase' => 1,
                    'sentinel_site_name' => 'Sentinel Site ' . rand(1, 5),
                    'site_province_name' => $provinces[array_rand($provinces)],
                    'surveillance_id' => $program->id,
                    'year_data' => $date->year,
                    'week_data' => $date->weekOfYear,
                    'patient_age_inday' => rand(100, 25000),
                    'patient_sex' => rand(0, 1) ? 'M' : 'F',
                    'is_alive' => 1,
                    'patient_privince' => $provinces[array_rand($provinces)]
                ]);

                foreach ($pathogens as $pathogen) {

                    $positiveRate = $pathogen === 'Influenza A' ? 0.2 :
                                   ($pathogen === 'Influenza B' ? 0.15 : 0.1);

                    CaseLabResult::create([
                        'lab_code' => $case->lab_code,
                        'is_positive' => rand(0, 100) < ($positiveRate * 100) ? 1 : 0,
                        'pathogen_name' => $pathogen,
                        'subtype' => null,
                        'indicator' => null
                    ]);
                }
            }
        }
    }
}