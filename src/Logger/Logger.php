<?php

/**
 * This file is part of the Boxunsoft package.
 *
 * (c) Jordy <arno.zheng@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Fw\Logger;

use Fw\Kernel;
use Fw\Logger\Handler\FileHandler;
use Fw\Request\Request;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;

/**
 * 日志类
 * Class Logger
 * @package Fw\Logger
 *
 * 根据PSR-3: Logger Interface
 *  https://www.php-fig.org/psr/psr-3/
 */
class Logger implements LoggerInterface
{
    use LoggerTrait;

    const DEBUG       = 0x00000001;
    const INFO        = 0x00000010;
    const NOTICE      = 0x00000100;
    const WARNING     = 0x00001000;
    const ERROR       = 0x00010000;
    const CRITICAL    = 0x00100000;
    const ALERT       = 0x01000000;
    const EMERGENCY   = 0x10000000;

    /**
     * @var int 错误等级
     */
    protected $level = LogLevel::INFO;
    const LEVEL_MAPPER = [
        LogLevel::DEBUG => self::DEBUG,
        LogLevel::INFO => self::INFO,
        LogLevel::NOTICE => self::NOTICE,
        LogLevel::WARNING => self::WARNING,
        LogLevel::ERROR => self::ERROR,
        LogLevel::CRITICAL => self::CRITICAL,
        LogLevel::ALERT => self::ALERT,
        LogLevel::EMERGENCY => self::EMERGENCY,
    ];

    /**
     * @var HandlerInterface
     */
    protected $handler;

    /**
     * @param string|null $level
     * @param HandlerInterface|null $handler
     */
    public function __construct(?string $level = LogLevel::DEBUG, ?HandlerInterface $handler = null)
    {
        if ($level && array_key_exists($level, self::LEVEL_MAPPER)) {
            $this->level = $level;
        }

        if ($handler) {
            $this->handler = $handler;
        }
    }

    /**
     * 处理日志内空的句柄
     *
     * @param HandlerInterface $handler
     * @return static
     */
    public function setHandler(HandlerInterface $handler)
    {
        $this->handler = $handler;
        return $this;
    }

    /**
     * 设置日志等级
     *
     * @param string $level
     * @return static
     */
    public function setLevel(string $level)
    {
        $this->level = $level;
        return $this;
    }

    public function log($level, $message, array $context = [])
    {
        if (!isset(self::LEVEL_MAPPER[$level])) {
            throw new \InvalidArgumentException(sprintf('Invalid parameter level, the values must be one of (%s)', implode(', ', array_values(self::LEVEL_MAPPER))));
        }

        if (self::LEVEL_MAPPER[$level] < self::LEVEL_MAPPER[$this->level]) {
            return;
        }

        $time = date('c');
        $Request = Request::getInstance();
        $host = 'cli' === PHP_SAPI ? 'cli' : $Request->getServerHost();
        $traceId = $Request->getTraceId();
        $serverIp = $Request->getServerIp();
        $clientIp = $Request->getClientIp();
        $content = $this->formatMessage($message, $context);

        $log = [
            'time' => $time,
            'level' => $level,
            'host' => $host,
            'trace_id' => $traceId,
            'server_ip' => $serverIp,
            'client_ip' => $clientIp,
            'message' => $content
        ];

        $this->getHandler()->write($log);
    }

    private function formatMessage($message, $context = [])
    {
        if ($message instanceof \Exception) {
            $trace = $message->getTrace();
            $trace = $trace[1];
            $trace = ' ;trace => ' . json_encode($trace, JSON_UNESCAPED_UNICODE);
            $message = $message->getCode() . ' => ' . $message->getMessage() . $trace;
        }

        if (is_string($message)) {
            $message = str_replace(["\r", "\n"], ' ', $message);
        } else {
            $message = json_encode(
                $message,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR
            );
        }

        $Request = Request::getInstance();
        $Kernel = Kernel::getInstance();

        $logData = [
            'pid' => getmypid(),
            'app' => $Kernel->getAppName(),
            'env' => $Kernel->getEnvironment(),
            'script' => $this->getCurrentScript(),
            'exec' => $this->getCurrentExec(),
            'msg' => mb_substr($message, 0, 6000),
        ];

        if (php_sapi_name() === 'fpm-fcgi') {
            $logData['request_method'] = $Request->method();
        }

        $logData += $context;

        return json_encode($logData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    protected function getCurrentExec()
    {
        static $exec =null;
        if($exec){
            return $exec;
        }

        if (empty($_SERVER['argv'])) {
            $exec = Request::getInstance()->getPathInfo();
        } else {
            $_ = array_reverse(explode('/', $_SERVER['argv'][0]??''));
            $exec = empty($_[1]) ? sprintf('/%s', $_[0]) : sprintf('/%s/%s', $_[1], $_[0]);
        }
        return $exec;
    }

    protected function getCurrentScript()
    {
        static $script=null;
        if($script){
            return $script;
        }

        if (empty($_SERVER['argv'])) {
            $Request = Request::getInstance();
            $uri = $Request->getUri();
            $limit = 1000;
            $script = strlen($uri) > $limit ? substr($uri, 0, $limit) . '...' : $uri;

            if(!empty($_POST)){
                $body = http_build_query($_POST);
                $body = strlen($body) > $limit ? substr($body, 0, $limit) . '...' : $body;
                $script = "-d '{$body}' '{$script}'";
            }
            $script = 'curl '.$script;
        } else {
            $script = 'php ' . $_SERVER['PWD'] . DIRECTORY_SEPARATOR . implode(" ", $_SERVER['argv']);
        }
        $script = urldecode($script); //url上的中文可能会被urlencode

        return $script;
    }

    /**
     * 如果没配置handler,默认使用FileHandler,输出到/var/log
     *
     * @return HandlerInterface
     */
    private function getHandler()
    {
        if ($this->handler) {
            return $this->handler;
        }

        $this->handler = new FileHandler();
        $this->handler->setSavePath('/var/log');

        return $this->handler;
    }
}
