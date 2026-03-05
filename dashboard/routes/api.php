<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardController;

Route::get('/dashboard/summary', [DashboardController::class, 'summary']);
Route::get('/dashboard/trend', [DashboardController::class, 'trend']);
Route::get('/dashboard/province', [DashboardController::class, 'province']);
Route::get('/dashboard/pathogen', [DashboardController::class, 'pathogen']);
Route::get('/dashboard/sentinel-map', [DashboardController::class, 'sentinelMap']);
Route::get('/dashboard/province-circles', [DashboardController::class, 'provinceCircles']);