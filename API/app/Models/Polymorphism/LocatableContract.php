<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 09.10.2018
 * Time: 17:50
 */

namespace Cintas\Models\Polymorphism;


interface LocatableContract
{
    public function getLocationAttribute($value = null);

    public function setLocationAttribute($location);
}