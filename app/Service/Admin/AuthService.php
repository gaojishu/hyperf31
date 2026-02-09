<?php

declare(strict_types=1);

namespace App\Service\Admin;

use App\Exception\BusinessException;
use App\Model\Admin\Admin;
use App\Utils\Auth\Auth;

class AuthService
{
    public function login(array $data)
    {
        $admin = Admin::where('username', $data['username'])->first();

        if (!$admin || !password_verify($data['password'], $admin->password)) {
            throw new BusinessException('用户名或密码错误');
        }

        // 生成token
        $token = Auth::guard(Auth::GUARD_ADMIN)->generateToken($admin->id);

        return $token;
    }
}
