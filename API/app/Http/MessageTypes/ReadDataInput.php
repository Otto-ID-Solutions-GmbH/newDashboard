<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 17.10.2018
 * Time: 15:55
 */

namespace Cintas\Http\MessageTypes;


class ReadDataInput
{

    /**
     * @var string
     */
    public $ip;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $optional;

    /**
     * @var string
     */
    public $lightbarrier;

    /**
     * @var TagDataInput[]
     */
    public $tags;

}