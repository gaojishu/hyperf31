<?php

declare(strict_types=1);

use Hyperf\HttpServer\Router\Router;

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */




$routeDir = BASE_PATH . '/routes';
if (is_dir($routeDir)) {
    foreach (glob($routeDir . '/*.php') as $file) {
        require_once $file;
    }
}
