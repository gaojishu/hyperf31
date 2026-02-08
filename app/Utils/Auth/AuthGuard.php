<?php

declare(strict_types=1);

namespace App\Utils\Auth;

use App\Model\Admin\Admin;
use Carbon\Carbon;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Redis\Redis;
use Psr\Container\ContainerInterface;
use Ramsey\Uuid\Uuid;

class AuthGuard
{

    private const TOKEN_PREFIX = 'auth:token';
    private const USER_TOKEN_PREFIX = 'auth:user';

    #[Inject]
    protected Redis $redis;

    #[Inject]
    protected ContainerInterface $container;

    public function __construct(
        protected string $guard,

    ) {}

    public function generateToken(int $userId, int $ttl = 3600 * 24 * 30): array
    {
        $token = Uuid::uuid4()->toString();
        $redis = $this->redis;

        $tokenKey = $this->tokenKey($token);
        $userTokenKey = $this->userTokenKey($userId);

        // 删除旧 token
        $oldToken = $redis->get($userTokenKey);
        if ($oldToken) {
            $redis->del($this->tokenKey($oldToken));
        }

        // 存储新 token
        $redis->setex($tokenKey, $ttl, $userId);
        $redis->setex($userTokenKey, $ttl, $token);

        return [
            'token' => $token,
            'expire' => Carbon::now()->timestamp + $ttl,
            'header' => 'Authorization',
            'prefix' => 'Bearer',
        ];
    }

    public function getUserId(): ?int
    {
        $token = get_request_token();
        if (!$token) {
            return null;
        }

        $redis = $this->redis;
        $userId = $redis->get($this->tokenKey($token));
        return $userId ? (int) $userId : null;
    }

    public function getUserByToken(): mixed
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return null;
        }

        return $this->findUserById($userId);
    }

    public function validateToken(): bool
    {
        return $this->getUserId() !== null;
    }

    public function invalidateToken(string $token): bool
    {
        $redis = $this->redis;
        $tokenKey = $this->tokenKey($token);
        $userId = $redis->get($tokenKey);

        if ($userId) {
            $redis->del($this->userTokenKey((int)$userId));
        }

        return (bool) $redis->del($tokenKey);
    }

    // --- Helper Methods ---

    public function tokenKey(string $token): string
    {
        return implode(':', [
            self::TOKEN_PREFIX,
            $this->guard,
            $token
        ]);
    }

    public function userTokenKey(int $userId): string
    {
        return implode(':', [
            self::USER_TOKEN_PREFIX,
            $this->guard,
            $userId
        ]);
    }

    private function findUserById(int $userId): mixed
    {
        return match ($this->guard) {
            Auth::GUARD_ADMIN => Admin::find($userId),
            // Auth::GUARD_CUSTOMER => Customer::find($userId),
            default => null,
        };
    }
}
