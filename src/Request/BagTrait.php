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

trait BagTrait
{
    /**
     * @var $attributes 附加属性信息
     */
    private $attributes = [];

    /**
     * 获取Query信息
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key = '', $default = null)
    {
        if (!$key) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }

    /**
     * 获取Post信息
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function post(string $key = '', $default = null)
    {
        if (!$key) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
    }

    /**
     * 获取Query&Post信息
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function input(string $key = '', $default = null)
    {
        if (!$key) {
            return $_REQUEST;
        }
        return $_REQUEST[$key] ?? $default;
    }

    /**
     * 获取Server信息
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function server(string $key = '', $default = null)
    {
        if (!$key) {
            return $_SERVER;
        }
        $upperKey = strtoupper($key);
        return $_SERVER[$upperKey] ?? $default;
    }

    /**
     * 获取cookie
     * 
     * @param string $key 设置的key中若包含句点(.)或空格( ),则会被转换为下划线(_)
     * @param mixed $default 默认值
     * @return mixed
     */
    public function cookie(string $key = '', $default = null)
    {
        if (!$key) {
            return $_COOKIE;
        }
        $cookieKey = str_replace(['.', ' '], '_', $key);
        return $_COOKIE[$cookieKey] ?? $default;
    }

    /**
     * 获取HEADER信息
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function header(string $key = '', $default = null)
    {
        static $headers = [];
        if (!$headers) {
            // 不带HTTP_前缀的header key
            $headerKeys = [
                'CONTENT_TYPE',
                'CONTENT_LENGTH',
                'CONTENT_MD5',
            ];
            foreach ($_SERVER as $serverKey => $value) {
                if (0 === strpos($serverKey, 'HTTP_')) {
                    $headers[substr($serverKey, 5)] = $value;
                } elseif (in_array($serverKey, $headerKeys, true)) {
                    $headers[$serverKey] = $value;
                }
            }
        }
        if (!$key) {
            return $headers;
        }
        $headerKey = strtoupper(str_replace('-', '_', $key));
        return $headers[$headerKey] ?? $default;
    }

    /**
     * 获取上传文件信息
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function file(string $key = '', $default = null)
    {
        if (!$key) {
            return $_FILES;
        }
        return $_FILES[$key] ?? $default;
    }

    /**
     * 获取附加属性信息
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function attribute(string $key = '', $default = null)
    {
        if (!$key) {
            return $this->attributes;
        }
        return $this->attributes[$key] ?? $default;
    }

    /**
     * 添加附加属性，如果key已存在，则跳过不覆盖，否则添加
     *
     * @param string $key
     * @param mixed $default
     * @return static
     */
    public function addAttribute(string $key, $value)
    {
        if (isset($this->attributes[$key])) {
            return $this;
        }
        $this->attributes[$key] = $value;
        return $this;
    }

    /**
     * 设置单个附加属性，如果key已存在，则覆盖
     *
     * @param string $key
     * @param mixed $default
     * @return static
     */
    public function setAttribute(string $key, $value)
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    /**
     * 设置整个附加属性
     *
     * @param array $attributes
     * @return static
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }
}
