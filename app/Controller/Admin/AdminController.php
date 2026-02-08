<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Annotation\PermissionAnnotation;
use App\Middleware\Admin\AuthMiddleware;
use App\Middleware\Admin\PermissionMiddleware;
use App\Service\Admin\AdminService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\Middlewares;

#[AutoController()]
#[Middlewares([AuthMiddleware::class, PermissionMiddleware::class])]
class AdminController extends BaseController
{
    #[Inject()]
    private AdminService $adminService;

    #[PermissionAnnotation('admin:create', '管理员创建')]
    public function create(\App\Request\Admin\Admin\AdminCreateRequest $request)
    {

        $this->adminService->create($request->validated());

        return $this->apisucceed('操作成功');
    }

    #[PermissionAnnotation('admin:update', '管理员更新')]
    public function update(\App\Request\Admin\Admin\AdminUpdateRequest $request)
    {
        $this->adminService->update($request->validated());

        return $this->apisucceed('操作成功');
    }

    //#[PermissionAnnotation('admin:page', '管理员查询')]
    public function page(\App\Request\Admin\Admin\AdminPageRequest $request)
    {
        $data =  $this->adminService->page($request->validated());
        return $this->setData($data)->apisucceed();
    }
}
