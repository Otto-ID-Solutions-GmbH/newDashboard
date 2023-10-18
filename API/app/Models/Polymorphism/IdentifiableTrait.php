<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 09.10.2018
 * Time: 17:41
 */

namespace Cintas\Models\Polymorphism;


use Cintas\Models\Identifiables\Barcode;
use Cintas\Models\Identifiables\RFIDTag;

trait IdentifiableTrait
{

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->guard(['identifiers']);
    }

    public function rfid_tags()
    {
        return $this->morphMany(RFIDTag::class, 'identifiable');
    }

    public function barcodes()
    {
        return $this->morphMany(Barcode::class, 'identifiable');
    }

}