<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Controller\Admin;

use App\Request\Admin\AdminLoginRequest;
use App\Service\Admin\AdminService;
use App\Service\Admin\AuthService;
use App\Service\Admin\PermissionService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;

#[AutoController()]
class AuthController extends BaseController
{
    #[Inject()]
    private AuthService $authService;

    #[Inject()]
    private AdminService $adminService;

    #[Inject()]
    private PermissionService $permissionService;

    public function login(AdminLoginRequest $request)
    {
        $data = $request->validated();

        $result = $this->authService->login($data);

        return $this->setData($result)->apisucceed('登录成功');
    }


    #[\Hyperf\HttpServer\Annotation\Middlewares([\App\Middleware\Admin\AuthMiddleware::class])]
    public function info()
    {
        $adminId = $this->adminId();
        $result = $this->adminService->findById($adminId);

        return $this->setData($result)->apisucceed();
    }

    #[\Hyperf\HttpServer\Annotation\Middlewares([\App\Middleware\Admin\AuthMiddleware::class])]
    public function permission()
    {
        $adminId = $this->adminId();

        if ($adminId == 1) {
            $result = $this->permissionService->findAll();
        } else {
            $result = $this->adminService->findPermissionByAdminId($adminId);
        }

        return $this->setData($result)->apisucceed();
    }
}
