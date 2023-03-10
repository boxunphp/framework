<?php

/**
 * This file is part of the Boxunsoft package.
 *
 * (c) Jordy <arno.zheng@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Fw\Cache;

interface DriverInterface
{
    public function set($key, $value, $expiration = 0);

    public function get($key);

    public function delete($key);

    public function setMulti(array $items, $expiration = 0);

    public function getMulti(array $keys);

    public function deleteMulti(array $keys);
}
