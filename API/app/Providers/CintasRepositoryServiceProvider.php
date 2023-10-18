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
use Cintas\Repositories\ItemRepository;
use Illuminate\Support\ServiceProvider;

class CintasRepositoryServiceProvider extends ServiceProvider
{

    public function boot(ItemRepository $itemRepository, FacilityRepository $facilityRepository)
    {
        $this->app->singleton(EloquentReaderRepository::class, function ($app) use ($itemRepository, $facilityRepository) {
            return new EloquentReaderRepository($itemRepository, $facilityRepository);
        });

        $this->app->singleton(EloquentFacilityRepository::class, function ($app) use ($itemRepository) {
            return new EloquentFacilityRepository($itemRepository);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(EloquentItemRepository::class, function ($app) {
            return new EloquentItemRepository();
        });

        $this->app->singleton(EloquentItemStatisticsRepository::class, function ($app) {
            return new EloquentItemStatisticsRepository();
        });

        $this->app->singleton(EloquentProductStatisticsRepository::class, function ($app) {
            return new EloquentProductStatisticsRepository();
        });

        $this->app->singleton(EloquentTargetStatisticsRepository::class, function ($app) {
            return new EloquentTargetStatisticsRepository();
        });

        $this->app->singleton(EloquentIdentifierRepository::class, function ($app) {
            return new EloquentIdentifierRepository();
        });


    }
}
