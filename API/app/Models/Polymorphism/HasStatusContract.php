<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 12.10.2018
 * Time: 17:32
 */

namespace Cintas\Models\Polymorphism;


interface HasStatusContract
{

    public function last_status();

    public function status_history();

}