<?php

namespace App\Handler\WebSocket;

use Swoole\WebSocket\Frame;

interface MessageHandlerInterface
{
    /**
     * @param \Swoole\WebSocket\Server $server
     * @param array $data 已经 json_decode 后的数组
     * @param Frame $frame 原始帧对象
     */
    public function handle($server, array $data, Frame $frame): void;
}
