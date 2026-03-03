<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Surveillance;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            $view->with('programs', Surveillance::orderBy('id')->get());
        });
    }
}
