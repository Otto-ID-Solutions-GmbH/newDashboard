<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 09.10.2018
 * Time: 17:53
 */

namespace Cintas\Models\Polymorphism;


trait LocatableTrait
{

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->append('location');
    }

    public function getLocationAttribute($value = null)
    {
        return $value;
    }

    public function setLocationAttribute($value)
    {
        $this->location()->save($value);
    }

    public function location()
    {
        return $this->morphTo();
    }

}