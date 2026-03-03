<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});



Route::get('/dashboard', [DashboardController::class, 'overview']);
Route::get('/dashboard/{code}', [DashboardController::class, 'detail']);