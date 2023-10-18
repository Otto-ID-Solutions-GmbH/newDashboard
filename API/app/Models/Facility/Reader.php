<?php

namespace Cintas\Models\Facility;

use Cintas\Models\AbstractModel;
use Cintas\Models\Polymorphism\IdentifiableContract;
use Cintas\Models\Polymorphism\IdentifiableTrait;

class Reader extends AbstractModel implements IdentifiableContract
{
    use IdentifiableTrait;

    public function getLabelAttribute($value)
    {
        return $this->display_label ?? $this->id ?? parent::getLabelAttribute($value);
    }


    public function facility()
    {
        return $this->belongsTo(Facility::class, 'facility_id');
    }

    public static function findById($id)
    {
        return Reader::where('id', '=', $id)->first();
    }
}
