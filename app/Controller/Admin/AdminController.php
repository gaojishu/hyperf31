<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Service\Admin\AdminService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;

#[AutoController()]
#[\Hyperf\HttpServer\Annotation\Middlewares([\App\Middleware\Admin\AuthMiddleware::class])]
class AdminController extends BaseController
{
    #[Inject()]
    private AdminService $adminService;

    public function create(\App\Request\Admin\AdminCreateRequest $request)
    {

        $this->adminService->create($request->validated());

        return $this->apisucceed('操作成功');
    }

    public function update(\App\Request\Admin\AdminUpdateRequest $request)
    {
        $this->adminService->update($request->validated());

        return $this->apisucceed('操作成功');
    }

    public function page()
    {
        $data =  $this->adminService->page();
        return $this->setData($data)->apisucceed();
    }
}
