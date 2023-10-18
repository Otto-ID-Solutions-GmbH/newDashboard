<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 17.10.2018
 * Time: 19:51
 */

namespace Cintas\Http\MessageTypes;


class TagDataInput
{
    /**
     * @var string
     */
    public $epc;

    /**
     * @var string
     */
    public $timestamp;

    /**
     * @var int
     */
    public $antenna;
}