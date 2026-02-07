<?php

declare(strict_types=1);

namespace App\Service\Admin;

use App\Enum\Admin\SortEnum;
use App\Model\Admin\AdminAction;

class AdminActionService
{
    const FIELD = ['admin_id', 'duration', 'ip', 'method', 'remark', 'path', 'params', 'query_params',];
    public function page(array $data)
    {
        // 1. 预处理参数
        $params = $data['params'] ?? [];
        $sort = $data['sort'] ?? [];

        var_dump($params);
        $query = AdminAction::query();

        // 2. 分页大小限制 (1-20 范围)
        $pagesize = (int)($params['page_size'] ?? 10);
        $pagesize = max(1, min($pagesize, 20));

        $query->when($params['id'] ?? null, function ($query, $id) {
            $query->where('id', $id);
        })
            // 筛选：用户名 模糊匹配 (修正了你代码中的 filter 错误)
            ->when($params['method'] ?? null, function ($query, $method) {
                $query->where('method', 'like', "%{$method}%");
            })
            // 筛选：昵称 模糊匹配
            ->when($params['path'] ?? null, function ($query, $path) {
                $query->where('path', 'like', "%{$path}%");
            })
            ->when($params['ip'] ?? null, function ($query, $ip) {
                $query->where('ip', 'like', "%{$ip}%");
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

    public function create(array $data)
    {
        $admin = new AdminAction();

        foreach ($data as $key => $value) {
            if (in_array($key, self::FIELD)) {
                $admin->$key = $value;
            }
        }

        $admin->save();
    }
}
