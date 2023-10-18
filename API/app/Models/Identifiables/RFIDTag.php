<?php

namespace Cintas\Models\Identifiables;

use Cintas\Models\AbstractModel;
use Cintas\Models\Polymorphism\IdentifierContract;
use Cintas\Models\Polymorphism\IdentifierTrait;

class RFIDTag extends AbstractModel implements IdentifierContract
{
    //
    use IdentifierTrait;

    public function getIdentifierAttribute()
    {
        return $this->epc;
    }

    public function setIdentifierAttribute(string $identifier)
    {
        $this->epc = $identifier;
    }

    public function getIdentifierTypeAttribute()
    {
        return $this->epc_type;
    }

    public function setIdentifierTypeAttribute(string $identifierType)
    {
        $this->epc_type = $identifierType;
    }

    public static function findByEpc($epc)
    {
        return RFIDTag::query()->where('epc', '=', $epc)->first();
    }

    public static function findByEpcs($epcs)
    {
        return RFIDTag::query()
            ->whereIn('epc', $epcs)->get();
    }

    public function identifiable()
    {
        return $this->morphTo('identifiable');
    }
}
