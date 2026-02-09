<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Amqp\Producer\AdminActionExportProducer;
use App\Annotation\PermissionAnnotation;
use App\Middleware\Admin\AuthMiddleware;
use App\Middleware\Admin\PermissionMiddleware;
use App\Service\Admin\AdminActionService;
use Hyperf\Amqp\Producer;
use Hyperf\Context\ApplicationContext;
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

    #[PermissionAnnotation('adminAction:export', '日志导出表格')]
    public function export(\App\Request\Admin\AdminAction\AdminActionPageRequest $request)
    {
        $producer = ApplicationContext::getContainer()->get(Producer::class);
        $producer->produce(new AdminActionExportProducer([
            'admin_id' => $this->admin_id(),
            'query' => $request->validated()
        ]));
        return $this->apisucceed('提交成功，可在消息中心查看下载');
    }
}
