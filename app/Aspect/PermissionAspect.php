<?php

declare(strict_types=1);

namespace App\Aspect;

use App\Annotation\PermissionAnnotation;
use App\Exception\BusinessException;
use App\Service\Admin\AdminService;
use App\Service\Admin\PermissionService;
use App\Util\Auth\Auth;
use App\Util\HttpResponse\HttpResponse;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\HttpServer\Contract\ResponseInterface;

#[Aspect()]
class PermissionAspect extends AbstractAspect
{


    // 指定要拦截的注解
    public array $annotations = [
        PermissionAnnotation::class,
    ];

    protected $response;

    #[Inject()]
    private AdminService $adminService;

    #[Inject()]
    private PermissionService $permissionService;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        // 1. 获取方法上的注解对象
        /** @var Permission $annotation */
        $annotation = $proceedingJoinPoint->getAnnotationMetadata()->method[PermissionAnnotation::class];

        $permissionCode = $annotation->code;

        // 2. 执行你的权限校验逻辑（例如从上下文获取当前用户，查数据库/缓存）
        $hasPermission = $this->checkUserPermission($permissionCode);


        if (!$hasPermission) {
            throw new BusinessException(403, '无权限');
        }

        // 切面切入后，执行对应的方法会由此来负责
        // $proceedingJoinPoint 为连接点，通过该类的 process() 方法调用原方法并获得结果
        // 在调用前进行某些处理
        $result = $proceedingJoinPoint->process();
        // 在调用后进行某些处理
        return $result;
    }

    private function checkUserPermission(string $code): bool
    {
        // 这里编写实际的权限判断逻辑
        $admin_id = Auth::guard(Auth::GUARD_ADMIN)->getUserId();

        //超级管理员id   不检查权限
        if ($admin_id == 1) {
            return true;
        }

        $permission = $this->permissionService->findByCode($code);

        //数据库不存在此权限，正常放行
        if (!$permission) {
            return true;
        }

        $admin = $this->adminService->findById($admin_id);

        if (!in_array($permission->key, $admin->permission_key)) {
            return false;
        }
        //记录访问日志





        return true;
    }
}
