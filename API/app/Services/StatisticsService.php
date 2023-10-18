<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 17.09.2017
 * Time: 13:52
 */

namespace Cintas\Services;


use Carbon\Carbon;
use Cintas\Facades\Statistics;
use Illuminate\Database\Eloquent\Relations\Relation;

class StatisticsService
{

    protected static $morphMap = [];
    protected static $morphMapInverse = [];

    public static function morphMap(array $morphMap = null)
    {
        self::$morphMap = $morphMap;
        self::$morphMapInverse = array_flip($morphMap);

        Relation::morphMap($morphMap);
    }

    public static function getMorphAliasFromModel($model)
    {

        if (is_object($model)) {
            $className = get_class($model);
        } else {
            $className = $model;
        }

        return array_key_exists($className, self::$morphMapInverse)
            ? self::$morphMapInverse[$className]
            : null;
    }

    public static function getMorphedModel($alias)
    {
        return Relation::getMorphedModel($alias);
    }

    public static function findPolymorphModel($type, $id)
    {
        $class = Statistics::getMorphedModel(ucfirst($type));
        return $class::find($id);
    }

    public static function findOrFailPolymorphModel($type, $id)
    {
        $class = Statistics::getMorphedModel(ucfirst($type));
        return $class::findOrFail($id);
    }

    public static function getUserTimezone(): string
    {
        return config('cintas.time.timezone', 'UTC');
    }

    public static function getPeriodFormatting($period, $start = null, $end = null, $timezone = null)
    {

        if (!$timezone) {
            $timezone = self::getUserTimezone();
        }

        switch (strtolower(urldecode($period))) {
            case 'today':
                $format = 'h:m';
                $start = Carbon::now($timezone)->startOfDay();
                $end = Carbon::now($timezone)->endOfDay();
                $interval = "1 hour";
                break;
            case 'this week':
                $format = 'm/d';
                $start = Carbon::now($timezone)->modify('this week');
                $end = (clone $start)->modify('this week +6 days');
                $interval = "1 day";
                break;
            case 'this month':
                $format = 'W (o)';
                $start = Carbon::now($timezone)->firstOfMonth();
                $end = Carbon::now($timezone)->lastOfMonth();
                $interval = "1 week";
                break;
            case 'last month':
                $format = 'W (o)';
                $start = Carbon::now($timezone)->subMonth(1)->firstOfMonth();
                $end = Carbon::now($timezone)->subMonth(1)->lastOfMonth();
                $interval = "1 week";
                break;
            case 'this year':
                $format = 'Y/m';
                $start = Carbon::now($timezone)->firstOfYear();
                $end = Carbon::now($timezone)->subMonth(1)->lastOfYear();
                $interval = "1 month";
                break;
            case 'last year':
                $format = 'Y/m';
                $start = Carbon::now($timezone)->subYear(1)->firstOfYear();
                $end = Carbon::now($timezone)->subYear(1)->lastOfYear();
                $interval = "1 month";
                break;
            case 'last 5 years':
                $format = 'Y';
                $start = Carbon::now($timezone)->subYear(5)->firstOfYear();
                $end = Carbon::now($timezone)->lastOfYear();
                $interval = "1 year";
                break;
            case 'since 1 year':
                $format = 'Y/m';
                $start = Carbon::now($timezone)->subYear(1);
                $end = Carbon::now($timezone);
                $interval = "1 month";
                break;
            case 'since 1 month':
                $format = 'Y/m/d';
                $start = Carbon::now($timezone)->subMonth(1);
                $end = Carbon::now($timezone);
                $interval = "1 day";
                break;
            case 'since 3 months':
                $format = 'Y/m/d';
                $start = Carbon::now($timezone)->subMonth(3);
                $end = Carbon::now($timezone);
                $interval = "1 day";
                break;
            case 'since 1 week':
                $format = 'Y/m/d';
                $start = Carbon::now($timezone)->subWeek(1);
                $end = Carbon::now($timezone);
                $interval = "1 day";
                break;
            case 'since 2 weeks':
                $format = 'Y/m/d';
                $start = Carbon::now($timezone)->subWeek(2);
                $end = Carbon::now($timezone);
                $interval = "1 day";
                break;
            default:
                $format = 'Y/m/d';
                $start = $start ?? Carbon::now($timezone)->subMonth(1);
                $end = $end ?? Carbon::now($timezone);
                $interval = "1 day";
        }

        return [
            'string_format' => $format,
            'start_date' => $start->startOfDay(),
            'end_date' => $end->startOfDay(),
            'interval' => $interval,
            'timezone' => $timezone
        ];
    }

}
