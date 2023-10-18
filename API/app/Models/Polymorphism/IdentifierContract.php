<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 09.10.2018
 * Time: 16:21
 */

namespace Cintas\Models\Polymorphism;


interface IdentifierContract
{

    public function getIdentifierAttribute();

    public function setIdentifierAttribute(string $identifier);

    public function getIdentifierTypeAttribute();

    public function setIdentifierTypeAttribute(string $identifierType);

    public function identifiable();

    public function not_identified_in_scans();

}