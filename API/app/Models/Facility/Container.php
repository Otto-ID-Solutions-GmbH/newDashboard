<?php

namespace Cintas\Models\Facility;

use Cintas\Models\AbstractModel;
use Cintas\Models\Polymorphism\LocatableContract;
use Cintas\Models\Polymorphism\LocatableTrait;

class Container extends AbstractModel implements LocatableContract
{

    use LocatableTrait;

    public function bundles()
    {
        return $this->hasMany(Bundle::class);
    }

    public function items()
    {
        //TODO: Implement hasManyThroughMany
        /*
         * URL:
         * https://medium.com/@DarkGhostHunter/laravel-has-many-through-pivot-elegantly-958dd096db
         */

        throw new \BadMethodCallException('Not implemented');
    }

    public function location()
    {
        //TODO: Test implementation of Container location
        return $this->bundles()->first()->location();
    }
}
