<?php

/**
 * This file is part of the Boxunsoft package.
 *
 * (c) Jordy <arno.zheng@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Tests\Request;

use Fw\Request\Request;
use PHPUnit\Framework\TestCase;

/**
 * @group Request
 */
class RequestTest extends TestCase
{
    public function setUp(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
    }
    public function testA()
    {
        $this->assertTrue(Request::getInstance()->isPost());
    }
}
