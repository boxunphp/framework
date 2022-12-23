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

class ApcuDriver implements DriverInterface
{
    use InstanceTrait {
        getInstance as private _getInstance;
    }

    public static function getInstance(array $config)
    {
        return self::_getInstance($config);
    }

    public function set($key, $value, $expiration = 0)
    {
        return \apcu_store($key, $value, $expiration);
    }

    public function get($key)
    {
        return \apcu_fetch($key);
    }


    public function delete($key)
    {
        return \apcu_delete($key);
    }

    public function getMulti(array $keys)
    {
        $result = \apcu_fetch($keys);
        if (!$result) {
            $result = [];
        }
        return $result;
    }

    public function setMulti(array $items, $expiration = 0)
    {
        //保存成功返回空数组
        $result = \apcu_store($items, null, $expiration);
        return empty($result) ? true : false;
    }

    public function deleteMulti(array $keys)
    {
        $result = \apcu_delete($keys);
        return empty($result);
    }
}
