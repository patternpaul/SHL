<?php
namespace App\Infrastructure\Database;

//use Redis;
class RedisDB implements IRedisDB
{
    private $redisDb;

    public function __construct($redisDb)
    {
        $this->redisDb = $redisDb;
    }

    public function hmset($key, $hash)
    {
        return $this->getDb()->hmset($key, $hash);
    }

    private function getDb()
    {
        if (!$this->redisDb) {
            $this->redisDb = app('redis');
        }

        return $this->redisDb;
    }

    public function hset($hash, $key, $val)
    {
        return $this->getDb()->hset($hash, $key, $val);
    }

    public function pipeline($function)
    {
        return $this->getDb()->pipeline($function);
    }

    public function hgetall($key)
    {
        return $this->getDb()->hgetall($key);
    }

    public function hget($hash, $key)
    {
        return $this->getDb()->hget($hash, $key);
    }

    public function hkeys($hash)
    {
        return $this->getDb()->hkeys($hash);
    }

    public function hvals($hash)
    {
        return $this->getDb()->hvals($hash);
    }

    public function hdel($key, $field)
    {
        return $this->getDb()->hdel($key, $field);
    }

    public function del($key)
    {
        return $this->getDb()->del($key);
    }

    public function zadd($hash, $key, $val)
    {
        $this->getDb()->zadd($hash, $key, $val);
    }

    public function zrem($hash, $key)
    {
        $this->getDb()->zrem($hash, $key);
    }

    public function zremrangebylex($hash, $min, $max)
    {
        return $this->getDb()->zremrangebylex($hash, $min, $max);
    }

    public function zrangebyscore($hash, $start, $end)
    {
        return $this->getDb()->zrangebyscore($hash, $start, $end);
    }

    public function zrange($hash, $start, $end)
    {
        return $this->getDb()->zrange($hash, $start, $end);
    }

    public function zscore($hash, $hashkey)
    {
        return $this->getDb()->zscore($hash, $hashkey);
    }
}
