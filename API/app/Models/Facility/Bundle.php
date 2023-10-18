<?php

namespace Cintas\Models\Facility;

use Cintas\Models\AbstractModel;
use Cintas\Models\Items\Item;
use Cintas\Models\Polymorphism\LocatableContract;
use Cintas\Models\Polymorphism\LocatableTrait;
use Cintas\Models\Polymorphism\LocationContract;

class Bundle extends AbstractModel implements LocatableContract
{

    use LocatableTrait;

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function container()
    {
        return $this->belongsToMany(Container::class);
    }

    public function location()
    {
        //TODO: Test implementation of Bundle location
        return $this->items()->first()->location();
    }

    /**
     * @param LocationContract $location
     */
    public function setLocationForItems($location)
    {
        foreach ($this->items as $item) {
            $item->location()->save($location);
        }
    }
}
