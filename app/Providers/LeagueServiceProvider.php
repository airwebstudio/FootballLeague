<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\League\LeagueSimulation;

class LeagueServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(LeagueSimulation::class, function ($app) {
            return new LeagueSimulation();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

