<?php

/**
 * This file is part of the Boxunsoft package.
 *
 * (c) Jordy <arno.zheng@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Fw\Redis;

use Fw\Instance\InstanceTrait;

/**
 * https://github.com/phpredis/phpredis/tree/2.2.8
 *
 * @method string get($key)
 * @method bool set($key, $value, $timeout = 0)
 * @method bool setex($key, $ttl, $value)
 * @method bool psetex($key, $ttl, $value)
 * @method bool setnx($key, $value)
 * @method int del(array | string $key1, $key2 = null, $keyN = null)
 * @method int exists($key)
 * @method int incr($key)
 * @method int incrBy($key, $value)
 * @method float incrByFloat($key, $value)
 * @method int decr($key)
 * @method int decrBy($key, $value)
 * @method array mget(array $keys)
 * @method bool mset(array $items)
 * @method bool expire($key, $ttl)
 * @method bool pexpire($key, $ttl)
 * @method bool expireAt($key, $timestamp)
 * @method bool pexpireAt($key, $timestamp)
 * @method int ttl($key)
 * @method int pttl($key)
 * @method array|boolean scan(&$iterator, $pattern = null, $count = 0)
 * @method int hSet($key, $hashKey, $value)
 * @method string hGet($key, $hashKey)
 * @method int hLen($key)
 * @method int hDel($key, $hashKey1, $hashKey2 = null, $hashKeyN = null)
 * @method array hKeys($key)
 * @method array hVals($key)
 * @method bool hSetNx($key, $hashKey, $value)
 * @method array hGetAll($key)
 * @method int hIncrBy($key, $hashKey, $value)
 * @method float hIncrByFloat($key, $hashKey, $value)
 * @method bool hExists($key, $hashKey)
 * @method bool hMSet($key, $hashItems)
 * @method array hMGet($key, $hashKeys)
 * @method array hScan($key, &$iterator, $pattern = null, $count = 0)
 * @method string lIndex($key, $index)
 * @method string lPop($key)
 * @method int lPush($key, $value1, $value2 = null, $valueN = null)
 * @method array lRange($key, $start, $end)
 * @method array lGetRange($key, $start, $end)
 * @method array lTrim($key, $start, $stop)
 * @method int lRem($key, $value, $count)
 * @method string rPop($key)
 * @method int rPush($key, $value1, $value2 = null, $valueN = null)
 * @method int lLen($key)
 * @method int lSize($key)
 * @method array blPop(array | string $keys, $timeout)
 * @method array brPop(array | string $keys, $timeout)
 * @method string rPoplPush($srcKey, $dstKey)
 * @method string brPoplPush($srcKey, $dstKey, $timeout)
 * @method int sAdd($key, $value1, $value2 = null, $valueN = null)
 * @method int sCard($key)
 * @method array sDiff(string $key1, $key2 = null, $keyN = null)
 * @method array sInter(string $key1, $key2 = null, $keyN = null)
 * @method bool sIsMember($key, $value)
 * @method bool sContains($key, $value)
 * @method array sMembers($key)
 * @method array sGetMembers($key)
 * @method bool sMove($srcKey, $dstKey, $member)
 * @method int sRem($key, $member1, $member2 = null, $memberN = null)
 * @method string sPop($key)
 * @method string sRandMember($key)
 * @method array sUnion(string $key1, $key2 = null, $keyN = null)
 * @method array|boolean sScan($key, &$iterator, $pattern = null, $count = 0)
 * @method int zAdd($key, $score1, $value1, $score2 = null, $value2 = null, $scoreN = null, $valueN = null)
 * @method int zCard($key)
 * @method int zCount($key, $start, $end)
 * @method float zIncrBy($key, $value, $member)
 * @method array zRange($key, $start, $end, $withScores = false)
 * @method array zRevRange($key, $start, $end, $withScores = false)
 * @method array zRangeByScore($key, $start, $end, $options = [])
 * @method array zRevRangeByScore($key, $max, $min, $options = [])
 * @method int zRank($key, $member)
 * @method int zRevRank($key, $member)
 * @method int zRem($key, $member1, $member2 = null, $memberN = null)
 * @method int zRemRangeByRank($key, $start, $end)
 * @method int zRemRangeByScore($key, $start, $end)
 * @method float zScore($key, $member)
 * @method array|boolean zScan($key, &$iterator, $pattern = null, $count = 0)
 * @method Redis multi($type = \Redis::MULTI)
 * @method mixed exec()
 * @method string getLastError()
 */
abstract class RedisAbstract
{
    use InstanceTrait;

    /**
     * @var Redis
     */
    protected $redis;
    /**
     * 配置文件的key
     * @var string
     */
    protected $configKey = '';
    /**
     * 配置信息,如果有定义了path和key,会被覆盖
     * @var array|null
     */
    protected $config = [];
    /**
     * 缓存前缀
     * @var string
     */
    protected $prefixKey = '';
    /**
     * 过期时间
     *
     * @var integer
     */
    protected $ttl = 0;

    /**
     * RedisAbstract constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        if ($this->configKey) {
            // 必须在全局定义环境函数, 用于获取配置
            if (!function_exists('env')) {
                throw new \Exception('function env cannot be defined!', E_ERROR);
            }

            $this->config = env($this->configKey);
        }

        $this->redis = Redis::getInstance($this->config);
    }

    public function __call($method, $params)
    {
        return call_user_func_array([$this->redis, $method], $params);
    }
}
