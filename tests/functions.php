<?php

/**
 * This file is part of the Boxunsoft package.
 *
 * (c) Jordy <arno.zheng@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

if (!function_exists('env')) {
    /**
     * @param $key
     * @return array|null
     * @throws \Exception
     */
    function env($key)
    {
        static $config;
        if (!$config) {
            $config = \Fw\Config\Config::getInstance()->setPath(__DIR__ . '/configs');
        }

        return $config->get($key);
    }
}
