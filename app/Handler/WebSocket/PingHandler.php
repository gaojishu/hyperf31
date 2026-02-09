<?php

namespace App\Handler\WebSocket;

class PingHandler implements MessageHandlerInterface
{
    public function handle($server, array $data, $frame): void
    {
        $server->push($frame->fd, WsResponse::success('pong'));
    }
}
