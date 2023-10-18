<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 17.09.2017
 * Time: 13:52
 */

namespace Cintas\Providers;


use Cintas\Services\StatisticsService;
use Illuminate\Support\ServiceProvider;

class StatisticsServiceProvider extends ServiceProvider
{

    public function boot()
    {

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind('statistics', StatisticsService::class);

        $this->app->singleton(StatisticsService::class, function ($app) {
            return new StatisticsService();
        });

    }

}