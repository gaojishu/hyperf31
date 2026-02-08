<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Annotation\PermissionAnnotation;
use App\Middleware\Admin\AuthMiddleware;
use App\Middleware\Admin\PermissionMiddleware;
use App\Service\Admin\AdminActionService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\Middlewares;

#[AutoController()]
#[Middlewares([AuthMiddleware::class, PermissionMiddleware::class])]
class AdminActionController extends BaseController
{
    #[Inject()]
    private AdminActionService $adminActionService;

    #[PermissionAnnotation('adminAction:page', '日志查询')]
    public function page(\App\Request\Admin\AdminAction\AdminActionPageRequest $request)
    {
        $data =  $this->adminActionService->page($request->validated());
        return $this->setData($data)->apisucceed();
    }
}
