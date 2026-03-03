<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Surveillance;
use App\Models\SurveillanceCase;
use App\Models\CaseLabResult;

class DashboardService
{
    /**
     * Get all surveillance programs
     */
    public function getPrograms()
    {
        return Surveillance::orderBy('id')->get();
    }

    /**
     * Summary cards (dynamic)
     */
    public function summaryCards($dateFrom, $dateTo)
    {
        $programs = $this->getPrograms();
        $results = [];

        $days = Carbon::parse($dateFrom)->diffInDays($dateTo);

        foreach ($programs as $program) {

            $current = SurveillanceCase::where('surveillance_id', $program->id)
                ->whereBetween('case_date', [$dateFrom, $dateTo])
                ->count();

            $previousFrom = Carbon::parse($dateFrom)->subDays($days + 1);
            $previousTo = Carbon::parse($dateFrom)->subDay();

            $previous = SurveillanceCase::where('surveillance_id', $program->id)
                ->whereBetween('case_date', [$previousFrom, $previousTo])
                ->count();

            $percentChange = $previous > 0
                ? round((($current - $previous) / $previous) * 100, 1)
                : 0;

            $last24h = SurveillanceCase::where('surveillance_id', $program->id)
                ->where('case_date', '>=', Carbon::now()->subDay())
                ->count();

            $results[] = [
                'surveillance_id' => $program->id,
                'code' => $program->code,
                'name_en' => $program->name_en,
                'name_kh' => $program->name_kh,
                'current_total' => $current,
                'percent_change' => $percentChange,
                'last_24h' => $last24h,
            ];
        }

        return $results;
    }

    /**
     * Aggregate cases by period
     * periodType: week | month | year
     */

    public function aggregateAllPrograms($periodType, $dateFrom, $dateTo)
    {
        $programs = Surveillance::all();

        $results = [];

        foreach ($programs as $program) {

            $query = SurveillanceCase::where('surveillance_id', $program->id)
                ->whereBetween('case_date', [$dateFrom, $dateTo]);

            switch ($periodType) {

                case 'week':
                    $query->selectRaw("
                    YEAR(case_date) as year,
                    WEEK(case_date, 3) as period,
                    COUNT(*) as total
                ")
                        ->groupByRaw("YEAR(case_date), WEEK(case_date, 3)")
                        ->orderByRaw("YEAR(case_date), WEEK(case_date, 3)");
                    break;

                case 'month':
                    $query->selectRaw("
                    YEAR(case_date) as year,
                    MONTH(case_date) as period,
                    COUNT(*) as total
                ")
                        ->groupByRaw("YEAR(case_date), MONTH(case_date)")
                        ->orderByRaw("YEAR(case_date), MONTH(case_date)");
                    break;

                case 'year':
                    $query->selectRaw("
                    YEAR(case_date) as period,
                    COUNT(*) as total
                ")
                        ->groupByRaw("YEAR(case_date)")
                        ->orderByRaw("YEAR(case_date)");
                    break;
            }

            $results[$program->code] = $query->get();
        }

        return $results;
    }
    public function aggregateCases($surveillanceId, $periodType, $dateFrom, $dateTo)
    {
        $query = SurveillanceCase::where('surveillance_id', $surveillanceId)
            ->whereBetween('case_date', [$dateFrom, $dateTo]);

        switch ($periodType) {

            case 'week':
                $query->selectRaw("
                    YEAR(case_date) as year,
                    WEEK(case_date, 3) as period,
                    COUNT(*) as total
                ")
                    ->groupByRaw("YEAR(case_date), WEEK(case_date, 3)")
                    ->orderByRaw("YEAR(case_date), WEEK(case_date, 3)");
                break;

            case 'month':
                $query->selectRaw("
                    YEAR(case_date) as year,
                    MONTH(case_date) as period,
                    COUNT(*) as total
                ")
                    ->groupByRaw("YEAR(case_date), MONTH(case_date)")
                    ->orderByRaw("YEAR(case_date), MONTH(case_date)");
                break;

            case 'year':
                $query->selectRaw("
                    YEAR(case_date) as period,
                    COUNT(*) as total
                ")
                    ->groupByRaw("YEAR(case_date)")
                    ->orderByRaw("YEAR(case_date)");
                break;
        }

        return $query->get();
    }

    /**
     * Province distribution
     */
    public function provinceDistribution($surveillanceId, $dateFrom, $dateTo)
    {
        return SurveillanceCase::selectRaw("
                site_province_name,
                COUNT(*) as total
            ")
            ->where('surveillance_id', $surveillanceId)
            ->whereBetween('case_date', [$dateFrom, $dateTo])
            ->groupBy('site_province_name')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Pathogen distribution (positive only)
     */
    public function pathogenDistribution($surveillanceId, $dateFrom, $dateTo)
    {
        return CaseLabResult::selectRaw("
                case_lab_results.pathogen_name,
                COUNT(*) as total
            ")
            ->join('surveillance_cases', 'case_lab_results.lab_code', '=', 'surveillance_cases.lab_code')
            ->where('surveillance_cases.surveillance_id', $surveillanceId)
            ->whereBetween('surveillance_cases.case_date', [$dateFrom, $dateTo])
            ->where('case_lab_results.is_positive', 1)
            ->groupBy('case_lab_results.pathogen_name')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Positivity rate
     */
    public function positivityRate($surveillanceId, $dateFrom, $dateTo)
    {
        $total = CaseLabResult::join('surveillance_cases', 'case_lab_results.lab_code', '=', 'surveillance_cases.lab_code')
            ->where('surveillance_cases.surveillance_id', $surveillanceId)
            ->whereBetween('surveillance_cases.case_date', [$dateFrom, $dateTo])
            ->count();

        $positive = CaseLabResult::join('surveillance_cases', 'case_lab_results.lab_code', '=', 'surveillance_cases.lab_code')
            ->where('surveillance_cases.surveillance_id', $surveillanceId)
            ->whereBetween('surveillance_cases.case_date', [$dateFrom, $dateTo])
            ->where('case_lab_results.is_positive', 1)
            ->count();

        return $total > 0
            ? round(($positive / $total) * 100, 1)
            : 0;
    }

    /**
     * Age distribution (grouped)
     */
    public function ageDistribution($surveillanceId, $dateFrom, $dateTo)
    {
        return SurveillanceCase::selectRaw("
                CASE
                    WHEN patient_age_inday < 365 THEN '0-1y'
                    WHEN patient_age_inday < 1825 THEN '1-5y'
                    WHEN patient_age_inday < 6570 THEN '5-18y'
                    WHEN patient_age_inday < 21900 THEN '18-60y'
                    ELSE '60+y'
                END as age_group,
                COUNT(*) as total
            ")
            ->where('surveillance_id', $surveillanceId)
            ->whereBetween('case_date', [$dateFrom, $dateTo])
            ->groupBy('age_group')
            ->get();
    }

    /**
     * Sex distribution
     */
    public function sexDistribution($surveillanceId, $dateFrom, $dateTo)
    {
        return SurveillanceCase::selectRaw("
                patient_sex,
                COUNT(*) as total
            ")
            ->where('surveillance_id', $surveillanceId)
            ->whereBetween('case_date', [$dateFrom, $dateTo])
            ->groupBy('patient_sex')
            ->get();
    }
}