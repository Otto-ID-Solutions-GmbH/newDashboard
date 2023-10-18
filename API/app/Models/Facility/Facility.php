<?php

namespace Cintas\Models\Facility;

use Cintas\Models\AbstractModel;
use Cintas\Models\Polymorphism\LocationContract;
use Cintas\Models\Polymorphism\LocationTrait;

class Facility extends AbstractModel implements LocationContract
{

    use LocationTrait;

    public function readers()
    {
        return $this->hasMany(Reader::class);
    }

    public function laundry_customers()
    {
        return $this->belongsToMany(LaundryCustomer::class);
    }

}
