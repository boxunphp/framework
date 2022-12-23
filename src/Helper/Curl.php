<?php

/**
 * This file is part of the Boxunsoft package.
 *
 * (c) Jordy <arno.zheng@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Fw\Helper;

use Fw\Instance\InstanceTrait;
use Fw\Request\Request;

class Curl
{
    use InstanceTrait;

    public static $responseHeader = '';

    /**
     * GET请求
     * demo
      $url = 'https://quote.eastmoney.com/newapi/getlrqs';
      $params = ['code' => '601318'];
      $responseHeaders = [];
      $options = [
          'timeout' => 10,
          'headers' => [
              'Referer' => 'https://quote.eastmoney.com',
              'Content-Type' => 'application/json',
          ]
      ];
      $res = \Fw\Helper\Curl::get($url, $params, $options, $responseHeaders);
     *
     * @param string $url
     * @param array $params
     * @param array $options
     * @param array $responseHeaders
     * @return string
     */
    public static function get($url, $params = [], $options = [], &$responseHeaders = [])
    {
        return self::_curl('GET', $url, $params, $options, $responseHeaders);
    }

    /**
     * POST请求
     * demo
     $url = 'https://quote.eastmoney.com/newapi/getlrqs';
      $params = ['code' => '601318'];
      $responseHeaders = [];
      $options = [
          'timeout' => 10,
          'headers' => [
              'Referer' => 'https://quote.eastmoney.com',
              'Content-Type: application/x-www-form-urlencoded',
          ]
      ];
      $res = \Fw\Helper\Curl::post($url, $params, $options, $responseHeaders);
     *
     * @param string $url
     * @param array $params
     * @param array $options
     * @param array $responseHeaders
     * @return string
     */
    public static function post($url, $params = [], $options = [], &$responseHeaders = [])
    {
        return self::_curl('POST', $url, $params, $options, $responseHeaders);
    }

    /**
     * CURL逻辑
     *
     * @param string $method
     * @param string $url
     * @param array $params
     * @param array $options
     * @param array $responseHeaders
     * @return string
     */
    protected static function _curl($method, $url, $params = [], $options = [], &$responseHeaders = [])
    {
        $useHttpBuildQuery = isset($options['use_http_build_query']) ? (bool)$options['use_http_build_query'] : true;
        $headers = isset($options['headers']) ? $options['headers'] : array();

        if (in_array($method, ['GET'])) {
            if ($params) {
                $queryString = http_build_query($params);
                if (strpos($url, '?') === false) {
                    $url .= '?' . $queryString;
                } else {
                    $url .= '&' . $queryString;
                }
            }
        } else {
            if ($useHttpBuildQuery && is_array($params)) {
                $params = http_build_query($params);
            }
        }

        $curlHandle = self::_curlHandle($url, $options);
        if (!$curlHandle) {
            return false;
        }
        
        $opts = self::_curlOptions($method, $url, $params, $options);

        curl_setopt_array($curlHandle, $opts);
        $result = curl_exec($curlHandle);
        $responseHeaders = [];
        $curlInfo = curl_getinfo($curlHandle);
        $responseHeaders['http_code'] = isset($curlInfo['http_code']) ? $curlInfo['http_code'] : null;
        if (empty($responseHeaders['http_code'])) {
            $responseHeaders['curl_errno'] = curl_errno($curlHandle);
            $responseHeaders['curl_error'] = curl_error($curlHandle);
            $responseHeaders['curl_info'] = $curlInfo;
        }
        if ($opts[CURLOPT_HEADER] && isset($curlInfo['header_size']) && $curlInfo['header_size'] > 0) {
            self::$responseHeader = substr($result, 0, $curlInfo['header_size']);
            $result = substr($result, $curlInfo['header_size']);
        }
        if(empty($options['keep_alive'])){
            curl_close($curlHandle);
        }
        self::_curlLog($url, $params, $headers, $responseHeaders, $result, $curlInfo);

        return $result;
    }

    /**
     * 获取curl句柄
     *
     * @param string $url
     * @param array $options
     * @return resource
     */
    protected static function _curlHandle($url, $options)
    {
        static $keepAliveHandles = [];
        if(!empty($options['keep_alive'])){
            $parsedArr= parse_url($url);
            $host = !empty($parsedArr['host']) ? $parsedArr['host'] : '';
            $port = !empty($parsedArr['port']) ? $parsedArr['port'] : '';
            if (empty($host)) {
                return false;
            }
            $hashKey = sprintf('%s:%s', $host, $port);
            if(empty($keepAliveHandles[$hashKey]) || (time() - $keepAliveHandles[$hashKey]['init_at'] > $options['keep_alive'])){
                $keepAliveHandles[$hashKey]['handle'] = curl_init();
                $keepAliveHandles[$hashKey]['init_at'] = time();
            }
            $curlHandle = $keepAliveHandles[$hashKey]['handle'];
        }else{
            $curlHandle = curl_init();
        } 
        return $curlHandle;
    }

    /**
     * Curl选项
     *
     * @param string $method
     * @param string $url
     * @param array $params
     * @param array $options
     * @return array
     */
    protected static function _curlOptions($method, $url, $params, $options)
    {
        $timeout = isset($options['timeout']) ? $options['timeout'] : 3;
        $connectTimeout = isset($options['connect_timeout']) ? $options['connect_timeout'] : 3;
        $headers = isset($options['headers']) ? $options['headers'] : array();

        $opts = [
            CURLOPT_URL            => $url,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => $connectTimeout,
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_HEADER         => 1,
        ];
        if ('POST' === $method) {
            $opts['CURLOPT_POST'] = 1;
            $opts['CURLOPT_POSTFIELDS'] = $params;
        }

        if ($headers) {
            $opts[CURLOPT_HTTPHEADER] = $headers;
        }

        // 接口返回的数据是否要gzip解压缩
        if (!empty($options['with_gzip'])) {
            $opts[CURLOPT_ENCODING] = 'gzip';
        }

        // 是否只允许ipv4解析
        if (!empty($options['only_ipv4'])) {
            $opts[CURLOPT_IPRESOLVE] = CURL_IPRESOLVE_V4;
        }

        // 是否需要保存并发送cookie
        if (!empty($options['cookie_file'])) {
            $opts[CURLOPT_COOKIEFILE] = $options['cookie_file'];
            $opts[CURLOPT_COOKIEJAR] = $options['cookie_file'];
        }
        return $opts;
    }

    /**
     * Curl日志
     *
     * @param string $url
     * @param array $params
     * @param array $headers
     * @param array $responseHeaders
     * @param mixed $result
     * @param array $curlInfo
     * @return void
     */
    protected static function _curlLog($url, $params, $headers, $responseHeaders, $result, $curlInfo)
    {
        $Request = Request::getInstance();
        $logData = array(
            'log_time'       => date('H:i:s', time()),
            'ip'             => $Request->getClientIp(),
            'curl_errno'     => isset($responseHeaders['curl_errno']) ? $responseHeaders['curl_errno'] : 0,
            'curl_error'     => isset($responseHeaders['curl_error']) ? $responseHeaders['curl_error'] : '',
            'request_url'    => $url,
            'request_param'  => $params,
            'request_header' => $headers,
            'http_referer'   => $Request->referer(),
            'http_code'      => $responseHeaders['http_code'],
            'ua'             => $Request->userAgent(),
            'curl_info'      => $curlInfo,
            'total_time'     => $curlInfo['total_time'],
        );
    
        if ($responseHeaders['http_code'] != 200) {
            $logData['result'] = $result;
        }

        logger()->info($logData);
    }
}
