<?php

declare(strict_types=1);

namespace App\Listener;

use App\Event\AdminNoticeCreateEvent;
use App\Handler\WebSocket\WsResponse;
use App\Service\Admin\WebSocketService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Event\Annotation\Listener;
use Psr\Container\ContainerInterface;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\WebSocketServer\Sender;

#[Listener]
class AdminNoticeWebSocketListener implements ListenerInterface
{

    #[Inject]
    protected Sender $sender;

    #[Inject]
    protected WebSocketService $webSocketService;

    public function __construct(protected ContainerInterface $container) {}

    public function listen(): array
    {
        // 监听主服务启动前的事件
        return [
            AdminNoticeCreateEvent::class,
        ];
    }

    public function process(object $event): void
    {
        $notice = $event->notice;
        $this->sender->push(
            $this->webSocketService->getFdByUserId($notice->admin_id),
            WsResponse::success('notice', [
                'title' => $notice->title,
                'content' => $notice->content,
                'attachments' => $notice->attachments,
            ])
        );
    }
}
