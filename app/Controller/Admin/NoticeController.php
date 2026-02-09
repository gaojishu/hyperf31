<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Middleware\Admin\AuthMiddleware;
use App\Service\Admin\NoticeService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\Middlewares;

#[AutoController()]
#[Middlewares([AuthMiddleware::class])]
class NoticeController extends BaseController
{
    #[Inject()]
    private NoticeService $noticeService;

    public function page(\App\Request\Admin\Notice\NoticePageRequest $request)
    {
        $data =  $this->noticeService->page($request->validated());
        return $this->setData($data)->apisucceed();
    }
}
