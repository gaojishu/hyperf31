<?php

declare(strict_types=1);

namespace App\Middleware\Admin;

use App\Annotation\PermissionAnnotation;
use App\Exception\BusinessException;
use App\Service\Admin\AdminActionService;
use App\Service\Admin\AdminService;
use App\Service\Admin\PermissionService;
use App\Util\Auth\Auth;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Router\Dispatched;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PermissionMiddleware implements MiddlewareInterface
{
    #[Inject()]
    protected AdminActionService $adminActionService;

    #[Inject]
    protected PermissionService $permissionService;

    #[Inject]
    protected AdminService $adminService;


    public function __construct(protected ContainerInterface $container) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
         // 1. 获取路由解析出的注解信息
        /** @var Dispatched $dispatched */
        $dispatched = $request->getAttribute(Dispatched::class);

        // 尝试从控制器方法获取 PermissionAnnotation 注解
        $annotation = $this->getAnnotation($dispatched);


        $hasPermission = $this->checkPermission($annotation?->code, $request);

        if (!$hasPermission) {
            throw new BusinessException(403, '无权限.');
        }

        return $handler->handle($request);
    }

    private function getAnnotation(Dispatched $dispatched)
    {
        if ($dispatched->isFound() && is_array($dispatched->handler->callback)) {
            [$class, $method] = $dispatched->handler->callback;
            // 使用 Hyperf 的 AnnotationCollector 获取注解
            return \Hyperf\Di\Annotation\AnnotationCollector::getClassMethodAnnotation($class, $method)[PermissionAnnotation::class] ?? null;
        }
        return null;
    }

    private function checkPermission(?string $code, ServerRequestInterface $request): bool
    {

        // 这里编写实际的权限判断逻辑
        $admin_id = Auth::guard(Auth::GUARD_ADMIN)->getUserId();

        $permission = null;
        if ($code) {
            $permission = $this->permissionService->findByCode($code);
        }

        //日志   如何获取request 上下文
        $this->adminActionService->create([
            'admin_id' => $admin_id,
            'duration' => 0,
            'ip'           => $request->getServerParams()['remote_addr'] ?? '', // 获取 IP
            'method'       => $request->getMethod(),                            // 获取请求方法
            'path'          => $request->getUri()->getPath(),                              // 获取 URI
            'params'       => $request->getParsedBody() ?: null,                     // 获取所有参数
            'query_params' => $request->getQueryParams() ?: null,                   // 获取查询参数
            'remark'       => $permission?->name,
        ]);

        //超级管理员id   不检查权限
        if ($admin_id == 1) {
            return true;
        }

        $admin = $this->adminService->findById($admin_id);

        if (!in_array($permission->key, $admin->permission_key)) {
            return false;
        }

        return true;
    }
}
