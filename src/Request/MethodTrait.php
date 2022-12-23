<?php

/**
 * This file is part of the Boxunsoft package.
 *
 * (c) Jordy <arno.zheng@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Fw\Request;

trait MethodTrait
{
    /**
     * @return string
     */
    public function method()
    {
        return $this->server('REQUEST_METHOD', 'GET');
    }

    /**
     * @return boolean
     */
    public function isGet()
    {
        return 'GET' === $this->method();
    }

    /**
     * @return boolean
     */
    public function isPost()
    {
        return 'POST' === $this->method();
    }

    /**
     * @return boolean
     */
    public function isHead()
    {
        return 'HEAD' === $this->method();
    }

    /**
     * @return boolean
     */
    public function isPut()
    {
        return 'PUT' === $this->method();
    }

    /**
     * @return boolean
     */
    public function isDelete()
    {
        return 'DELETE' === $this->method();
    }

    /**
     * @return boolean
     */
    public function isPatch()
    {
        return 'PATCH' === $this->method();
    }

    /**
     * @return boolean
     */
    public function isOptions()
    {
        return 'OPTIONS' === $this->method();
    }

    /**
     * @return boolean
     */
    public function isTrace()
    {
        return 'TRACE' === $this->method();
    }

    /**
     * @return boolean
     */
    public function isPurge()
    {
        return 'PURGE' === $this->method();
    }

    /**
     * @return boolean
     */
    public function isConnect()
    {
        return 'CONNECT' === $this->method();
    }
}
