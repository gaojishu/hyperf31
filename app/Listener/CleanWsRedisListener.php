<?php

declare(strict_types=1);

namespace App\Listener;

use App\Service\Admin\WebSocketService;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Event\Annotation\Listener;
use Psr\Container\ContainerInterface;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BeforeMainServerStart;
use Hyperf\Redis\Redis;

#[Listener]
class CleanWsRedisListener implements ListenerInterface
{
    // 在类中注入
    #[Inject()]
    protected StdoutLoggerInterface $logger;

    #[Inject]
    protected Redis $redis;

    #[Inject]
    protected WebSocketService $wsService;

    public function __construct(protected ContainerInterface $container) {}

    public function listen(): array
    {
        // 监听主服务启动前的事件
        return [
            BeforeMainServerStart::class,
        ];
    }

    public function process(object $event): void
    {
        $pattern = $this->wsService->getPrefix() . '*';

        try {
            // 使用 Lua 脚本保证原子性并避开游标问题
            // 逻辑：通过 keys 找到匹配项并直接删除
            $script = "
                local keys = redis.call('keys', ARGV[1])
                if #keys > 0 then
                    return redis.call('del', unpack(keys))
                else
                    return 0
                end
            ";

            // [Hyperf Redis Eval 文档](https://hyperf.wiki)
            // 参数说明：脚本, 参数数组, KEY数量(这里填0，因为我们用 ARGV 传匹配符)
            $result = $this->redis->eval($script, [$pattern], 0);

            $this->logger->info("WebSocket Redis cleaned. Count: " . $result);
        } catch (\Throwable $e) {
            $this->logger->error("Redis clean failed: " . $e->getMessage());
        }

        $this->logger->info('WebSocket Redis mappings cleared via SCAN.');
    }
}
