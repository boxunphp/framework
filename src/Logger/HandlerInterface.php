<?php

/**
 * This file is part of the Boxunsoft package.
 *
 * (c) Jordy <arno.zheng@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Fw\Logger;

/**
 * 日志输出口
 */
interface HandlerInterface
{
    public function write(array $message): void;
}
