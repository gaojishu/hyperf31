<?php

declare(strict_types=1);

namespace App\Service\Admin;

use App\Enum\Admin\SortEnum;
use App\Exception\BusinessException;
use App\Model\Admin\Admin;
use App\Model\Admin\Permission;
use Hyperf\DbConnection\Db;

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
        Db::transaction(function () use ($data) {

            if (isset($data['password'])) {
                // 使用 BCRYPT 算法进行加密
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            $admin = new Admin();
            foreach ($data as $key => $value) {
                $admin->$key = $value;
            }

            $admin->save();

            //权限绑定
            $permission_key = $data['permission_key'] ?? null;
            if ($permission_key) {
                $permission_ids = [];
                foreach ($permission_key as $val) {
                    $key = explode('-', $val);
                    foreach ($key as $id) {
                        $permission_ids[] = $id;
                    }
                }
                $permission_ids = array_unique($permission_ids);
                $admin->permission()->attach($permission_ids);
            }
        });
    }

    public function update(array $data)
    {
        Db::transaction(function () use ($data) {

            if (isset($data['password'])) {
                // 使用 BCRYPT 算法进行加密
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            $admin = Admin::where('id', $data['id'])->first();
            if (! $admin) {
                throw new BusinessException('数据不存在');
            }
            unset($data['id']);
            foreach ($data as $key => $value) {
                $admin->$key = $value;
            }

            $admin->save();

            //权限绑定
            $permission_key = $data['permission_key'] ?? null;
            if ($permission_key) {
                $permission_ids = [];
                foreach ($permission_key as $val) {
                    $key = explode('-', $val);
                    foreach ($key as $id) {
                        $permission_ids[] = $id;
                    }
                }
                $permission_ids = array_unique($permission_ids);
                $admin->permission()->detach();
                $admin->permission()->attach($permission_ids);
            }
        });
    }

    public function findById(?int $admin_id): ?Admin
    {
        $admin = Admin::where('id', $admin_id)->first();
        return $admin;
    }

    public function findPermissionByadminId(?int $admin_id)
    {
        $admin = Admin::find($admin_id);
        if (! $admin) {
            return null;
        }
        return $admin->permission;
    }

    public function findPermissionByadminIdAndCode(?int $admin_id, string $code)
    {
        //查询当前用户 关联的权限，查询条件 adminid  and code
        $admin = Admin::where('id', $admin_id)
            ->whereHas('permission', function ($query) use ($code) {
                $query->where('code', $code);
            })->first();

        return $admin;
    }
}
