<?php

namespace Cintas\Traits;

use EndyJasmi\Cuid;

trait CuidForKeyTrait
{
    /**
     * Boot the Uuid trait for the model.
     *
     * @return void
     */
    public static function bootCuidForKey()
    {
        static::creating(function ($model) {
            $model->primaryKey = 'cuid';
            $model->incrementing = false;
            $model->cuid = (string)Cuid::cuid();
        });
    }

    /**
     * Get the casts array.
     *
     * @return array
     */
    public function getCasts()
    {
        return $this->casts;
    }
}
