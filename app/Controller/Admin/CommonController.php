<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Enum\Admin\AdminDisabledStatusEnum;
use App\Enum\Admin\PermissionTypeEnum;
use Hyperf\HttpServer\Annotation\AutoController;

#[AutoController()]
class CommonController extends BaseController
{
    public function enums()
    {
        $data = [
            'adminDisabledStatus' => AdminDisabledStatusEnum::toArrayList(),
            'permissionType' => PermissionTypeEnum::toArrayList(),
        ];
        return $this->setData($data)->apisucceed();
    }
}
