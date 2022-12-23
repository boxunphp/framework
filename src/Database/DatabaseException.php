<?php

/**
 * This file is part of the Boxunsoft package.
 *
 * (c) Jordy <arno.zheng@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Fw\Database;

use Fw\Exception\Exception;

class DatabaseException extends Exception
{
    protected $prepareSql = '';
    protected $params = [];
    protected $host = '';
    protected $port = 0;

    public function setPrepareSql($prepareSql)
    {
        $this->prepareSql = $prepareSql;
        return $this;
    }

    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    public function setHost($host)
    {
        $this->host = $host;
    }

    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    public function getPrepareSql()
    {
        return $this->prepareSql;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPort()
    {
        return $this->port;
    }
}
