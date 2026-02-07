<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Request\Admin\Permission\PermissionCreateRequest;
use App\Request\Admin\Permission\PermissionUpdateRequest;
use App\Service\Admin\PermissionService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Contract\RequestInterface;

#[AutoController()]
#[\Hyperf\HttpServer\Annotation\Middlewares([\App\Middleware\Admin\AuthMiddleware::class])]
class PermissionController extends BaseController
{
    #[Inject()]
    private PermissionService $permissionService;

    public function create(PermissionCreateRequest $request)
    {
        $this->permissionService->create($request->validated());
        return $this->apisucceed('操作成功');
    }

    public function update(PermissionUpdateRequest $request)
    {
        $this->permissionService->update($request->validated());
        return $this->apisucceed('操作成功');
    }

    public function delete(RequestInterface $request)
    {
        $this->permissionService->delete((int) $request->query('id'));
        return $this->apisucceed('操作成功');
    }

    /**
     * 获取全部权限
     */
    public function records()
    {
        $data = $this->permissionService->findAll();
        return $this->setData($data)->apisucceed();
    }
}
