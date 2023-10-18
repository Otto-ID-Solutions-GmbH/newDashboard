<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 17.10.2018
 * Time: 15:55
 */

namespace Cintas\Http\MessageTypes;


use EndyJasmi\Cuid;

class StocktakingDataInput
{

    /**
     * @var string|null
     */
    public $cuid;

    /**
     * @param string|null $cuid
     */
    public function setCuid($cuid): void
    {
        if (!$cuid) {
            $this->cuid = Cuid::make();
        } else {
            $this->cuid = $cuid;
        }
    }

    /**
     * @var string|null
     */
    public $created_at = null;

    /**
     * @var string|null
     */
    public $updated_at = null;

    /**
     * @var string|null
     */
    public $responsible_person_name = null;

    /**
     * @var string|null
     */
    public $notes = null;

    /**
     * @var string
     */
    public $location_id;

    /**
     * @var string
     */
    public $location_type;

    /**
     * @var StocktakingEntriesInput[]
     */
    public $stocktaking_entries;

    public function __construct()
    {
        $this->cuid = Cuid::make();
    }

}