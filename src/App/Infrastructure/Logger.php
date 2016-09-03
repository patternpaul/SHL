<?php
/**
 * Created by PhpStorm.
 * User: pauleverton
 * Date: 15-07-19
 * Time: 7:29 PM
 */

namespace App\Infrastructure;

use Illuminate\Support\Facades\Log;

class Logger
{
    //TODO: Better fix for these statics
    public static $should_log = true;

    public static function info($logdata, $add = [])
    {
        if (Logger::$should_log) {
            Log::info($logdata, $add);
        }
    }

    public static function error($logdata, $add = [])
    {
        if (Logger::$should_log) {
            Log::error($logdata, $add);
        }
    }
}
