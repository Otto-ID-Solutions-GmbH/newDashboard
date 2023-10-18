<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 17.10.2018
 * Time: 19:51
 */

namespace Cintas\Http\MessageTypes;


use EndyJasmi\Cuid;

class StocktakingEntriesInput
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
     * @var string
     */
    public $stock_id;

    /**
     * @var string
     */
    public $stock_label;

    /**
     * @var float
     */
    public $is_amount;

    /**
     * @var string[]
     */
    public $item_ids = [];

    /**
     * StocktakingEntriesInput constructor.
     */
    public function __construct()
    {
        $this->cuid = Cuid::make();
    }


}