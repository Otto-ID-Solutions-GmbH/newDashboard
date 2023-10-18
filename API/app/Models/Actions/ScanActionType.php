<?php

namespace Cintas\Models\Actions;

use Cintas\Models\AbstractModel;

class ScanActionType extends AbstractModel
{
    public function getLabelAttribute($value)
    {
        return $this->display_label ?? $this->name ?? parent::getLabelAttribute($value);
    }


    public static function findByName($name)
    {
        return ScanActionType::where('name', '=', $name)->first();
    }

    public function scan_actions()
    {
        return $this->hasMany(ScanAction::class);
    }
}
