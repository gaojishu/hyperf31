<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Middleware\Admin\AuthMiddleware;
use App\Middleware\Admin\PermissionMiddleware;
use App\Request\Admin\Role\RoleStoreRequest;
use App\Service\Admin\RoleService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Contract\RequestInterface;

#[AutoController()]
#[Middlewares([AuthMiddleware::class, PermissionMiddleware::class])]
class RoleController extends BaseController
{
    #[Inject()]
    private RoleService $roleService;

    public function store(RoleStoreRequest $request)
    {
        $role = $this->roleService->store($request->validated());
        return $this->setData($role)->apisucceed('操作成功');
    }

    public function delete(RequestInterface $request)
    {
        $this->roleService->delete((int) $request->query('id'));
        return $this->apisucceed('操作成功');
    }

    /**
     * 获取全部角色
     */
    public function records()
    {
        $data = $this->roleService->findAll();
        return $this->setData($data)->apisucceed();
    }
}
