<?php

namespace Cintas\Models\Identifiables;

use Cintas\Models\AbstractModel;
use Cintas\Models\Polymorphism\IdentifierContract;
use Cintas\Models\Polymorphism\IdentifierTrait;

class Barcode extends AbstractModel implements IdentifierContract
{
    //
    use IdentifierTrait;

    public function getIdentifierAttribute(): string
    {
        return $this->code;
    }

    public function setIdentifierAttribute(string $identifier)
    {
        $this->code = $identifier;
    }

    public function getIdentifierTypeAttribute(): string
    {
        return $this->code_type;
    }

    public function setIdentifierTypeAttribute(string $identifierType)
    {
        $this->code_type = $identifierType;
    }

}
