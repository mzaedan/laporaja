<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\ReportStatus;
use App\Observers\ReportStatusObserver;

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
        ReportStatus::observe(ReportStatusObserver::class);
    }
}
