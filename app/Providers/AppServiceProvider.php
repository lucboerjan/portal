<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\UtilitySolarPanelReading::observe(\App\Observers\SolarPanelReadingObserver::class);
        \App\Models\InvestmentRate::observe(\App\Observers\InvestmentRateObserver::class);
        \App\Models\FinTransactie::observe(\App\Observers\FinTransactieObserver::class);
        
    }
}
