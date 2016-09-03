<?php
/**
 * Created by PhpStorm.
 * User: pauleverton
 * Date: 2016-05-05
 * Time: 9:24 PM
 */
namespace App\Infrastructure\Util\metrics;

use App\Infrastructure\Logger;

class Metrics
{
    private static $timers = [];

    public static function start($metric)
    {
        Metrics::$timers[$metric] = microtime(true);
    }

    public static function end($metric)
    {
        if (isset(Metrics::$timers[$metric])) {
            $diff  = round((microtime(true) - Metrics::$timers[$metric])*1000);
            Logger::info($metric.'.'.$diff, ['metric_name' => $metric, 'metric_value' => $diff]);
        } else {
            Logger::info($metric . ' was never started.');
        }
    }
}
