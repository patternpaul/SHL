<?php
/**
 * Created by PhpStorm.
 * User: pauleverton
 * Date: 15-07-22
 * Time: 9:18 PM
 */

namespace App\Infrastructure\Database;

class InMemoryRedisDB implements IRedisDB
{
    //TODO: Maybe return empty results when does not exist? Check Redis example
    public $hash;
    public $zsets;
    public $pipeline;

    public function __construct()
    {
        $this->hash = [];
        $this->pipeline = [];
        $this->zsets = [];
    }

    public function test_reset()
    {
        $this->hash = [];
        $this->pipeline = [];
        $this->zsets = [];
    }

    public function hmset($key, $hash)
    {
        if (!array_key_exists($key, $this->hash)) {
            $this->hash[$key] = [];
        }

        foreach ($hash as $item => $itemValue) {
            $this->hash[$key][$item] = $itemValue;
        }
    }

    public function hset($hash, $key, $val)
    {
        $this->hash[$hash][$key] = $val;
    }

    public function hkeys($hash)
    {
        return array_keys($this->getFromHash($hash, $this->hash));
    }

    public function hvals($hash)
    {
        return array_values($this->getFromHash($hash, $this->hash));
    }

    public function pipeline($function)
    {
        $this->pipeline = [];
        $function($this);
        return $this->pipeline;
    }

    public function hgetall($key)
    {
        $hash = $this->getFromHash($key, $this->hash);
        $this->pipeline[] = $hash;
        return $hash;
    }

    private function getFromHash($key, $hash)
    {
        if (array_key_exists($key, $hash)) {
            return $hash[$key];
        } else {
            return [];
        }
    }

    public function hget($hash, $key)
    {
        $value = $this->getFromHash($key, $this->getFromHash($hash, $this->hash));
        $this->pipeline[] = $value;
        return $value;
    }

    public function hdel($key, $field)
    {
        unset($this->hash[$key][$field]);
    }

    public function del($key)
    {
        unset($this->hash[$key]);
    }

    public function zadd($hash, $key, $val)
    {
        $this->zsets[$hash][$key] = $val;
    }

    public function zremrangebylex($hash, $min, $max)
    {
        $delete_keys = [];
        foreach ($this->zsets[$hash] as $key => $val) {
            //TODO Get it working with [ inclusion and ( exclusion
            if (($val >= substr($min,1,-1)) &&($val <= substr($max,1,-1))) {
                $delete_keys[] = $key;
            }
        }

        foreach ($delete_keys as $delete_key) {
            unset($this->zsets[$hash][$delete_key]);
        }
    }

    public function zrem($hash, $key)
    {
        unset($this->zsets[$key]);
    }

    public function zrangebyscore($hash, $start, $end)
    {
        $vals = $this->zsets[$hash];
        $returnVals = [];
        foreach ($vals as $key => $val) {
            if ($key >= $start && $key <= $end) {
                $returnVals[] = $val;
            }
        }

        return $returnVals;
    }

    public function zscore($hash, $hashkey)
    {
        $vals = $this->zsets[$hash];
        foreach ($vals as $key => $val) {
            if ($hashkey == $val) {
                return $key;
            }
        }
    }

    public function zrange($hash, $start, $end)
    {
        $vals = $this->zsets[$hash];
        $returnVals = [];
        foreach ($vals as $key => $val) {
            if ($key >= $start && $key <= $end) {
                $returnVals[] = $val;
            }
        }

        return $returnVals;
    }
}
