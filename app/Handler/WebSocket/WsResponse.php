<?php

namespace App\Handler\WebSocket;

use Hyperf\Engine\WebSocket\Frame;
use Hyperf\Engine\WebSocket\Opcode;

class WsResponse
{
    /**
     * 统一成功消息格式
     */
    public static function success(string $type, ?array $data = null, ?string $message = null): string
    {
        $payload = json_encode([
            'type' => $type,
            'data' => $data,
            'message'  => $message,
        ], JSON_UNESCAPED_SLASHES);

        return $payload;
        //return new Frame(payloadData: $payload, opcode: Opcode::TEXT);
    }

    /**
     * 统一错误消息格式
     */
    public static function error(string $message = 'error'): string
    {
        $payload = json_encode([
            'type' => 'error',
            'data' => null,
            'message'  => $message,
        ], JSON_UNESCAPED_SLASHES);
        return $payload;
        //return new Frame(payloadData: $payload, opcode: Opcode::TEXT);
    }
}
