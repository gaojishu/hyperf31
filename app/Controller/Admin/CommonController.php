<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Enum\Admin\AdminDisabledStatusEnum;
use App\Enum\Admin\AsycnJob\AsyncJobQueueEnum;
use App\Enum\Admin\AsycnJob\AsyncJobStatusEnum;
use App\Enum\Admin\Files\FilesTypeEnum;
use App\Enum\Admin\PermissionTypeEnum;
use App\Utils\Aliyun\OssUtil;
use Hyperf\HttpServer\Annotation\AutoController;

#[AutoController()]
class CommonController extends BaseController
{

    public function enums()
    {
        $data = [
            'admin_disabled_status' => AdminDisabledStatusEnum::toArrayList(),
            'permission_type' => PermissionTypeEnum::toArrayList(),
            'files_type' => FilesTypeEnum::toArrayList(),
            'async_job_queue' => AsyncJobQueueEnum::toArrayList(),
            'async_job_status' => AsyncJobStatusEnum::toArrayList(),
        ];
        return $this->setData($data)->apisucceed();
    }

    public function oss()
    {
        $policy = OssUtil::generatePostPolicy();
        return $this->setData($policy)->apisucceed();
    }

    public function oss_post_policy()
    {
        $policy = OssUtil::generatePostPolicy();
        return $this->setData($policy)->apisucceed();
    }
}
