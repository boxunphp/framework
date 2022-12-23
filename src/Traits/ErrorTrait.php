<?php

/**
 * This file is part of the Boxunsoft package.
 *
 * (c) Jordy <arno.zheng@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Fw\Traits;

trait ErrorTrait
{
    /**
     * 处理逻辑过程中需要相应的错误吗
     * 
     * 如果需要 相应数据，使用指针方式传递
     */
    protected $errorCode = 0;

    /**
     * 错误信息
     *
     * @var string
     */
    protected $errorMessage = '';

    /**
     * 获取错误吗
     *
     * @return int
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * 获取错误信息
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * 错误信息
     *
     * @param int $errorCode
     * @param string $errorMessage
     * @return boolean
     */
    protected function setError($errorCode, $errorMessage)
    {
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;

        return false;
    }
}
