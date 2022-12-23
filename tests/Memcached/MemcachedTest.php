<?php

/**
 * This file is part of the Boxunsoft package.
 *
 * (c) Jordy <arno.zheng@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Tests\Memcached;

use Fw\Memcached\Memcached;
use PHPUnit\Framework\TestCase;

/**
 * @group Memcached
 */
class MemcachedTest extends TestCase
{
    /**
     * @var Memcached
     */
    private $mc;

    public function setUp(): void
    {
        $config = env('mc/default');
        $this->mc = Memcached::getInstance($config);
    }

    public function testAll()
    {
        $k1 = 'a';
        $k2 = 'b';
        $k3 = 'c';
        $v1 = 1;
        $v2 = 2;
        $v3 = 3;

        $this->mc->deleteMulti([$k1, $k2, $k3, 'aa']);

        $this->assertTrue($this->mc->set($k1, $v1));
        $this->assertEquals($this->mc->get($k1), $v1);
        $this->assertFalse($this->mc->add($k1, $v1));
        $this->assertTrue($this->mc->add($k2, $v2));
        $this->assertEquals($this->mc->get($k2), $v2);
        $this->assertEquals($this->mc->getMulti([$k1, $k2]), [$k1 => $v1, $k2 => $v2]);
        $this->assertTrue($this->mc->delete($k1));
        $this->assertTrue($this->mc->delete($k2));
        $this->assertFalse($this->mc->get($k1));
        $this->assertFalse($this->mc->get($k2));

        $this->assertTrue($this->mc->setMulti([$k1 => $v1, $k2 => $v2, $k3 => $v3]));
        $this->assertEquals($this->mc->getMulti([$k1, $k2, $k3]), [$k1 => $v1, $k2 => $v2, $k3 => $v3]);

        $this->assertEquals($this->mc->increment($k1, 5), 6);
        $this->assertEquals($this->mc->decrement($k1, 5), 1);
        $this->assertEquals($this->mc->decrement($k1, 2), 0);

        $this->assertTrue($this->mc->set('aa', 'abc'));
        $this->assertTrue($this->mc->replace('aa', 'efg'));
        $this->assertEquals($this->mc->get('aa'), 'efg');
    }
}
