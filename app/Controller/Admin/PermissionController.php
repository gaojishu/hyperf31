<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Annotation\PermissionAnnotation;
use App\Middleware\Admin\AuthMiddleware;
use App\Middleware\Admin\PermissionMiddleware;
use App\Request\Admin\Permission\PermissionCreateRequest;
use App\Request\Admin\Permission\PermissionUpdateRequest;
use App\Service\Admin\PermissionService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Contract\RequestInterface;

#[AutoController()]
#[Middlewares([AuthMiddleware::class, PermissionMiddleware::class])]
class PermissionController extends BaseController
{
    #[Inject()]
    private PermissionService $permissionService;

    #[PermissionAnnotation('permission:create', '权限创建')]
    public function create(PermissionCreateRequest $request)
    {
        $this->permissionService->create($request->validated());
        return $this->apisucceed('操作成功');
    }

    #[PermissionAnnotation('permission:update', '权限更新')]
    public function update(PermissionUpdateRequest $request)
    {
        $this->permissionService->update($request->validated());
        return $this->apisucceed('操作成功');
    }

    #[PermissionAnnotation('permission:delete', '权限删除')]
    public function delete(RequestInterface $request)
    {
        $this->permissionService->delete((int) $request->query('id'));
        return $this->apisucceed('操作成功');
    }

    //#[PermissionAnnotation('permission:read', '权限查询')]
    public function records()
    {
        $data = $this->permissionService->findAll();
        return $this->setData($data)->apisucceed();
    }
}
