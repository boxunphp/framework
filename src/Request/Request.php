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

use Fw\Instance\InstanceTrait;

/**
 * 请求类
 */
class Request
{
    use InstanceTrait;
    use BagTrait;
    use MethodTrait;

    /**
     * @var string 追踪串
     */
    protected $traceId;

    /**
     * @var string 服务器IP
     */
    protected $serverIp;

    /**
     * @var string 客户端IP
     */
    protected $clientIp;

    /**
     * 带queryString的URL path
     *
     * @return string
     */
    public function getRequestUri()
    {
        return $this->server('REQUEST_URI', '');
    }

    /**
     * 整个URL
     *
     * @return string
     */
    public function getUri()
    {
        $scheme = $this->getServerScheme();
        $hostAndPort = $this->getServerHost();
        $port = $this->getServerPort();
        if (('http' == $scheme && 80 != $port) || ('https' == $scheme && 443 != $port)) {
            $hostAndPort .= ':' . $port;
        }
        return $scheme . '://' . $hostAndPort . $this->getRequestUri();
    }

    /**
     * 不带queryString的path
     *
     * @return string
     */
    public function getPathInfo()
    {
        $requestUri = $this->getRequestUri();
        if (false !== $pos = strpos($requestUri, '?')) {
            $requestUri = substr($requestUri, 0, $pos);
        }
        if ('' !== $requestUri && '/' !== $requestUri[0]) {
            $requestUri = '/' . $requestUri;
        }
        return $requestUri;
    }

    /**
     * 追踪ID
     * 用于跨项目或本项目同次请求日志跟踪
     *
     * @return string
     */
    public function getTraceId()
    {
        // 先取header和input的传入的,正常是用于外部项目传入
        do {
            if ($this->traceId) {
                break;
            }
            $this->traceId = $this->header('Trace-ID');
            if ($this->traceId) {
                break;
            }
            $this->traceId = $this->input('_trace_id');
            if ($this->traceId) {
                break;
            }
            $this->traceId = md5(uniqid(gethostname(), true));
        } while (0);

        return $this->traceId;
    }

    /**
     * RAW请求内容
     *
     * @return string
     */
    public function getBody()
    {
        return file_get_contents('php://input');
    }

    /**
     * 是否使用安全协议
     * 
     * @return boolean
     */
    public function isSecure()
    {
        $https = $_SERVER['HTTPS'] ?? null;
        return !empty($https) && 'off' !== strtolower($https);
    }

    /**
     * 服务器协议名
     *
     * @return string
     */
    public function getServerScheme()
    {
        return $this->isSecure() ? 'https' : 'http';
    }

    /**
     * 服务器主机
     *
     * @return string
     */
    public function getServerHost()
    {
        if (!$host = $this->header('HOST')) {
            if (!$host = $this->server('SERVER_NAME')) {
                $host = $this->server('SERVER_ADDR', '');
            }
        }
        // 移除端口
        $host = strtolower(preg_replace('/:\d+$/', '', trim($host)));
        return $host;
    }

    /**
     * 服务器名
     *
     * @return string
     */
    public function getServerName()
    {
        return $this->server('SERVER_NAME');
    }

    /**
     * 服务器端口
     *
     * @return integer
     */
    public function getServerPort()
    {
        return $this->server('SERVER_PORT');
    }

    /**
     * 服务器IP
     *
     * @return string
     */
    public function getServerIp()
    {
        if (null !== $this->serverIp) {
            return $this->serverIp;
        }

        $this->serverIp = $this->server('SERVER_ADDR');
        if (!$this->serverIp) {
            $this->serverIp = gethostbyname(gethostname());
        }

        return $this->serverIp;
    }

    /**
     * 客户端IP
     *
     * @return string
     */
    public function getClientIp()
    {
        if ($this->clientIp) {
            return $this->clientIp;
        }

        $headerKeys = [
            'Cdn-Src-Ip',
            'X-Original-Forwarded-For',
            'X-Forwarded-For',
            'X-Real-Ip',
            'X-Appengine-Remote-Addr',
            'X-Envoy-External-Address',
        ];
        foreach ($headerKeys as $headerKey) {
            $clientIp = $this->header($headerKey);
            if ($clientIp) {
                break;
            }
        }
        if (!$clientIp) {
            return $this->clientIp = 'unknown';
        }
        return $this->clientIp = trim(explode(',', $clientIp)[0]);
    }

    /**
     * 客户端端口
     *
     * @return string
     */
    public function getClientPort()
    {
        return $this->server('REMOTE_PORT');
    }

    /**
     * 客户端UA
     *
     * @return string
     */
    public function userAgent()
    {
        return $this->header('USER_AGENT');
    }

    /**
     * 来源地址
     *
     * @return string
     */
    public function referer()
    {
        return $this->header('REFERER');
    }

    /**
     * 是否是Ajax请求
     *
     * @return boolean
     */
    public function isXmlHttpRequest()
    {
        return 'XMLHttpRequest' == $this->header('X-Requested-With');
    }
}
