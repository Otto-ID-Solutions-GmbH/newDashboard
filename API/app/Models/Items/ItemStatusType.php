<?php

namespace Cintas\Models\Items;

use Cintas\Models\AbstractModel;

class ItemStatusType extends AbstractModel
{

    public static function findByName($name)
    {
        return ItemStatusType::where('name', '=', $name)->first();
    }

    public function item_statuses()
    {
        return $this->hasMany(ItemStatus::class);
    }
}
