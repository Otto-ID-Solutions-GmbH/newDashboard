<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 31.10.2018
 * Time: 18:21
 */

namespace Cintas\Repositories;


interface TargetStatisticsRepository
{

    public function getTargetContainerReachPerScan($period = null);

    public function getDailyTargetContainerReachPerProductType($date = null);

    public function getAggregatedContainerBundleRatio($period = null, $start = null, $end = null);

    public function getContainerBundleRatio($period = null, $start = null, $end = null);

}