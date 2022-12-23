<?php

/**
 * This file is part of the Boxunsoft package.
 *
 * (c) Jordy <arno.zheng@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Fw\Memcached;

use Fw\Exception\Exception;

class MemcacheException extends Exception
{
    protected $method = '';
    protected $params = [];
    protected $config = [];

    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getConfig()
    {
        return $this->config;
    }
}
