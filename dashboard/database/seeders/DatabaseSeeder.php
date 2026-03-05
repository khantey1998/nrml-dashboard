<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\LongitudinalSurveillanceSeeder;
use Database\Seeders\SurveillanceSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(
            [
                SurveillanceSeeder::class,
                LongitudinalSurveillanceSeeder::class,
            ]
        );
    }
}
