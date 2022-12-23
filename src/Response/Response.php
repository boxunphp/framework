<?php

/**
 * This file is part of the Boxunsoft package.
 *
 * (c) Jordy <arno.zheng@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Fw\Response;

use Fw\Instance\InstanceTrait;
use Fw\Helper\HttpCode;

class Response
{
    use InstanceTrait;

    /**
     * 跳转
     *
     * @param string $url 链接地址
     * @param int $httpCode HTTP状态码
     */
    public function redirect($url, $httpCode = HttpCode::FOUND)
    {
        header('Location:' . $url, true, $httpCode);
    }

    public function HTMLError($code)
    {
        header(sprintf('HTTP/1.1 %d %s', $code, HttpCode::message($code)));
    }

    public function json($data)
    {
        ob_clean();
        header('Content-type:application/json;charset=utf-8');
        //指定JSON_PARTIAL_OUTPUT_ON_ERROR,避免$data中有非utf-8字符导致json编码返回false
        echo json_encode($data, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function JSONError($code, $message, $params = [])
    {
        if ($params) {
            $message = vsprintf($message, $params);
        }
        $this->JSONOutput($code, $message);
    }

    public function JSONSuccess(array $data = [])
    {
        $this->JSONOutput(0, '成功', $data);
    }

    protected function JSONOutput($code, $message, array $data = [])
    {
        $code = intval($code);
        $response = [
            'flag' => $code ? 'failure' : 'success',
            'code' => $code,
            'message' => $message,
            'data' => $data ?: new \stdClass(),
        ];
        $this->json($response);
    }
}
