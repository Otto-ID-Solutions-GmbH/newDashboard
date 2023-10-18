<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 10.10.2018
 * Time: 20:17
 */

namespace Cintas\Models\Polymorphism;


trait LocationTrait
{

    public function at_location()
    {
        //TODO: Creat abstract location as union type
        //TODO: Resolve via status??!!
        return $this->morphedByMany(LocatableContract::class, 'location');
    }

}