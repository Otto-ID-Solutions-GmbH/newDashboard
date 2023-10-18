<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 09.10.2018
 * Time: 17:35
 */

namespace Cintas\Models\Polymorphism;


interface IdentifiableContract
{
    public function rfid_tags();

    public function barcodes();
}