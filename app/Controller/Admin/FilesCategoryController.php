<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Annotation\PermissionAnnotation;
use App\Middleware\Admin\AuthMiddleware;
use App\Middleware\Admin\PermissionMiddleware;
use App\Request\Admin\Role\RoleStoreRequest;
use App\Service\Admin\FilesCategoryService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Contract\RequestInterface;

#[AutoController()]
#[Middlewares([AuthMiddleware::class, PermissionMiddleware::class])]
#[PermissionAnnotation('files:all', '文件管理所有权限')]
class FilesCategoryController extends BaseController
{
    #[Inject()]
    private FilesCategoryService $filesCategoryService;

    public function store(RoleStoreRequest $request)
    {
        $role = $this->filesCategoryService->store($request->validated());
        return $this->setData($role)->apisucceed('操作成功');
    }

    public function delete(RequestInterface $request)
    {
        $this->filesCategoryService->delete((int) $request->query('id'));
        return $this->apisucceed('操作成功');
    }


    public function records()
    {
        $data = $this->filesCategoryService->findAll();
        return $this->setData($data)->apisucceed();
    }
}
