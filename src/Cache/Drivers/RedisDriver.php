<?php

/**
 * This file is part of the Boxunsoft package.
 *
 * (c) Jordy <arno.zheng@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Fw\Cache\Drivers;

use Fw\Cache\DriverInterface;
use Fw\Instance\InstanceTrait;
use Fw\Redis\Redis;

class RedisDriver implements DriverInterface
{
    use InstanceTrait {
        getInstance as private _getInstance;
    }

    /**
     * @var Redis
     */
    protected $redis;

    private function __construct(array $config)
    {
        $this->redis = Redis::getInstance($config);
    }

    public static function getInstance(array $config)
    {
        return self::_getInstance($config);
    }

    public function set($key, $value, $expiration = 0)
    {
        if ($expiration > 0) {
            return $this->redis->set($key, $this->serialize($value), $expiration);
        } else {
            return $this->redis->set($key, $this->serialize($value));
        }
    }

    public function get($key)
    {
        $result = $this->redis->get($key);
        return $this->unserialize($result);
    }

    public function delete($key)
    {
        $result = $this->redis->del($key);
        return $result == 1;
    }

    public function setMulti(array $items, $expiration = 0)
    {
        foreach ($items as $key => $value) {
            $items[$key] = $this->serialize($value);
        }
        if ($expiration > 0) {
            $this->redis->multi(\Redis::PIPELINE);
            foreach ($items as $key => $value) {
                $this->set($key, $value, $expiration);
            }
            $result = $this->redis->exec();
            return array_sum($result) == count($items);
        } else {
            return $this->redis->mSet($items);
        }
    }

    public function getMulti(array $keys)
    {
        $result = $this->redis->mGet($keys);
        $data = [];
        if ($result && is_array($result)) {
            foreach ($keys as $index => $key) {
                if (isset($result[$index]) && $result[$index] !== false) {
                    $data[$key] = $this->unserialize($result[$index]);
                }
            }
        }

        return $data;
    }

    public function deleteMulti(array $keys)
    {
        $result = $this->redis->del($keys);
        return count($keys) == $result;
    }

    protected function serialize($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
    }

    protected function unserialize($value)
    {
        return json_decode($value, true);
    }
}
