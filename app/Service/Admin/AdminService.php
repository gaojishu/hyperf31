<?php

declare(strict_types=1);

namespace App\Service\Admin;

use App\Enum\Admin\SortEnum;
use App\Exception\BusinessException;
use App\Model\Admin\Admin;

class AdminService
{

    public function page(array $data)
    {
        // 1. 预处理参数
        $params = $data['params'] ?? [];
        $sort = $data['sort'] ?? [];

        $query = Admin::query();

        // 2. 分页大小限制 (1-20 范围)
        $pagesize = (int)($params['page_size'] ?? 10);
        $pagesize = max(1, min($pagesize, 20));

        $query->when($params['id'] ?? null, function ($query, $id) {
            $query->where('id', $id);
        })
            // 筛选：用户名 模糊匹配 (修正了你代码中的 filter 错误)
            ->when($params['username'] ?? null, function ($query, $username) {
                $query->where('username', 'like', "%{$username}%");
            })
            // 筛选：昵称 模糊匹配
            ->when($params['nickname'] ?? null, function ($query, $nickname) {
                $query->where('nickname', 'like', "%{$nickname}%");
            })
            ->when($params['mobile'] ?? null, function ($query, $mobile) {
                $query->where('mobile', $mobile);
            })
            ->when($params['email'] ?? null, function ($query, $email) {
                $query->where('email', $email);
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
        $admin = new Admin();
        foreach ($data as $key => $value) {
            $admin->$key = $value;
        }
        return $admin->save();
    }

    public function update(array $data)
    {
        $admin = Admin::where('id', $data['id'])->first();
        if (! $admin) {
            throw new BusinessException(500, '数据不存在');
        }
        unset($data['id']);
        foreach ($data as $key => $value) {
            $admin->$key = $value;
        }
        return $admin->save();
    }

    public function findById(?int $admin_id): ?Admin
    {
        $admin = Admin::where('id', $admin_id)->first();
        return $admin;
    }

    public function findPermissionByadmin_id(?int $admin_id)
    {
        $admin = Admin::find($admin_id);
        if (! $admin) {
            return null;
        }
        return $admin->permission;
    }
}
