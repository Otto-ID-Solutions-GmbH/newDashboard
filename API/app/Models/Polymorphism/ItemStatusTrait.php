<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 09.10.2018
 * Time: 18:13
 */

namespace Cintas\Models\Polymorphism;


trait ItemStatusTrait
{

    protected static function boot()
    {

        static::created(function ($model) {
            $model->source->last_status()->save($model);
        });

        parent::boot();
    }

    public function source()
    {
        return $this->morphTo();
    }


}