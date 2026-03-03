<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Carbon\Carbon;

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
        $dateFrom = $request->query('date_from', Carbon::now()->subDays(7)->toDateString());
        $dateTo = $request->query('date_to', Carbon::now()->toDateString());

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
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

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
        $surveillanceId = $request->query('surveillance_id');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $data = $this->service->provinceDistribution(
            $surveillanceId,
            $dateFrom,
            $dateTo
        );

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