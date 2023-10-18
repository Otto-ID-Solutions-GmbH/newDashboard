<?php

namespace Cintas\Providers;

use Cintas\Facades\Statistics;
use Cintas\Models\Actions\ScanAction;
use Cintas\Models\Customer;
use Cintas\Models\Facility\Bundle;
use Cintas\Models\Facility\Container;
use Cintas\Models\Facility\Facility;
use Cintas\Models\Facility\LaundryCustomer;
use Cintas\Models\Facility\Reader;
use Cintas\Models\Items\Item;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        $morphMap = Relation::morphMap([
            'Item' => Item::class,
            'Reader' => Reader::class,
            'ScanAction' => ScanAction::class,
            'Bundle' => Bundle::class,
            'Container' => Container::class,
            'Facility' => Facility::class,
            'Customer' => Customer::class,
            'LaundryCustomer' => LaundryCustomer::class
        ]);

        Statistics::morphMap($morphMap);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
