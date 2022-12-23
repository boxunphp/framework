<?php

/**
 * This file is part of the Boxunsoft package.
 *
 * (c) Jordy <arno.zheng@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Fw\View;

use Fw\Instance\InstanceTrait;

class View
{
    use InstanceTrait;

    private $data;
    protected $rootPath;
    protected $extensionName = '.phtml';

    /**
     * 页面显示时使用该方法,防止XSS
     * @param mixed $value
     * @param string $default
     */
    public function escape(&$value, $default = '')
    {
        return isset($value) && $value ? htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : $default;
    }

    /**
     * 赋值
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function assign($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * 输出渲染之后的内容
     * @param string $tpl
     * @throws \Exception
     */
    public function render($tpl)
    {
        echo $this->fetch($tpl);
    }

    /**
     * 获取渲染之后的内容
     *
     * @param string $tpl
     * @return string
     * @throws \Exception
     */
    public function fetch($tpl)
    {
        if (!$tpl) {
            throw new \InvalidArgumentException('Invalid template parameter');
        }
        $tplFile = $this->_getTplFile($tpl);
        if (!file_exists($tplFile)) {
            throw new \Exception('The template file ' . $tplFile . ' is not exists', 404);
        }

        $this->data && extract($this->data);
        $this->data = null;

        ob_start();
        include $tplFile;
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }

    public function template($tpl)
    {
        return $this->_getTplFile($tpl);
    }

    public function setRootPath($path)
    {
        $this->rootPath = $path;
        return $this;
    }

    public function getRootPath()
    {
        if (!$this->rootPath) {
            $this->rootPath = './View';
        }

        return $this->rootPath;
    }

    protected function _getTplFile($tpl)
    {
        $path = $this->getRootPath();
        $tpl = trim($tpl, '/\\');
        return $path . DIRECTORY_SEPARATOR . $tpl . $this->extensionName;
    }
}
