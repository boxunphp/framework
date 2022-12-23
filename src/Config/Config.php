<?php

/**
 * This file is part of the Boxunsoft package.
 *
 * (c) Jordy <arno.zheng@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Fw\Config;

use Fw\Instance\InstanceTrait;

/**
 * 读取配置文件信息，配置文件为PHP，通过return返回配置信息，如：
 * <?php
 * return [
 *     'field' => 'value',
 * ];
 */
class Config
{
    use InstanceTrait;

    private $path;
    private $data;

    public function __construct()
    {
    }

    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getPath()
    {
        if (!$this->path) {
            throw new \Exception('Config path is not configured');
        }
        return $this->path;
    }

    /**
     * @param string $key example: db/default.master.host
     *              db/default 为配置文件的路径
     *              master.host 为配置文件里，配置信息的数组层级
     * @return array|null
     * @throws \Exception
     */
    public function get($key)
    {
        list($file, $keys) = $this->parseKey($key);
        $data = $this->getData($file);
        if (!$keys || !$data) {
            return $data;
        }
        foreach ($keys as $key) {
            if (!is_array($data) || !isset($data[$key])) {
                return null;
            }
            $data = $data[$key];
        }
        return $data;
    }

    /**
     * @param $file
     * @return array|null
     * @throws \Exception
     */
    private function getData($file)
    {
        if (isset($this->data[$file])) {
            return $this->data[$file];
        }
        $filePath = $this->getPath() . DIRECTORY_SEPARATOR . $file . '.php';
        if (!is_file($filePath) || !is_readable($filePath)) {
            return null;
        }
        $data = include $filePath;
        $data = $data && is_array($data) ? $data : [];
        return $this->data[$file] = $data;
    }

    private function parseKey($key)
    {
        $keys = explode('.', $key);
        $file = trim(array_shift($keys), '/');
        return [$file, $keys];
    }
}
