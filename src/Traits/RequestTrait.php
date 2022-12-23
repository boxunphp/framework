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

use Fw\Request\Request;

trait RequestTrait
{
    /**
     * @var \Fw\Request\Request
     */
    private $request;

    public function request()
    {
        if (null === $this->request) {
            $this->request = Request::getInstance();
        }

        return $this->request;
    }

    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }
}
