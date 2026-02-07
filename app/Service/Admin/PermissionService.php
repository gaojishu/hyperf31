<?php

declare(strict_types=1);

namespace App\Service\Admin;

use App\Exception\BusinessException;
use App\Model\Admin\Permission;

class PermissionService
{
    public function findAll()
    {
        return Permission::query()->orderBy('sort', 'asc')->get();
    }

    public function create(array $data): void
    {
        $permission = new Permission();
        foreach ($data as $key => $val) {
            $permission->$key = $val;
        }
        $permission->save();
        $p = Permission::where('parent_id', $data['parent_id'] ?? null)->first();
        if ($p) {
            $permission->key = $p->key . '-' . $permission->id;
        } else {
            $permission->key = $permission->id;
        }
        $permission->save();
    }

    public function update(array $data): void
    {
        $permission = Permission::where('id', $data['id'] ?? null)->first();
        if (! $permission) {
            throw new BusinessException(500, '权限不存在');
        }
        unset($data['id']);
        foreach ($data as $key => $val) {
            $permission->$key = $val;
        }
        $p = Permission::where('parent_id', $data['parent_id'])->first();
        if ($p) {
            $permission->key = $p->key . '-' . $permission->id;
        } else {
            $permission->key = $permission->id;
        }
        $permission->save();
    }

    public function delete(int $id): bool
    {
        $permission = Permission::where('id', $id)->first();

        if(!$permission){
            return true;
        }
        $children = Permission::where('parent_id', $id)->first();
        if ($children) {
            throw new BusinessException(500, '权限下有子权限，不能删除');
        }

        return $permission->delete();
    }
}
