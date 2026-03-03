<?php

namespace App\Http\Controllers;

use App\Models\Surveillance;

class DashboardController extends Controller
{
    public function overview()
    {
        $programs = Surveillance::all();
        return view('dashboard.overview', compact('programs'));
    }

    public function detail($code)
    {
        $selected = Surveillance::where('code', strtoupper($code))->firstOrFail();
        return view('dashboard.detail', compact('selected'));
    }
}