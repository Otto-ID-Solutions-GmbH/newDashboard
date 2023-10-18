<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 17.09.2017
 * Time: 13:44
 */

namespace Cintas\Facades;


use Illuminate\Support\Facades\Facade;

class Statistics extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'statistics';
    }

}