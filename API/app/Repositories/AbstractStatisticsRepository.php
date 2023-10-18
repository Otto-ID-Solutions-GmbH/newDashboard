<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 07.12.2018
 * Time: 14:17
 */

namespace Cintas\Repositories;


abstract class AbstractStatisticsRepository
{
    public function categorizeIntervalScale($max, $min = 0, $chunkSize = null, $noGroups = null)
    {
        $groups = collect();

        if (!$chunkSize && $noGroups !== null) {
            $chunkSize = ceil(($max - $min) / $noGroups);
        }

        $groups->push([$min, $min + $chunkSize]);

        for ($i = $min + $chunkSize + 1; $i < $max; $i = $i + $chunkSize) {
            $groups->push([$i, $i + $chunkSize - 1]);
        }

        //TODO: How to handle last group max as infinity?

        return $groups;
    }
}