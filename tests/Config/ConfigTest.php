<?php

/**
 * This file is part of the Boxunsoft package.
 *
 * (c) Jordy <arno.zheng@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Tests\Config;

use Fw\Config\Config;
use PHPUnit\Framework\TestCase;

/**
 * @group Config
 */
class ConfigTest extends TestCase
{
    /**
     * @var Config
     */
    private $config;

    protected function setUp(): void
    {
        $this->config = new Config();
        $this->config->setPath(__DIR__);
    }

    /**
     * @throws \Exception
     */
    public function testGet()
    {
        $this->assertEquals(['master' => ['host' => '127.0.0.1']], $this->config->get('config'), '127.0.0.1');
        $this->assertEquals('127.0.0.1', $this->config->get('config.master.host'));
    }
}
