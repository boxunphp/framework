<?php

namespace Fw\Session;

use Fw\Instance\InstanceTrait;

class Session
{
    use InstanceTrait;

    /**
     * 启动Session
     *
     * https://www.php.net/manual/zh/session.configuration.php
     * @param array $config
        [
            'save_handler' => 'redis',
            'save_path' => 'tcp://127.0.0.1:6379?auth=&weight=100&timeout=1&persistent=1&database=0', // database:选择那个Redis数据库;timeout:连接超时时间;weight:权重;persistent:长连接;auth:Redis密码
            'name' => 'TESTSESS', // 会话名,用作 cookie 的名字
            'gc_maxlifetime' => 1440,
            'cookie_lifetime' => 86400,
            'cookie_path' => '/',
            'cookie_domain' => '',
            'cookie_secure' => true, // 仅允许在 HTTPS 协议下访问会话 ID cookie
            'cookie_httponly' => true, // 禁止 JavaScript 访问会话 cookie
        ]
     * @return self
     */
    public function start($config = [])
    {
        $saveHandler = $config['save_handler'] ?? 'files';
        $savePath = $config['save_path'] ?? '/tmp';
        $name = $config['name'] ?? '';
        $gcMaxLifetime = $config['gc_maxlifetime'] ?? 0;

        $cookieParams = session_get_cookie_params();
        $cookieLifetime = $config['cookie_lifetime'] ?? ($cookieParams['lifetime'] ?? 0);
        $cookiePath = $config['cookie_path'] ?? ($cookieParams['path'] ?? '/');
        $cookieDomain = $config['cookie_domain'] ?? ($cookieParams['domain'] ?? null);
        $cookieSecure = $config['cookie_secure'] ?? ($cookieParams['secure'] ?? false);
        $cookieHttpOnly = $config['cookie_httponly'] ?? ($cookieParams['httponly'] ?? false);

        if ($saveHandler) {
            ini_set('session.save_handler', $saveHandler);
        }
        if ($savePath) {
            if (strtolower($saveHandler) == 'redis') {
                //设置默认超时时间为1s
                if (($pos = strpos($savePath, '?')) === false) {
                    $savePath .= '?timeout=1';
                } else {
                    $prefix = substr($savePath, 0, $pos);
                    $query = substr($savePath, $pos + 1);
                    if ($query) {
                        parse_str($query, $queryParams);
                        if (empty($queryParams['timeout'])) {
                            $queryParams['timeout'] = 1;
                            $savePath = $prefix . '?' . http_build_query($queryParams);
                        }
                    }
                }
            }
            session_save_path($savePath);
        }
        if ($name) {
            session_name($name);
        }
        if ($gcMaxLifetime) {
            ini_set('session.gc_maxlifetime', $gcMaxLifetime);
        }
        if (!isset($config['lazy_write'])) {
            if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
                //PHP7新增了一个session.lazy_write，默认值为1，当程序没有配置时，先关闭它。
                ini_set('session.lazy_write', 0);
            }
        }

        session_set_cookie_params($cookieLifetime, $cookiePath, $cookieDomain, $cookieSecure, $cookieHttpOnly);
        if (!isset($_SESSION)) {
            session_start();
        }

        return $this;
    }

    public function close()
    {
        session_write_close();
    }

    public function destroy()
    {
        session_destroy();
    }

    public function get($key)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public function getAll()
    {
        return $_SESSION;
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function delete($key)
    {
        unset($_SESSION[$key]);
    }

    public function getId()
    {
        return session_id();
    }

    public function setId($sessionId)
    {
        session_id($sessionId);
    }
}
