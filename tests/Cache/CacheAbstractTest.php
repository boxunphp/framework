<?php

/**
 * This file is part of the Boxunsoft package.
 *
 * (c) Jordy <arno.zheng@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Tests\Cache;

use Fw\Cache\Cache;
use Fw\Cache\CacheAbstract;
use PHPUnit\Framework\TestCase;

/**
 * @group Cache
 */
class CacheAbstractTest extends TestCase
{
    public function testCacheConfig()
    {
        $cache = CacheConfig::getInstance();

        $k1 = 'a';
        $k2 = 'b';
        $v1 = 1;
        $v2 = 2;
        $this->assertTrue($cache->set($k1, $v1));
        $this->assertEquals($v1, $cache->get($k1));
        $this->assertTrue($cache->delete($k1));
        $data = [$k1 => $v1, $k2 => $v2];
        $this->assertTrue($cache->setMulti($data));
        $this->assertEquals($data, $cache->getMulti([$k1, $k2]));
        $this->assertTrue($cache->deleteMulti([$k1, $k2]));
    }

    public function testCacheFile()
    {
        $cache = CacheFile::getInstance();

        $k1 = 'a';
        $k2 = 'b';
        $v1 = 1;
        $v2 = 2;
        $this->assertTrue($cache->set($k1, $v1));
        $this->assertEquals($v1, $cache->get($k1));
        $this->assertTrue($cache->delete($k1));
        $data = [$k1 => $v1, $k2 => $v2];
        $this->assertTrue($cache->setMulti($data));
        $this->assertEquals($data, $cache->getMulti([$k1, $k2]));
        $this->assertTrue($cache->deleteMulti([$k1, $k2]));
    }
}

class CacheConfig extends CacheAbstract
{
    protected $cacheType = Cache::TYPE_MEMCACHED;
    protected $config = [];
    protected $prefixKey = 'testc';
    protected $ttl = 10;
    public function __construct()
    {
        $this->config = env('mc/default');
        parent::__construct();
    }
}

class CacheFile extends CacheAbstract
{
    protected $type = Cache::TYPE_MEMCACHED;
    protected $configKey = 'mc/default';
    protected $prefixKey = 'testb';
    protected $ttl = 20;
}
