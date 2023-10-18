<?php

namespace Cintas\Providers;

use Cintas\Repositories\EloquentFacilityRepository;
use Cintas\Repositories\EloquentIdentifierRepository;
use Cintas\Repositories\EloquentItemRepository;
use Cintas\Repositories\EloquentItemStatisticsRepository;
use Cintas\Repositories\EloquentProductStatisticsRepository;
use Cintas\Repositories\EloquentReaderRepository;
use Cintas\Repositories\EloquentTargetStatisticsRepository;
use Cintas\Repositories\FacilityRepository;
use Cintas\Repositories\IdentifierRepository;
use Cintas\Repositories\ItemRepository;
use Cintas\Repositories\ItemStatisticsRepository;
use Cintas\Repositories\ProductStatisticsRepository;
use Cintas\Repositories\ReaderRepository;
use Cintas\Repositories\TargetStatisticsRepository;
use Illuminate\Support\ServiceProvider;


class EloquentBackendProvider extends ServiceProvider
{

    protected $defer = true;

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind(ItemRepository::class, EloquentItemRepository::class);
        $this->app->bind(FacilityRepository::class, EloquentFacilityRepository::class);
        $this->app->bind(ReaderRepository::class, EloquentReaderRepository::class);
        $this->app->bind(ItemStatisticsRepository::class, EloquentItemStatisticsRepository::class);
        $this->app->bind(ProductStatisticsRepository::class, EloquentProductStatisticsRepository::class);
        $this->app->bind(TargetStatisticsRepository::class, EloquentTargetStatisticsRepository::class);
        $this->app->bind(IdentifierRepository::class, EloquentIdentifierRepository::class);


    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [ItemRepository::class, FacilityRepository::class, ReaderRepository::class, ItemStatisticsRepository::class, ProductStatisticsRepository::class, TargetStatisticsRepository::class];
    }
}
