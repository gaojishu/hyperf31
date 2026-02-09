<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Handler\WebSocket\PingHandler;
use App\Handler\WebSocket\WsResponse;
use App\Service\Admin\WebSocketService;
use App\Utils\Auth\Auth;
use Hyperf\Contract\OnCloseInterface;
use Hyperf\Contract\OnMessageInterface;
use Hyperf\Contract\OnOpenInterface;
use Hyperf\Di\Annotation\Inject;

class WebSocketController implements OnMessageInterface, OnOpenInterface, OnCloseInterface
{
    #[Inject()]
    protected PingHandler $pingHandler;

    #[Inject]
    protected WebSocketService $webSocketService;


    public function onMessage($server, $frame): void
    {

        $data = json_decode($frame->data, true);

        // 如果不是标准 JSON，可以在这里抛异常或记录日志
        if (!$data || !isset($data['type'])) return;

        // 核心分发逻辑
        try {
            $userId = $this->webSocketService->getUserId($frame->fd);
            if (!$userId) {
                $server->close($frame->fd);
                return;
            }

            // 收到消息自动续期
            $this->webSocketService->bind($frame->fd, $userId);

            $handler = match ($data['type']) {
                'ping' => $this->pingHandler,
                // 其他业务...
                default => throw new \App\Exception\BusinessException("Unsupported message type: {$data['type']}")
            };

            $handler->handle($server, $data, $frame);
        } catch (\Throwable $e) {
            // 可选：给前端推送错误提示
            $server->push(WsResponse::error($e->getMessage()));
        }
    }

    public function onClose($server, int $fd, int $reactorId): void
    {
        $this->webSocketService->unbind($fd);
    }

    public function onOpen($server, $request): void
    {
        $userId = Auth::guard(Auth::GUARD_ADMIN)->getUserId();

        $this->webSocketService->bind($request->fd, $userId);

        $server->push($request->fd, WsResponse::success('Opened'));
    }
}
