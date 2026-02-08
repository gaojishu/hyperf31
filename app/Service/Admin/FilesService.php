<?php

declare(strict_types=1);

namespace App\Service\Admin;

use App\Enum\Admin\Files\FilesTypeEnum;
use App\Enum\Admin\SortEnum;
use App\Exception\BusinessException;
use App\Model\Admin\Admin;
use App\Model\Admin\Files;
use App\Utils\Aliyun\OssUtil;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Filesystem\FilesystemFactory;

class FilesService
{


    #[Inject()]
    private FilesystemFactory $filesystemFactory;

    public function page(array $data)
    {
        // 1. 预处理参数
        $params = $data['params'] ?? [];
        $sort = $data['sort'] ?? [];

        $query = Files::query();

        // 2. 分页大小限制 (1-20 范围)
        $pagesize = (int)($params['page_size'] ?? 10);
        $pagesize = max(1, min($pagesize, 20));

        $query->when($params['category_id'] ?? null, function ($query, $category_id) {
            $query->where('category_id', $category_id);
        })
            ->when($params['type'] ?? null, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($params['name'] ?? null, function ($query, $name) {
                $query->where('name', $name);
            })
            ->when($params['created_at'] ?? null, function ($query, $created_at) {
                $query->whereBetween('created_at', $created_at);
            })
            ->when($params['updated_at'] ?? null, function ($query, $updated_at) {
                $query->whereBetween('updated_at', $updated_at);
            })
            // 排序：安全处理枚举
            ->when($sort['id'] ?? null, function ($query, $idSort) {
                $direction = SortEnum::tryFrom($idSort)?->sql() ?? 'desc';
                $query->orderBy('id', $direction);
            }, function ($query) {
                $query->orderBy('id', 'desc');
            });


        return $query->paginate($pagesize, ['*'], 'params.current');
    }

    public function findByHash($hash)
    {
        return Files::where('hash', $hash)->first();
    }

    public function delete($data)
    {
        $oss = $this->filesystemFactory->get('oss');
        foreach ($data['keys'] ?? [] as $key) {

            Db::transaction(function () use ($oss, $key) {
                $key = OssUtil::getPathByUrl($key);

                $file = Files::query()->where('key', $key)->delete();

                if ($file) {
                    $oss->delete($key);
                }
            });
        }
    }

    public function create(array $data)
    {
        $oss = $this->filesystemFactory->get('oss');
        $fileList = $data['file_list'] ?? [];
        foreach ($fileList as $val) {
            Db::transaction(function () use ($oss, $val) {
                $type = explode('/', $val['mime_type'])[0];
                $newKey = OssUtil::rename($val['key'], $type);
                $oss->copy($val['key'], $newKey);

                $file = new Files;
                $file->name = $val['name'];
                $file->key = $newKey;
                $file->size = $val['size'];
                $file->hash = $val['hash'];
                $file->mime_type = $val['mime_type'];
                $file->type = FilesTypeEnum::tryFrom($type);
                $file->category_id = $val['category_id'];
                $file->save();
            });
        }
    }
}
