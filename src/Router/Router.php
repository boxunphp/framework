<?php

/**
 * This file is part of the Boxunsoft package.
 *
 * (c) Jordy <arno.zheng@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Fw\Router;

use Fw\Instance\InstanceTrait;
use Fw\Request\Request;
use Fw\Traits\RequestTrait;
use Fw\Helper\HttpCode;

/**
 * URI路由
 *
 * Class Router
 */
final class Router
{
    use InstanceTrait;
    use RequestTrait;

    /**
     * 支持的扩展名后缀,空位不限制
     *
     * @var array
     */
    protected $supportedSuffixs = [];

    /**
     * 路由配置
     *
     * @var array
     */
    protected $routes = [];

    protected function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * 路由配置
     *
     * @param array $config
     * uri规则 => 匹配后实际uri，实际uri决定了使用哪个controller
     [
         '/user/:user_id/get_list/:group_id' => '/user/get_list',
     ]
     * @return self
     */
    public function setConfig($config)
    {
        $this->routes = $config ?: [];
        return $this;
    }

    /**
     * 路由
     *
     * @return string
     */
    public function route()
    {
        $pathInfo = $this->request()->getPathInfo();
        // 验证路径和扩展名
        $path = $this->validatePathInfo($pathInfo);
        // 匹配路由配置
        list($path, $params) = $this->matchRoutes($path);
        $filePath = $this->parsePath($path);
        $this->request()->setAttributes($params);
        return $filePath;
    }

    /**
     * 设置支持的扩展名后缀,空位不限制
     *
     * @param array $supportedSuffixs
     * @return void
     */
    public function setSupportedSuffixs($supportedSuffixs)
    {
        $this->supportedSuffixs = $supportedSuffixs;
    }

    /**
     * 获取去掉扩展名之后的路径,顺便验证一下扩展名
     *
     * @param string $pathInfo
     * @return string
     */
    protected function validatePathInfo($pathInfo)
    {
        $extension = pathinfo($pathInfo, PATHINFO_EXTENSION);
        if ($this->supportedSuffixs && (!$extension || !in_array($extension, $this->supportedSuffixs))) {
            throw new RouterException('The extension is not allowed', HttpCode::BAD_REQUEST);
        }
        if ($extension) {
            $path = substr($pathInfo, 0, -strlen($extension) - 1);
        } else {
            $path = $pathInfo;
        }
        // url只能小写字母、数字和下划线组成,其他不允许
        if (preg_match('/[^a-z0-9\/_]+/i', $path)) {
            throw new \Exception('URLs can only consist of lowercase letters, numbers, and underscores', HttpCode::BAD_REQUEST);
        }
        return $path;
    }

    /**
     * 匹配路由配置
     *
     * @param string $path
     * @return array [path, params]
     */
    protected function matchRoutes($path)
    {
        $params = [];
        $routedPath = $path;
        foreach ($this->routes as $router => $_routedPath) {
            /**
             * /user/:user_id/get_list/:group_id
             * 匹配到的matches的格式
                Array
                (
                    [0] => Array
                        (
                            [0] => :user_id
                            [1] => :group_id
                        )

                )
             */
            if (!preg_match_all('/:[a-z0-9_]+/i', $router, $matches, PREG_PATTERN_ORDER)) {
                continue;
            }
            $paramNames = [];
            $regexUri = $router;
            foreach ($matches[0] as $m) {
                // 替换后的regexUri为 /user/([a-zA-Z0-9_]+)/get_list/([a-zA-Z0-9_]+)
                $regexUri = str_replace($m, '([a-zA-Z0-9_]+)', $regexUri);
                /**
                 * paramNames为
                 [
                     'user_id',
                     'group_id'
                 ]
                 */
                $paramNames[] = substr($m, 1);
            }
            if (!preg_match('#^' . $regexUri . '$#i', $path, $matches)) {
                unset($paramNames);
                continue;
            }
            foreach ($paramNames as $key => $field) {
                $params[$field] = $matches[$key + 1] ?? '';
            }
            $routedPath = $_routedPath;
            break;
        }
        return [$routedPath, $params];
    }

    /**
     * 把uri路径解析成文件路径,也是类的路径
     *
     * @param string $path
     * @return string
     */
    protected function parsePath($path)
    {
        $path = trim($path, '/');
        // 默认Index
        if (!$path) {
            return 'Index';
        }
        $uriArr = array_filter(explode('/', $path));
        $pathArr = [];
        foreach ($uriArr as $word) {
            $wordArr = array_filter(explode('_', $word));
            $pathArr[] = implode('', array_map('ucfirst', $wordArr));
        }
        $filePath = implode('/', $pathArr);
        return $filePath;
    }
}
