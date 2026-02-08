<?php

declare(strict_types=1);

namespace App\Utils\Auth;

class Auth
{
    public const GUARD_ADMIN = 'admin';
    public const GUARD_CUSTOMER = 'customer';

    // ✅ 门面方法：返回 AuthGuard 实例
    public static function guard(string $name): AuthGuard
    {
        return new AuthGuard($name);
    }
}
