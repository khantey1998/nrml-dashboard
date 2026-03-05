<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Carbon\Carbon;
use App\Models\SurveillanceCase;

class DashboardController extends Controller
{
    protected $service;

    public function index()
    {
        $programs = \App\Models\Surveillance::all();
        return view('dashboard.index', compact('programs'));
    }
    public function __construct(DashboardService $service)
    {
        $this->service = $service;
    }

    /**
     * Summary cards
     * GET /api/dashboard/summary?date_from=2026-01-01&date_to=2026-03-01
     */
    public function summary(Request $request)
    {
        if ($request->has('start_year')) {

            $startYear = $request->query('start_year');
            $startWeek = $request->query('start_week');

            $endYear = $request->query('end_year');
            $endWeek = $request->query('end_week');

            $dateFrom = Carbon::now()->setISODate($startYear, $startWeek)->startOfWeek()->toDateString();
            $dateTo = Carbon::now()->setISODate($endYear, $endWeek)->endOfWeek()->toDateString();

        } else {

            $dateFrom = $request->query('date_from', Carbon::now()->subDays(7)->toDateString());
            $dateTo = $request->query('date_to', Carbon::now()->toDateString());

        }

        $data = $this->service->summaryCards($dateFrom, $dateTo);

        return response()->json($data);
    }

    /**
     * Trend chart
     * GET /api/dashboard/trend?surveillance_id=1&period_type=week&date_from=...&date_to=...
     */
    public function trend(Request $request)
    {
        $periodType = $request->query('period_type', 'week');

        if ($request->has('start_year')) {

            $startYear = $request->query('start_year');
            $startWeek = $request->query('start_week');

            $endYear = $request->query('end_year');
            $endWeek = $request->query('end_week');

            $dateFrom = Carbon::now()->setISODate($startYear, $startWeek)->startOfWeek()->toDateString();
            $dateTo = Carbon::now()->setISODate($endYear, $endWeek)->endOfWeek()->toDateString();

        } else {

            $dateFrom = $request->query('date_from');
            $dateTo = $request->query('date_to');

        }

        $data = $this->service->aggregateAllPrograms(
            $periodType,
            $dateFrom,
            $dateTo
        );

        return response()->json($data);
    }

    /**
     * Province distribution
     */
    public function province(Request $request)
    {
        if ($request->has('start_year')) {

            $startYear = $request->query('start_year');
            $startWeek = $request->query('start_week');

            $endYear = $request->query('end_year');
            $endWeek = $request->query('end_week');

            $dateFrom = Carbon::now()
                ->setISODate($startYear, $startWeek)
                ->startOfWeek()
                ->toDateString();

            $dateTo = Carbon::now()
                ->setISODate($endYear, $endWeek)
                ->endOfWeek()
                ->toDateString();

        } else {

            $dateFrom = $request->query('date_from');
            $dateTo = $request->query('date_to');

        }

        $rows = $this->service->provinceDistribution($dateFrom, $dateTo);

        $result = [];

        foreach ($rows as $row) {
            $result[$row->site_province_name] = $row->total;
        }

        return response()->json($result);
    }
    public function sentinelMap(Request $request)
    {
        $startYear = $request->query('start_year');
        $startWeek = $request->query('start_week');

        $endYear = $request->query('end_year');
        $endWeek = $request->query('end_week');

        $dateFrom = Carbon::now()->setISODate($startYear, $startWeek)->startOfWeek();
        $dateTo = Carbon::now()->setISODate($endYear, $endWeek)->endOfWeek();

        $data = $this->service->sentinelMap($dateFrom, $dateTo);

        return response()->json($data);
    }
    public function provinceCircles(Request $request)
    {
        $startYear = $request->query('start_year');
        $startWeek = $request->query('start_week');

        $endYear = $request->query('end_year');
        $endWeek = $request->query('end_week');

        $dateFrom = Carbon::now()->setISODate($startYear, $startWeek)->startOfWeek();
        $dateTo = Carbon::now()->setISODate($endYear, $endWeek)->endOfWeek();

        $data = $this->service->provinceCircles($dateFrom, $dateTo);

        return response()->json($data);
    }
    /**
     * Pathogen distribution
     */
    public function pathogen(Request $request)
    {
        $surveillanceId = $request->query('surveillance_id');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $data = $this->service->pathogenDistribution(
            $surveillanceId,
            $dateFrom,
            $dateTo
        );

        return response()->json($data);
    }
}