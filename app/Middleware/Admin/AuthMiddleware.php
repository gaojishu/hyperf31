<?php

declare(strict_types=1);

namespace App\Middleware\Admin;

use App\Exception\BusinessException;
use App\Util\Auth\Auth;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthMiddleware implements MiddlewareInterface
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 从 Header 或 Query 获取 token
        $authorization = $request->getHeaderLine('Authorization');
        $token = null;

        if (str_starts_with($authorization, 'Bearer ')) {
            $token = substr($authorization, 7);
        }

        if (! $token) {
            $token = $request->getQueryParams()['token'] ?? null;
        }

        if (! $token) {
            throw new BusinessException(401, 'Authorization header or query param "token" is required.');
        }

        $userId = Auth::guard(Auth::GUARD_ADMIN)->getUserIdByToken($token);

        if (! $userId) {
            throw new BusinessException(401, 'Invalid or expired token.');
        }

        //token验证通过 放行

        return $handler->handle($request);
    }
}
