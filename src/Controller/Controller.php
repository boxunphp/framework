<?php

/**
 * This file is part of the Boxunsoft package.
 *
 * (c) Jordy <arno.zheng@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Fw\Controller;

use Fw\Traits\ErrorTrait;
use Fw\Traits\RequestTrait;
use Fw\Traits\ResponseTrait;

/**
 * 控制器
 *
 * Class Controller
 */
class Controller
{
    use ErrorTrait;
    use RequestTrait;
    use ResponseTrait;

    public function JSONError($code, $message, $params = [])
    {
        $this->errorCode = $code;
        $this->errorMessage = $params ? $message : vsprintf($message, $params);
        $this->response()->JSONError($code, $message, $params);
        if (method_exists($this, 'after')) {
            call_user_func([$this, 'after']);
        }
        exit;
    }

    public function JSONSuccess(array $data = [])
    {
        $this->errorCode = 0;
        $this->errorMessage = '';
        $this->response()->JSONSuccess($data);
        if (method_exists($this, 'after')) {
            call_user_func([$this, 'after']);
        }
        exit;
    }
}
