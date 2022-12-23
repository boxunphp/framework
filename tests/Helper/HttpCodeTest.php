<?php

/**
 * This file is part of the Boxunsoft package.
 *
 * (c) Jordy <arno.zheng@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Tests\Helper;

use Fw\Helper\HttpCode;
use PHPUnit\Framework\TestCase;

class HttpCodeTest extends TestCase
{
    public function testMessage()
    {
        $this->assertEquals('No Content', HttpCode::message(HttpCode::NO_CONTENT));
    }
}
