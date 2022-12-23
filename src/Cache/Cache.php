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

class Cache
{
    const TYPE_MEMCACHED = 1;
    const TYPE_REDIS = 2;
    const TYPE_APCU = 3;
    const TYPE_FILE = 4;
}
