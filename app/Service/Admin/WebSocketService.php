<?php

declare(strict_types=1);

namespace App\Service\Admin;

use Hyperf\Di\Annotation\Inject;
use Hyperf\Redis\Redis;

class WebSocketService
{
    #[Inject]
    protected Redis $redis;

    private string $prefix = 'ws:';
    private string $fdPrefix = 'fd_to_user:';
    private string $userPrefix = 'user_to_fd:';

    public function getPrefix(): string
    {
        return $this->prefix; // 确保与 bind 方法中的前缀一致
    }

    public function getFdPrefix(): string
    {
        return $this->prefix . $this->fdPrefix;
    }

    public function getUserPrefix(): string
    {
        return $this->prefix . $this->userPrefix;
    }

    /**
     * 绑定或刷新连接
     */
    public function bind(int $fd, ?int $userId, int $ttl = 3600): void
    {
        $this->redis->set($this->getFdPrefix() . $fd, $userId, $ttl);
        $this->redis->set($this->getUserPrefix() . $userId, $fd, $ttl);
    }

    /**
     * 根据 FD 获取用户 ID
     */
    public function getUserId(int $fd): ?int
    {
        $userId = $this->redis->get($this->getFdPrefix() . $fd);
        return $userId ? (int)$userId : null;
    }

    /**
     * 根据用户 ID 获取 FD（用于定向推送）
     */
    public function getFdByUserId(?int $userId): ?int
    {
        $fd = $this->redis->get($this->getUserPrefix() . $userId);
        return $fd ? (int)$fd : null;
    }

    /**
     * 解绑连接（用于 onClose）
     */
    public function unbind(int $fd): void
    {
        $userId = $this->getUserId($fd);
        if ($userId) {
            $this->redis->del($this->getUserPrefix() . $userId);
        }
        $this->redis->del($this->getFdPrefix() . $fd);
    }
}
