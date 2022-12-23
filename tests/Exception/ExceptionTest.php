<?php

/**
 * This file is part of the Boxunsoft package.
 *
 * (c) Jordy <arno.zheng@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Tests\Exception;

use Fw\Exception\HttpException;
use PHPUnit\Framework\TestCase;

/**
 * @group Exception
 */
class ExceptionTest extends TestCase
{
    /**
     * @expectException \Fw\Exception\HttpException
     * @expectExceptionCode 400
     * @expectExceptionMessage Bad Request
     */
    public function testBadRequest()
    {
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('Bad Request');
        throw new HttpException(400);
    }
}
