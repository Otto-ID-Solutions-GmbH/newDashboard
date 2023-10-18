<?php

namespace Cintas\Models\Facility;

use Cintas\Models\AbstractModel;
use Cintas\Models\Items\Product;
use Cintas\Models\Polymorphism\LocationContract;
use Cintas\Models\Polymorphism\LocationTrait;

class LaundryCustomer extends AbstractModel implements LocationContract
{

    use LocationTrait;

    public function getLabelAttribute($value)
    {
        return $this->display_label ?? $this->name ?? parent::getLabelAttribute($value);
    }

    public function products()
    {
        return $this->hasManyThrough(Product::class, TargetContainer::class);
    }

    public function served_by_facilities()
    {
        return $this->belongsToMany(Facility::class);
    }

    public function target_container()
    {
        return $this->hasOne(TargetContainer::class);
    }

}
