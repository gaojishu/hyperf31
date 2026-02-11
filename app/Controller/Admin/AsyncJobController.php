<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Annotation\PermissionAnnotation;
use App\Middleware\Admin\AuthMiddleware;
use App\Middleware\Admin\PermissionMiddleware;
use App\Service\Admin\AsyncJobService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\Middlewares;

#[AutoController()]
#[Middlewares([AuthMiddleware::class, PermissionMiddleware::class])]
class AsyncJobController extends BaseController
{
    #[Inject()]
    private AsyncJobService $async_job_service;

    #[PermissionAnnotation('adminAction:page', '异步任务查询')]
    public function page(\App\Request\Admin\AsyncJob\AsyncJobPageRequest $request)
    {
        $data =  $this->async_job_service->page($request->validated());
        return $this->setData($data)->apisucceed();
    }

    #[PermissionAnnotation('adminAction:export', '日志导出表格')]
    public function export(\App\Request\Admin\AsyncJob\AsyncJobPageRequest $request)
    {
        $this->async_job_service->export($request->validated());

        return $this->apisucceed('提交成功，可在消息中心查看下载');
    }
}
