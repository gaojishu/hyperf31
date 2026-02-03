<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Controller\Admin;

use App\Request\Admin\AdminLoginRequest;
use App\Service\Admin\AuthService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;

#[AutoController()]
class AuthController extends BaseController
{
    #[Inject()]
    private AuthService $authService;
    public function login(AdminLoginRequest $request)
    {
        $data = $request->validated();

        $result = $this->authService->login($data);

        return $this->setData($result)->apisucceed('登录成功');
    }
}
