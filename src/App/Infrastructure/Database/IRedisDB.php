<?php
/**
 * Created by PhpStorm.
 * User: pauleverton
 * Date: 15-07-22
 * Time: 9:17 PM
 */

namespace App\Infrastructure\Database;

interface IRedisDB
{
    public function hmset($key, $hash);

    public function hset($hash, $key, $val);

    public function pipeline($function);

    public function hgetall($key);

    public function hget($hash, $key);

    public function hkeys($key);

    public function hvals($key);

    public function hdel($key, $field);

    public function del($key);

    public function zadd($hash, $key, $value);

    public function zremrangebylex($hash, $min, $max);

    public function zrem($hash, $key);

    public function zrangebyscore($hash, $start, $end);

    public function zrange($hash, $start, $end);

    public function zscore($hash, $key);
}
