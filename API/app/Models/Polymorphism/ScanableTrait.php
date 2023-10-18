<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 11.10.2018
 * Time: 17:16
 */

namespace Cintas\Models\Polymorphism;


use Cintas\Models\Actions\ScanAction;

trait ScanableTrait
{
    public function scans()
    {
        return $this->belongsToMany(ScanAction::class);
    }
}