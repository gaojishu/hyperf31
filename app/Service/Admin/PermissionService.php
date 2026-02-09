<?php

declare(strict_types=1);

namespace App\Service\Admin;

use App\Enum\Admin\PermissionTypeEnum;
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
        $permission->type = PermissionTypeEnum::tryFrom($data['type']);
        $permission->level = 1;
        $permission->save();

        $p = null;
        if ($data['parent_id'] ?? null) {
            $p = Permission::where('parent_id', $data['parent_id'] ?? null)->first();
        }


        if ($p) {
            $permission->level = $p->level + 1;
            $permission->key = $p->key . '-' . $permission->id;
        } else {
            $permission->level = 1;
            $permission->key = $permission->id;
        }
        $permission->save();
    }

    public function update(array $data): void
    {
        $permission = Permission::where('id', $data['id'] ?? null)->first();
        if (! $permission) {
            throw new BusinessException('权限不存在');
        }
        unset($data['id']);
        foreach ($data as $key => $val) {
            $permission->$key = $val;
        }
        $permission->type = PermissionTypeEnum::tryFrom($data['type']);

        $p = null;
        if ($data['parent_id'] ?? null) {
            $p = Permission::where('parent_id', $data['parent_id'] ?? null)->first();
        }

        if ($p) {
            $permission->level = $p->level + 1;
            $permission->key = $p->key . '-' . $permission->id;
        } else {
            $permission->level = 1;
            $permission->key = $permission->id;
        }
        $permission->save();
    }

    public function delete(int $id): bool
    {
        $permission = Permission::where('id', $id)->first();

        if (!$permission) {
            return true;
        }
        $children = Permission::where('parent_id', $id)->first();
        if ($children) {
            throw new BusinessException('权限下有子权限，不能删除');
        }

        return $permission->delete();
    }

    public function findByCode(string $code): ?Permission
    {
        $permission = Permission::where('code', $code)->first();
        return $permission;
    }
}
