<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Annotation\PermissionAnnotation;
use App\Middleware\Admin\AuthMiddleware;
use App\Middleware\Admin\PermissionMiddleware;
use App\Service\Admin\FilesService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Contract\RequestInterface;

#[AutoController()]
#[Middlewares([AuthMiddleware::class, PermissionMiddleware::class])]
#[PermissionAnnotation('files:all', '文件管理所有权限')]
class FilesController extends BaseController
{
    #[Inject()]
    private FilesService $filesService;


    public function create(\App\Request\Admin\Files\FilesCreateRequest $request)
    {
        $this->filesService->create($request->validated());

        return $this->apisucceed('操作成功');
    }


    public function hash(RequestInterface $request)
    {
        $file = $this->filesService->findByHash($request->query('hash'));

        return $this->setData($file)->apisucceed();
    }

    public function page(\App\Request\Admin\Files\FilesPageRequest $request)
    {
        $data =  $this->filesService->page($request->validated());
        return $this->setData($data)->apisucceed();
    }

    public function delete(\App\Request\Admin\Files\FilesDeleteRequest $request)
    {
        $this->filesService->delete($request->validated());
        return $this->apisucceed('操作成功');
    }
}
