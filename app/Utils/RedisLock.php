<?php

declare(strict_types=1);

namespace App\Utils;

use Hyperf\Context\ApplicationContext;
use Hyperf\Redis\Redis;
use Psr\Log\LoggerInterface;
use Throwable;

class RedisLock
{
    protected Redis $redis;
    protected string $key;
    protected int $expire;
    protected int $waitInterval; // 等待间隔（毫秒）
    protected string $value; // 唯一标识，防止误删
    protected bool $acquired = false; // 记录是否已获得锁

    // 默认配置
    private const DEFAULT_EXPIRE = 30; // 秒
    private const DEFAULT_WAIT_INTERVAL = 10; // 毫秒
    private const MAX_WAIT_TIME = 60; // 最大等待时间 60 秒

    public function __construct(
        string $key,
        int $expire = self::DEFAULT_EXPIRE,
        int $waitInterval = self::DEFAULT_WAIT_INTERVAL
    ) {
        $container = ApplicationContext::getContainer();
        $this->redis = $container->get(Redis::class);

        // 获取 Logger（可选）
        $logger = $container->has(LoggerInterface::class)
            ? $container->get(LoggerInterface::class)
            : null;

        // 校验并限制参数范围
        $this->expire = max(1, min($expire, 3600)); // 1～3600 秒
        $this->waitInterval = max(1, min($waitInterval, 100)); // 1～100 毫秒

        $this->key = 'lock:' . $key;
        $this->value = $this->generateUniqueValue();

        if ($logger) {
            $logger->debug('RedisLock initialized', [
                'key' => $this->key,
                'expire' => $this->expire,
                'wait_interval_ms' => $this->waitInterval,
            ]);
        }
    }

    /**
     * 生成唯一的锁标识符
     */
    private function generateUniqueValue(): string
    {
        return bin2hex(random_bytes(16)) . '_' . microtime(true);
    }

    /**
     * 尝试获取锁（非阻塞）
     */
    public function tryLock(): bool
    {
        try {
            $result = $this->redis->set(
                $this->key,
                $this->value,
                ['nx', 'ex' => $this->expire]
            );

            $this->acquired = ($result === 'OK');
            return $this->acquired;
        } catch (Throwable $e) {
            $this->logError('Failed to acquire lock', $e);
            return false;
        }
    }

    /**
     * 阻塞式获取锁（带超时）
     */
    public function lock(int $timeout = 5): bool
    {
        if ($this->acquired) {
            return true; // 已持有锁
        }

        $timeout = min($timeout, self::MAX_WAIT_TIME);
        $start = microtime(true);
        $end = $start + $timeout;

        while (microtime(true) < $end) {
            if ($this->tryLock()) {
                return true;
            }

            $remaining = $end - microtime(true);
            if ($remaining <= 0) {
                break;
            }

            $sleepTime = min($this->waitInterval * 1000, $remaining * 1000000);
            usleep((int) $sleepTime);
        }

        return false;
    }

    /**
     * 安全释放锁（Lua 脚本保证原子性）
     */
    public function unlock(): bool
    {
        if (!$this->acquired) {
            return false;
        }

        try {
            $script = '
                if redis.call("GET", KEYS[1]) == ARGV[1] then
                    return redis.call("DEL", KEYS[1])
                else
                    return 0
                end
            ';

            $result = $this->redis->eval($script, [$this->key, $this->value], 1);
            $released = (bool) $result;

            if ($released) {
                $this->acquired = false;
            }

            return $released;
        } catch (Throwable $e) {
            $this->logError('Failed to release lock', $e);
            $this->acquired = false;
            return false;
        }
    }

    /**
     * 检查当前实例持有的锁是否仍然有效
     */
    public function isLocked(): bool
    {
        if (!$this->acquired) {
            return false;
        }

        try {
            $currentValue = $this->redis->get($this->key);
            return $currentValue === $this->value;
        } catch (Throwable $e) {
            $this->logError('Failed to check lock status', $e);
            return false;
        }
    }

    /**
     * 续约锁（延长锁的有效期）
     */
    public function renew(?int $newExpire = null): bool
    {
        if (!$this->acquired) {
            return false;
        }

        $newExpire = $newExpire ?? $this->expire;
        $newExpire = max(1, min($newExpire, 3600));

        try {
            $script = '
                if redis.call("GET", KEYS[1]) == ARGV[1] then
                    return redis.call("EXPIRE", KEYS[1], ARGV[2])
                else
                    return 0
                end
            ';

            $result = $this->redis->eval($script, [$this->key, $this->value, $newExpire], 1);
            return (bool) $result;
        } catch (Throwable $e) {
            $this->logError('Failed to renew lock', $e);
            return false;
        }
    }

    /**
     * 获取锁的剩余生存时间（TTL，单位：秒）
     * 返回值：
     *   >0: 剩余秒数
     *    0: key 存在但无过期时间（不应出现）
     *   -1: key 不存在
     *   -2: 其他错误
     */
    public function ttl(): int
    {
        try {
            return $this->redis->ttl($this->key);
        } catch (Throwable $e) {
            $this->logError('Failed to get TTL', $e);
            return -2;
        }
    }

    /**
     * 自动加锁 + 执行 + 自动释放（推荐使用方式）
     */
    public function run(\Closure $callback, int $timeout = 5)
    {
        if (!$this->lock($timeout)) {
            throw new \RuntimeException("Failed to acquire lock within {$timeout} seconds for key: {$this->key}");
        }

        try {
            return $callback();
        } finally {
            $this->unlock();
        }
    }

    /**
     * 析构函数（兜底释放，但不保证执行时机）
     */
    public function __destruct()
    {
        if ($this->acquired) {
            $this->unlock();
        }
    }

    /**
     * 记录错误日志
     */
    private function logError(string $message, Throwable $exception): void
    {
        $container = ApplicationContext::getContainer();
        if ($container->has(LoggerInterface::class)) {
            $logger = $container->get(LoggerInterface::class);
            $logger->error($message, [
                'key' => $this->key,
                'exception_class' => get_class($exception),
                'exception_message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
        }
    }
}
