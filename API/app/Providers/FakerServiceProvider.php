<?php

namespace Cintas\Providers;

use Illuminate\Support\ServiceProvider;

class FakerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Register Faker singleton Can be used with $faker = app('Faker');
        $this->app->singleton('Faker', function ($app) {
            $faker = \Faker\Factory::create();
            $newClass = new class($faker) extends \Faker\Provider\Base
            {

                public function cintasProduct()
                {
                    $choice = ['Wipe, orange', 'Wipe, blue', 'Wipe, yellow', 'Wipe, green', 'Mop, large', 'Mop, small', 'Mop, extra large'];
                    return $this->randomElement($choice);
                }

                public function cintasProductType()
                {
                    $choice = ['Wipe', 'Mop', 'Cloth'];
                    return $this->randomElement($choice);
                }

            };

            $faker->addProvider($newClass);
            return $faker;
        });

        // Add redirect for factories that use Faker\Generator for dependency injection
        $this->app->singleton('Faker\Generator', function ($app) {
            return app('Faker');
        });
    }
}
