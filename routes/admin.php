<?php

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

use Hyperf\HttpServer\Router\Router;


Router::addServer('ws', function () {
    Router::get('/', \App\Controller\Admin\WebSocketController::class);
});

Router::get('/favicon.ico', function () {
    return '';
});
