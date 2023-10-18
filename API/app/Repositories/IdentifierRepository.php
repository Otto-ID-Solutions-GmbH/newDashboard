<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 14.10.2018
 * Time: 20:24
 */

namespace Cintas\Repositories;


use Cintas\Models\Identifiables\RFIDTag;
use Illuminate\Support\Collection;

interface IdentifierRepository
{

    /**
     * @return Collection<RFIDTag>
     */
    public function getIdentifiers();

    public function getIdentifiables($since = null);

    public function getIdentifiable($identifiableType, $identifiableId);

}